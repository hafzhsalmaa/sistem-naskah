<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Naskah;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LayouterController extends Controller
{
    /**
     * Display the layouter dashboard.
     */
    public function index(): View
    {
        $layouter = auth()->user()->layouter;
        $naskahList = $this->getLayouterNaskah($layouter?->getKey());
        [$chartStatuses, $chartTotal] = $this->buildChartStatuses($naskahList);
        $latestNaskahRows = $naskahList->take(3)->values();
        $metrics = $this->buildMetrics($naskahList, $layouter?->getKey());
        $announcementItems = $this->buildAnnouncementItems();

        return view('layouter.dashboard', compact(
            'chartStatuses',
            'chartTotal',
            'latestNaskahRows',
            'metrics',
            'announcementItems'
        ));
    }

    private function getLayouterNaskah(?int $idLayouter): Collection
    {
        if (! $idLayouter) {
            return collect();
        }

        return Naskah::query()
            ->where('id_layouter', $idLayouter)
            ->orderByDesc('tanggal_submit')
            ->get()
            ->map(function (Naskah $naskah): Naskah {
                $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);

                return $naskah;
            });
    }

    private function buildChartStatuses(Collection $naskahList): array
    {
        $statusOrder = [
            'Proses Layout',
            'Revisi Layout',
            'Selesai Layout',
            'Menunggu Jadwal Penerbitan',
            'Terjadwal Terbit',
        ];

        $statusColorMap = [
            'Proses Layout' => '#a5b4fc',
            'Revisi Layout' => '#d8b4fe',
            'Selesai Layout' => '#824DD2',
            'Menunggu Jadwal Penerbitan' => '#ddd6fe',
            'Terjadwal Terbit' => '#bfa8ff',
        ];

        $groupedStatus = $naskahList
            ->groupBy(fn ($naskah) => $naskah->status_tampilan ?? $naskah->status_naskah)
            ->map(fn ($items) => $items->count());

        $orderedStatuses = collect($statusOrder)
            ->filter(fn ($status) => $groupedStatus->has($status))
            ->values();

        $remainingStatuses = $groupedStatus->keys()
            ->reject(fn ($status) => $orderedStatuses->contains($status))
            ->values();

        $chartStatuses = $orderedStatuses
            ->concat($remainingStatuses)
            ->map(function ($status) use ($groupedStatus, $statusColorMap) {
                return [
                    'label' => $status,
                    'value' => (int) $groupedStatus->get($status, 0),
                    'color' => $statusColorMap[$status] ?? '#cbd5e1',
                ];
            })
            ->values();

        $chartTotal = $chartStatuses->sum('value');

        return [
            $chartStatuses->map(function (array $item) use ($chartTotal) {
                $item['percentage'] = $chartTotal > 0
                    ? round(($item['value'] / $chartTotal) * 100, 1)
                    : 0;

                return $item;
            }),
            $chartTotal,
        ];
    }

    private function buildMetrics(Collection $naskahList, ?int $idLayouter): array
    {
        $layoutStatuses = ['Menunggu Layout', 'Proses Layout', 'Revisi Layout', 'Selesai Layout', 'Menunggu Jadwal Penerbitan', 'Terjadwal Terbit'];
        $layoutDurations = $this->getLayoutDurations($idLayouter);

        return [
            'layouterActiveCount' => User::query()->where('role', 'layouter')->count(),
            'naskahLayoutSayaCount' => $naskahList->whereIn('status_naskah', $layoutStatuses)->count(),
            'averageLayoutDuration' => $layoutDurations->isNotEmpty()
                ? round($layoutDurations->avg(), 1).' Hari'
                : '-',
            'revisiLayoutCount' => $naskahList->where('status_naskah', 'Revisi Layout')->count(),
        ];
    }

    private function resolveDisplayStatus(Naskah $naskah): string
    {
        if ($naskah->status_naskah === 'Menunggu Layout' && filled($naskah->id_layouter)) {
            return 'Proses Layout';
        }

        return $naskah->status_naskah;
    }

    private function getLayoutDurations(?int $idLayouter): Collection
    {
        if (! $idLayouter) {
            return collect();
        }

        return Layout::query()
            ->where('id_layouter', $idLayouter)
            ->whereNotNull('tanggal_layout')
            ->with('naskah:id_naskah,tanggal_submit')
            ->get()
            ->map(function (Layout $layout) {
                $tanggalSubmit = $layout->naskah?->tanggal_submit;

                if (! $tanggalSubmit instanceof Carbon || ! $layout->tanggal_layout instanceof Carbon) {
                    return null;
                }

                return max(0, $tanggalSubmit->diffInHours($layout->tanggal_layout) / 24);
            })
            ->filter(fn ($value) => $value !== null)
            ->values();
    }

    private function buildAnnouncementItems(): Collection
    {
        return collect([
            [
                'title' => 'Pastikan file layout final sudah sesuai naskah terbaru sebelum diunggah ke sistem.',
            ],
            [
                'title' => 'Selesaikan proses layout setelah file final tersedia agar status naskah sinkron ke admin dan penulis.',
            ],
        ]);
    }
}
