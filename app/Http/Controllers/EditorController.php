<?php

namespace App\Http\Controllers;

use App\Models\Naskah;
use App\Models\Revisi;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EditorController extends Controller
{
    /**
     * Display the editor dashboard.
     */
    public function index(): View
    {
        $editor = auth()->user()->editor;
        $naskahList = $this->getEditorNaskah($editor?->getKey());
        [$chartStatuses, $chartTotal] = $this->buildChartStatuses($naskahList);
        $latestNaskahRows = $naskahList->take(3)->values();
        $metrics = $this->buildMetrics($naskahList, $editor?->getKey());
        $announcementItems = $this->buildAnnouncementItems();

        return view('editor.dashboard', compact(
            'chartStatuses',
            'chartTotal',
            'latestNaskahRows',
            'metrics',
            'announcementItems'
        ));
    }

    private function getEditorNaskah(?int $idEditor): Collection
    {
        if (! $idEditor) {
            return collect();
        }

        return Naskah::query()
            ->where('id_editor', $idEditor)
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
            'Pending Review',
            'Perbaikan Dikirim',
            'Revisi',
            'Menunggu Layout',
            'Proses Layout',
            'Revisi Layout',
            'Selesai Layout',
            'Menunggu Jadwal Penerbitan',
            'Terjadwal Terbit',
        ];

        $statusColorMap = [
            'Pending Review' => '#93c5fd',
            'Perbaikan Dikirim' => '#7dd3fc',
            'Revisi' => '#a5b4fc',
            'Menunggu Layout' => '#488CBD',
            'Proses Layout' => '#67e8f9',
            'Revisi Layout' => '#c4b5fd',
            'Selesai Layout' => '#60a5fa',
            'Menunggu Jadwal Penerbitan' => '#ddd6fe',
            'Terjadwal Terbit' => '#38bdf8',
        ];

        $groupedStatus = $naskahList
            ->groupBy(fn ($naskah) => $this->normalizeEditorDashboardStatus($naskah->status_tampilan ?? $naskah->status_naskah))
            ->map(fn ($items) => $items->count());

        $orderedStatuses = collect($statusOrder)
            ->filter(fn ($status) => $groupedStatus->has($this->normalizeEditorDashboardStatus($status)))
            ->values();

        $remainingStatuses = $groupedStatus->keys()
            ->reject(fn ($status) => $orderedStatuses
                ->map(fn ($orderedStatus) => $this->normalizeEditorDashboardStatus($orderedStatus))
                ->contains($status))
            ->values();

        $chartStatuses = $orderedStatuses
            ->concat($remainingStatuses)
            ->map(function ($status) use ($groupedStatus, $statusColorMap) {
                $normalizedStatus = $this->normalizeEditorDashboardStatus($status);
                $label = $this->resolveEditorDashboardStatusLabel($status);

                return [
                    'label' => $label,
                    'value' => (int) $groupedStatus->get($normalizedStatus, 0),
                    'color' => $statusColorMap[$label] ?? '#cbd5e1',
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

    private function normalizeEditorDashboardStatus(?string $status): string
    {
        $normalized = strtolower(trim((string) $status));

        return preg_replace('/\s+/', ' ', $normalized) ?? '';
    }

    private function resolveEditorDashboardStatusLabel(?string $status): string
    {
        $normalized = $this->normalizeEditorDashboardStatus($status);

        return [
            'pending review' => 'Pending Review',
            'perbaikan dikirim' => 'Perbaikan Dikirim',
            'revisi' => 'Revisi',
            'menunggu layout' => 'Menunggu Layout',
            'proses layout' => 'Proses Layout',
            'revisi layout' => 'Revisi Layout',
            'selesai layout' => 'Selesai Layout',
            'menunggu jadwal penerbitan' => 'Menunggu Jadwal Penerbitan',
            'terjadwal terbit' => 'Terjadwal Terbit',
        ][$normalized] ?? trim((string) $status);
    }

    private function buildMetrics(Collection $naskahList, ?int $idEditor): array
    {
        $reviewStatuses = ['Pending Review', 'Perbaikan Dikirim', 'Revisi', 'Diterima'];
        $layoutStatuses = ['Menunggu Layout', 'Proses Layout', 'Revisi Layout', 'Selesai Layout', 'Menunggu Jadwal Penerbitan', 'Terjadwal Terbit'];

        $reviewDurations = $this->getReviewDurations($idEditor);

        return [
            'editorActiveCount' => User::query()->where('role', 'editor')->count(),
            'naskahReviewSayaCount' => $naskahList->whereIn('status_naskah', $reviewStatuses)->count(),
            'averageReviewDuration' => $reviewDurations->isNotEmpty()
                ? round($reviewDurations->avg(), 1).' Hari'
                : '-',
            'naskahRevisiCount' => $naskahList->whereIn('status_naskah', ['Revisi', 'Perbaikan Dikirim'])->count(),
            'naskahSelesaiCount' => $naskahList->where('status_naskah', 'Diterima')->count(),
            'naskahSiapLayoutCount' => $naskahList->whereIn('status_naskah', $layoutStatuses)->count(),
        ];
    }

    private function resolveDisplayStatus(Naskah $naskah): string
    {
        if ($naskah->status_naskah === 'Diterima') {
            return 'Menunggu Layout';
        }

        if ($naskah->status_naskah === 'Menunggu Layout' && filled($naskah->id_layouter)) {
            return 'Proses Layout';
        }

        return $naskah->status_naskah;
    }

    private function getReviewDurations(?int $idEditor): Collection
    {
        if (! $idEditor) {
            return collect();
        }

        return Revisi::query()
            ->where('id_editor', $idEditor)
            ->whereNotNull('tanggal_revisi')
            ->with('naskah:id_naskah,tanggal_submit')
            ->get()
            ->map(function (Revisi $revisi) {
                $tanggalSubmit = $revisi->naskah?->tanggal_submit;

                if (! $tanggalSubmit instanceof Carbon || ! $revisi->tanggal_revisi instanceof Carbon) {
                    return null;
                }

                return max(0, $tanggalSubmit->diffInHours($revisi->tanggal_revisi) / 24);
            })
            ->filter(fn ($value) => $value !== null)
            ->values();
    }

    private function buildAnnouncementItems(): Collection
    {
        return collect([
            [
                'title' => 'Pastikan pemeriksaan kurikulum, silabus, RPP, dan unsur SARA sudah lengkap sebelum mengubah status naskah.',
            ],
            [
                'title' => 'Gunakan catatan revisi yang jelas agar penulis dapat mengirim perbaikan dengan lebih cepat dan akurat.',
            ],
        ]);
    }
}
