<?php

namespace App\Http\Controllers;

use App\Models\Naskah;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PenulisController extends Controller
{
    /**
     * Display the penulis dashboard.
     */
    public function index(): View
    {
        $penulis = auth()->user()->penulis;
        $naskahList = $this->getPenulisNaskah($penulis?->getKey());
        [$chartStatuses, $chartTotal] = $this->buildChartStatuses($naskahList);
        $latestNaskahRows = $naskahList->take(3)->values();
        $deadlineRevisiItems = collect();
        $announcementItems = $this->buildAnnouncementItems();
        $metrics = $this->buildMetrics($naskahList);

        return view('penulis.dashboard', compact(
            'chartStatuses',
            'chartTotal',
            'latestNaskahRows',
            'deadlineRevisiItems',
            'announcementItems',
            'metrics'
        ));
    }

    private function getPenulisNaskah(?int $idPenulis): Collection
    {
        if (! $idPenulis) {
            return collect();
        }

        return Naskah::query()
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where('id_penulis', $idPenulis)
            ->select('naskah.*', 'jadwal_penerbitan.tanggal_cetak')
            ->orderByDesc('naskah.tanggal_submit')
            ->get()
            ->map(function (Naskah $naskah): Naskah {
                $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);

                return $naskah;
            });
    }

    private function buildChartStatuses(Collection $naskahList): array
    {
        $statusOrder = [
            'Pending Review',
            'Perbaikan Dikirim',
            'Revisi',
            'Menunggu Layout',
            'Proses Layout',
            'Revisi Layout',
            'Menunggu Jadwal Penerbitan',
            'Terbit',
        ];

        $statusColorMap = [
            'Pending Review' => '#F6B704',
            'Perbaikan Dikirim' => '#4DB6E8',
            'Revisi' => '#FF8916',
            'Menunggu Layout' => '#FFE479',
            'Proses Layout' => '#F4A300',
            'Revisi Layout' => '#FFB347',
            'Menunggu Jadwal Penerbitan' => '#C29B2B',
            'Terbit' => '#22c55e',
        ];

        $groupedStatus = $naskahList
            ->groupBy(fn ($naskah) => $this->resolveChartStatus($naskah->status_tampilan ?? $naskah->status_naskah))
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
                    'color' => $statusColorMap[$status] ?? '#94a3b8',
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

    private function resolveChartStatus(string $status): string
    {
        if (str_starts_with($status, 'Terbit')) {
            return 'Terbit';
        }

        return $status;
    }

    private function buildAnnouncementItems(): Collection
    {
        return collect([
            [
                'title' => 'Pastikan naskah sudah sesuai kurikulum dan menyertakan silabus serta RPP sebagai acuan penyelarasan materi.',
            ],
            [
                'title' => 'Pantau status naskah secara berkala agar revisi dari editor dapat segera ditindaklanjuti.',
            ],
        ]);
    }

    private function buildMetrics(Collection $naskahList): array
    {
        return [
            'totalNaskah' => $naskahList->count(),
            'pendingReviewCount' => $naskahList->where('status_naskah', 'Pending Review')->count(),
            'revisiCount' => $naskahList->whereIn('status_naskah', ['Revisi', 'Perbaikan Dikirim'])->count(),
            'menungguLayoutCount' => $naskahList->where('status_tampilan', 'Menunggu Layout')->count(),
            'menungguJadwalCount' => $naskahList->where('status_tampilan', 'Menunggu Jadwal Penerbitan')->count(),
            'naskahMingguIniCount' => $naskahList->filter(function ($naskah) {
                return $naskah->tanggal_submit instanceof Carbon
                    ? $naskah->tanggal_submit->between(Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek())
                    : false;
            })->count(),
        ];
    }

    private function resolveDisplayStatus(Naskah $naskah): string
    {
        if ($naskah->tanggal_cetak) {
            return 'Terbit '.Carbon::parse($naskah->tanggal_cetak)->translatedFormat('F Y');
        }

        if ($naskah->status_naskah === 'Diterima') {
            return 'Menunggu Layout';
        }

        if ($naskah->status_naskah === 'Menunggu Layout' && filled($naskah->id_layouter)) {
            return 'Proses Layout';
        }

        if ($naskah->status_naskah === 'Selesai Layout') {
            return 'Menunggu Jadwal Penerbitan';
        }

        return $naskah->status_naskah;
    }
}
