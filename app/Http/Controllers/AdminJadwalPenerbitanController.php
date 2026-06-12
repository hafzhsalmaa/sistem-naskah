<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\NaskahWorkflowEmailNotification;
use App\Notifications\WorkflowNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminJadwalPenerbitanController extends Controller
{
    public function index(): View
    {
        $latestLayoutSubquery = DB::table('layout as ly')
            ->select('ly.id_naskah', DB::raw('MAX(ly.id_layout) as latest_id_layout'))
            ->groupBy('ly.id_naskah');

        $naskahList = DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users as penulis_user', 'penulis_user.id_user', '=', 'penulis.id_user')
            ->leftJoin('editor', 'editor.id_editor', '=', 'naskah.id_editor')
            ->leftJoin('users as editor_user', 'editor_user.id_user', '=', 'editor.id_user')
            ->leftJoin('layouter', 'layouter.id_layouter', '=', 'naskah.id_layouter')
            ->leftJoin('users as layouter_user', 'layouter_user.id_user', '=', 'layouter.id_user')
            ->leftJoinSub($latestLayoutSubquery, 'latest_layout', function ($join): void {
                $join->on('latest_layout.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('layout', 'layout.id_layout', '=', 'latest_layout.latest_id_layout')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where('naskah.status_naskah', 'Selesai Layout')
            ->whereNull('jadwal_penerbitan.id_jadwal')
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.kelas',
                'naskah.bidang_keahlian as jenjang',
                'penulis_user.username as nama_penulis',
                'editor_user.username as nama_editor',
                'layouter_user.username as nama_layouter',
                'layout.file_layout',
                'layout.nama_file_layout_asli'
            )
            ->orderByDesc('naskah.id_naskah')
            ->get();

        return view('admin.jadwal-penerbitan.index', compact('naskahList'));
    }

    public function store(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2024', 'max:2100'],
        ]);

        $naskah = DB::table('naskah')
            ->where('id_naskah', $id)
            ->where('status_naskah', 'Selesai Layout')
            ->firstOrFail();

        $bulan = (int) $validated['bulan'];
        $tahun = (int) $validated['tahun'];

        $tanggalTerbit = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();

        DB::table('jadwal_penerbitan')->updateOrInsert(
            ['id_naskah' => $naskah->id_naskah],
            [
                'tanggal_cetak' => $tanggalTerbit,
                'catatan_admin' => 'Terbit '.$tanggalTerbit->translatedFormat('F Y'),
            ]
        );

        $penulisUser = User::query()
            ->whereHas('penulis', fn ($query) => $query->where('id_penulis', $naskah->id_penulis))
            ->first();

        if ($penulisUser) {
            $penulisUser->notify(new WorkflowNotification(
                'Jadwal terbit naskah telah ditentukan.',
                'Admin menetapkan jadwal terbit '.$tanggalTerbit->translatedFormat('F Y').' untuk naskah Anda.',
                route('penulis.riwayat-naskah.index', [], false)
            ));
        }

        try {
            if ($penulisUser && filled($penulisUser->email)) {
                $tanggalTerbitLabel = $tanggalTerbit->translatedFormat('d F Y');
                $statusTampilan = 'Terbit '.$tanggalTerbit->translatedFormat('F Y');

                $penulisUser->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Jadwal Terbit Naskah - '.$naskah->judul,
                    greeting: 'Halo '.($penulisUser->username ?? 'Penulis').',',
                    opening: 'Jadwal penerbitan untuk naskah Anda yang berjudul "'.$naskah->judul.'" telah ditentukan melalui sistem PubliSync.',
                    details: [
                        'Kode Naskah' => $naskah->kode_naskah ?? '#'.$naskah->id_naskah,
                        'Judul Naskah' => $naskah->judul,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Tanggal Terbit' => $tanggalTerbitLabel,
                        'Status' => $statusTampilan,
                    ],
                    bodyMessage: 'Mohon memperhatikan informasi jadwal penerbitan tersebut. Jika terdapat perubahan jadwal, informasi akan diperbarui melalui sistem PubliSync.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Riwayat Naskah',
                    actionUrl: route('penulis.riwayat-naskah.index'),
                ));
            }
        } catch (Throwable $emailException) {
            Log::warning('Gagal mengirim email workflow jadwal terbit.', [
                'id_naskah' => $naskah->id_naskah,
                'id_penulis' => $naskah->id_penulis,
                'error' => $emailException->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.jadwal-penerbitan.index')
            ->with('status', 'Jadwal penerbitan berhasil disimpan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'tahun' => ['required', 'integer', 'min:2024', 'max:2100'],
            'catatan_admin' => ['nullable', 'string', 'max:255'],
        ]);

        $naskah = DB::table('naskah')
            ->join('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where('naskah.id_naskah', $id)
            ->select(
                'naskah.*',
                'jadwal_penerbitan.id_jadwal',
                'jadwal_penerbitan.tanggal_cetak as tanggal_cetak_lama',
                'jadwal_penerbitan.catatan_admin as catatan_admin_lama'
            )
            ->firstOrFail();

        $bulan = (int) $validated['bulan'];
        $tahun = (int) $validated['tahun'];
        $tanggalTerbit = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
        $statusTampilan = 'Terbit '.$tanggalTerbit->translatedFormat('F Y');
        $catatanAdmin = filled($validated['catatan_admin'] ?? null)
            ? $validated['catatan_admin']
            : $statusTampilan;

        DB::table('jadwal_penerbitan')
            ->where('id_jadwal', $naskah->id_jadwal)
            ->update([
                'tanggal_cetak' => $tanggalTerbit,
                'catatan_admin' => $catatanAdmin,
            ]);

        $penulisUser = User::query()
            ->whereHas('penulis', fn ($query) => $query->where('id_penulis', $naskah->id_penulis))
            ->first();

        if ($penulisUser) {
            $penulisUser->notify(new WorkflowNotification(
                'Jadwal penerbitan naskah telah diperbarui.',
                'Admin memperbarui jadwal terbit '.$tanggalTerbit->translatedFormat('F Y').' untuk naskah Anda.',
                route('penulis.riwayat-naskah.index', [], false)
            ));
        }

        try {
            if ($penulisUser && filled($penulisUser->email)) {
                $penulisUser->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Perubahan Jadwal Terbit Naskah - '.$naskah->judul,
                    greeting: 'Halo '.($penulisUser->username ?? 'Penulis').',',
                    opening: 'Jadwal penerbitan untuk naskah Anda yang berjudul "'.$naskah->judul.'" telah diperbarui melalui sistem PubliSync.',
                    details: [
                        'Kode Naskah' => $naskah->kode_naskah ?? '#'.$naskah->id_naskah,
                        'Judul Naskah' => $naskah->judul,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Tanggal Terbit Baru' => $tanggalTerbit->translatedFormat('d F Y'),
                        'Status' => $statusTampilan,
                    ],
                    bodyMessage: 'Mohon memperhatikan perubahan jadwal penerbitan tersebut. Informasi terbaru juga dapat dilihat melalui sistem PubliSync.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Riwayat Naskah',
                    actionUrl: route('penulis.riwayat-naskah.index'),
                ));
            }
        } catch (Throwable $emailException) {
            Log::warning('Gagal mengirim email perubahan jadwal terbit.', [
                'id_naskah' => $naskah->id_naskah,
                'id_penulis' => $naskah->id_penulis,
                'error' => $emailException->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.riwayat-naskah.index')
            ->with('status', 'Jadwal penerbitan berhasil diperbarui.');
    }

    public function riwayat(): View
    {
        $latestLayoutSubquery = DB::table('layout as ly')
            ->select('ly.id_naskah', DB::raw('MAX(ly.id_layout) as latest_id_layout'))
            ->groupBy('ly.id_naskah');

        $riwayatList = DB::table('jadwal_penerbitan')
            ->join('naskah', 'naskah.id_naskah', '=', 'jadwal_penerbitan.id_naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users as penulis_user', 'penulis_user.id_user', '=', 'penulis.id_user')
            ->leftJoin('editor', 'editor.id_editor', '=', 'naskah.id_editor')
            ->leftJoin('users as editor_user', 'editor_user.id_user', '=', 'editor.id_user')
            ->leftJoin('layouter', 'layouter.id_layouter', '=', 'naskah.id_layouter')
            ->leftJoin('users as layouter_user', 'layouter_user.id_user', '=', 'layouter.id_user')
            ->leftJoinSub($latestLayoutSubquery, 'latest_layout', function ($join): void {
                $join->on('latest_layout.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('layout', 'layout.id_layout', '=', 'latest_layout.latest_id_layout')
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.kelas',
                'naskah.bidang_keahlian as jenjang',
                'penulis_user.username as nama_penulis',
                'editor_user.username as nama_editor',
                'layouter_user.username as nama_layouter',
                'jadwal_penerbitan.tanggal_cetak',
                'jadwal_penerbitan.catatan_admin',
                'layout.file_layout',
                'layout.nama_file_layout_asli'
            )
            ->orderByDesc('jadwal_penerbitan.tanggal_cetak')
            ->get();

        return view('admin.riwayat-naskah.index', compact('riwayatList'));
    }

    public function download(int $id): StreamedResponse
    {
        $layout = DB::table('layout')
            ->join('naskah', 'naskah.id_naskah', '=', 'layout.id_naskah')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where('naskah.id_naskah', $id)
            ->where(function ($query): void {
                $query->where('naskah.status_naskah', 'Selesai Layout')
                    ->orWhereNotNull('jadwal_penerbitan.id_jadwal');
            })
            ->orderByDesc('layout.id_layout')
            ->select('layout.file_layout', 'layout.nama_file_layout_asli')
            ->firstOrFail();

        $downloadName = $layout->nama_file_layout_asli ?: basename($layout->file_layout);

        return Storage::download($layout->file_layout, $downloadName);
    }

    public function preview(int $id): BinaryFileResponse
    {
        $layout = DB::table('layout')
            ->join('naskah', 'naskah.id_naskah', '=', 'layout.id_naskah')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where('naskah.id_naskah', $id)
            ->where(function ($query): void {
                $query->where('naskah.status_naskah', 'Selesai Layout')
                    ->orWhereNotNull('jadwal_penerbitan.id_jadwal');
            })
            ->orderByDesc('layout.id_layout')
            ->select('layout.file_layout')
            ->firstOrFail();

        return $this->previewPdfFile($layout->file_layout);
    }

    private function previewPdfFile(string $path): BinaryFileResponse
    {
        abort_unless(strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf', 404);
        abort_unless(Storage::exists($path), 404);

        return response()->file(Storage::path($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
