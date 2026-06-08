<?php

namespace App\Http\Controllers;

use App\Models\Naskah;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $jenjangOrder = ['SD', 'SMP', 'SMA', 'MI', 'MTS', 'MA', 'SMK'];

        $reviewStatuses = ['Pending Review', 'Perbaikan Dikirim', 'Revisi'];
        $layoutStatuses = ['Diterima', 'Menunggu Layout', 'Proses Layout', 'Revisi Layout'];

        $reviewCounts = DB::table('naskah')
            ->whereIn('naskah.status_naskah', $reviewStatuses)
            ->selectRaw('naskah.bidang_keahlian as jenjang, COUNT(*) as total')
            ->groupBy('naskah.bidang_keahlian')
            ->pluck('total', 'jenjang')
            ->all();

        $layoutCounts = DB::table('naskah')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where(function ($query) use ($layoutStatuses): void {
                $query->whereIn('naskah.status_naskah', $layoutStatuses)
                    ->orWhere(function ($subquery): void {
                        $subquery->where('naskah.status_naskah', 'Selesai Layout')
                            ->whereNull('jadwal_penerbitan.tanggal_cetak');
                    });
            })
            ->selectRaw('naskah.bidang_keahlian as jenjang, COUNT(*) as total')
            ->groupBy('naskah.bidang_keahlian')
            ->pluck('total', 'jenjang')
            ->all();

        $penulisCounts = DB::table('naskah')
            ->selectRaw('naskah.bidang_keahlian as jenjang, COUNT(DISTINCT naskah.id_penulis) as total')
            ->groupBy('naskah.bidang_keahlian')
            ->pluck('total', 'jenjang')
            ->all();

        $reviewChart = $this->buildChartDataset($jenjangOrder, $reviewCounts, [
            '#0f4c81',
            '#2a6db0',
            '#4f8fc9',
            '#86b6df',
            '#cfe1f2',
            '#e7f0f8',
            '#dbeafe',
        ]);

        $layoutChart = $this->buildChartDataset($jenjangOrder, $layoutCounts, [
            '#32145f',
            '#5b2ea9',
            '#7e4cd0',
            '#a67ae3',
            '#c7a9ee',
            '#e2d6f7',
            '#ede9fe',
        ]);

        $penulisChart = $this->buildChartDataset($jenjangOrder, $penulisCounts, [
            '#f4a300',
            '#ff8f1f',
            '#ffb347',
            '#ffd260',
            '#c79b27',
            '#ffe39a',
            '#fff3bf',
        ]);

        $editorActiveCount = User::query()
            ->where('role', 'editor')
            ->count();

        $layouterActiveCount = User::query()
            ->where('role', 'layouter')
            ->count();

        $penulisTerdaftarCount = User::query()
            ->where('role', 'penulis')
            ->count();

        $naskahReviewCount = Naskah::query()
            ->where('status_naskah', 'Pending Review')
            ->count();

        $naskahLayoutCount = DB::table('naskah')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where(function ($query) use ($layoutStatuses): void {
                $query->whereIn('naskah.status_naskah', $layoutStatuses)
                    ->orWhere(function ($subquery): void {
                        $subquery->where('naskah.status_naskah', 'Selesai Layout')
                            ->whereNull('jadwal_penerbitan.tanggal_cetak');
                    });
            })
            ->count();

        $naskahRevisiCount = Naskah::query()
            ->whereIn('status_naskah', ['Revisi', 'Perbaikan Dikirim'])
            ->count();

        $naskahMingguIniCount = Naskah::query()
            ->whereBetween('tanggal_submit', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->count();

        $naskahPendingReviewCount = $naskahReviewCount;

        $naskahMenungguJadwalCount = DB::table('naskah')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where('naskah.status_naskah', 'Selesai Layout')
            ->whereNull('jadwal_penerbitan.tanggal_cetak')
            ->count();

        $latestNaskahRows = Naskah::query()
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.tanggal_submit',
                'naskah.status_naskah',
                'naskah.id_layouter',
                'jadwal_penerbitan.tanggal_cetak'
            )
            ->orderByDesc('naskah.tanggal_submit')
            ->limit(3)
            ->get()
            ->map(function (Naskah $naskah): Naskah {
                $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);

                return $naskah;
            });

        return view('admin.dashboard', compact(
            'reviewChart',
            'layoutChart',
            'penulisChart',
            'editorActiveCount',
            'layouterActiveCount',
            'penulisTerdaftarCount',
            'naskahReviewCount',
            'naskahLayoutCount',
            'naskahRevisiCount',
            'naskahMingguIniCount',
            'naskahPendingReviewCount',
            'naskahMenungguJadwalCount',
            'latestNaskahRows'
        ));
    }

    /**
     * @param  array<int, string>  $labels
     * @param  array<string, int>  $counts
     * @param  array<int, string>  $colors
     * @return array{items: array<int, array{label: string, value: int, percentage: float, color: string}>, total: int}
     */
    private function buildChartDataset(array $labels, array $counts, array $colors): array
    {
        $total = 0;

        foreach ($labels as $label) {
            $total += (int) ($counts[$label] ?? 0);
        }

        $items = [];

        foreach ($labels as $index => $label) {
            $value = (int) ($counts[$label] ?? 0);

            $items[] = [
                'label' => $label,
                'value' => $value,
                'percentage' => $total > 0 ? round(($value / $total) * 100, 1) : 0,
                'color' => $colors[$index] ?? '#cbd5e1',
            ];
        }

        return [
            'items' => $items,
            'total' => $total,
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
