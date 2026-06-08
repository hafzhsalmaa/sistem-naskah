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
use Illuminate\View\View;
use Throwable;

class AdminNaskahController extends Controller
{
    public function index(): View
    {
        $latestVersiSubquery = DB::table('versi_naskah as vn')
            ->select('vn.id_naskah', DB::raw('MAX(vn.no_versi) as latest_no_versi'))
            ->groupBy('vn.id_naskah');

        $naskahList = DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users', 'users.id_user', '=', 'penulis.id_user')
            ->leftJoin('editor', 'editor.id_editor', '=', 'naskah.id_editor')
            ->leftJoin('users as editor_user', 'editor_user.id_user', '=', 'editor.id_user')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->leftJoinSub($latestVersiSubquery, 'latest_versi', function ($join): void {
                $join->on('latest_versi.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('versi_naskah', function ($join): void {
                $join->on('versi_naskah.id_naskah', '=', 'naskah.id_naskah')
                    ->on('versi_naskah.no_versi', '=', 'latest_versi.latest_no_versi');
            })
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.kelas',
                'naskah.bidang_keahlian',
                'naskah.id_editor',
                'naskah.id_layouter',
                'naskah.status_naskah',
                'jadwal_penerbitan.tanggal_cetak',
                'naskah.kategori_mapel',
                'naskah.mata_pelajaran',
                'users.username as nama_penulis',
                'editor_user.username as nama_editor',
                'versi_naskah.file_path',
                'versi_naskah.nama_file_asli',
                'versi_naskah.tanggal_upload'
            )
            ->orderByDesc('versi_naskah.tanggal_upload')
            ->orderByDesc('naskah.id_naskah')
            ->get()
            ->map(function (object $naskah): object {
                $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);

                return $naskah;
            });

        $editorMap = [];

        $naskahAktif = $naskahList
            ->filter(fn (object $naskah): bool => blank($naskah->tanggal_cetak ?? null))
            ->values();

        $naskahBelumDikirim = $naskahAktif->whereNull('id_editor')->values();
        $naskahSudahDikirim = $naskahAktif->whereNotNull('id_editor')->values();

        $editorActiveCountSubquery = DB::table('naskah as active_naskah')
            ->select('active_naskah.id_editor', DB::raw('COUNT(*) as naskah_aktif_count'))
            ->whereNotNull('active_naskah.id_editor')
            ->where(function ($query): void {
                $query->whereIn('active_naskah.status_naskah', [
                    'Pending Review',
                    'Perbaikan Dikirim',
                    'Revisi',
                ])
                    ->orWhere(function ($subQuery): void {
                        $subQuery->where('active_naskah.status_naskah', 'Diterima')
                            ->whereNull('active_naskah.id_layouter');
                    });
            })
            ->whereNotExists(function ($query): void {
                $query->select(DB::raw(1))
                    ->from('jadwal_penerbitan')
                    ->whereColumn('jadwal_penerbitan.id_naskah', 'active_naskah.id_naskah')
                    ->whereNotNull('jadwal_penerbitan.tanggal_cetak');
            })
            ->groupBy('active_naskah.id_editor');

        foreach ($naskahBelumDikirim as $naskah) {
            $bidangKeahlianEditor = $this->resolveBidangKeahlianEditor($naskah->bidang_keahlian);

            $editorMap[$naskah->id_naskah] = DB::table('editor')
                ->join('users', 'users.id_user', '=', 'editor.id_user')
                ->leftJoinSub($editorActiveCountSubquery, 'editor_active_count', function ($join): void {
                    $join->on('editor_active_count.id_editor', '=', 'editor.id_editor');
                })
                ->where('editor.bidang_keahlian', $bidangKeahlianEditor)
                ->where('editor.kategori_mapel', $naskah->kategori_mapel)
                ->where('editor.mata_pelajaran', $naskah->mata_pelajaran)
                ->select(
                    'editor.id_editor',
                    'users.username',
                    'editor.bidang_keahlian',
                    'editor.kategori_mapel',
                    'editor.mata_pelajaran',
                    DB::raw('COALESCE(editor_active_count.naskah_aktif_count, 0) as naskah_aktif_count')
                )
                ->orderBy('users.username')
                ->get();
        }

        return view('admin.naskah.index', compact('naskahBelumDikirim', 'naskahSudahDikirim', 'editorMap'));
    }

    private function resolveDisplayStatus(object $naskah): string
    {
        if (filled($naskah->tanggal_cetak ?? null)) {
            return 'Terbit '.Carbon::parse($naskah->tanggal_cetak)->translatedFormat('F Y');
        }

        if ($naskah->status_naskah === 'Diterima') {
            return 'Menunggu Layout';
        }

        if ($naskah->status_naskah === 'Menunggu Layout' && filled($naskah->id_layouter)) {
            return 'Proses Layout';
        }

        if ($naskah->status_naskah === 'Selesai Layout' && blank($naskah->tanggal_cetak ?? null)) {
            return 'Menunggu Jadwal Penerbitan';
        }

        return $naskah->status_naskah;
    }

    private function resolveBidangKeahlianEditor(?string $jenjangNaskah): ?string
    {
        return match (strtoupper(trim((string) $jenjangNaskah))) {
            'SD', 'MI' => 'SD/MI',
            'SMP', 'MTS' => 'SMP/MTS',
            'SMA', 'MA', 'SMK' => 'SMA/MA/SMK',
            'SD/MI', 'SMP/MTS', 'SMA/MA/SMK' => $jenjangNaskah,
            default => $jenjangNaskah,
        };
    }

    public function assignEditor(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'id_editor' => ['required', 'integer'],
        ]);

        $naskah = DB::table('naskah')
            ->where('naskah.id_naskah', $id)
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.id_penulis',
                'naskah.judul',
                'naskah.kelas',
                'naskah.bidang_keahlian',
                'naskah.kategori_mapel',
                'naskah.mata_pelajaran',
                'naskah.tanggal_submit'
            )
            ->firstOrFail();

        $editorCocok = DB::table('editor')
            ->where('id_editor', $validated['id_editor'])
            ->where('bidang_keahlian', $this->resolveBidangKeahlianEditor($naskah->bidang_keahlian))
            ->where('kategori_mapel', $naskah->kategori_mapel)
            ->where('mata_pelajaran', $naskah->mata_pelajaran)
            ->exists();

        if (! $editorCocok) {
            return redirect()
                ->route('admin.naskah.index')
                ->withErrors(['id_editor' => 'Editor yang dipilih tidak sesuai dengan bidang naskah.']);
        }

        DB::table('naskah')
            ->where('id_naskah', $id)
            ->update([
                'id_editor' => $validated['id_editor'],
                'status_naskah' => 'Pending Review',
            ]);

        $editorUser = User::query()
            ->whereHas('editor', fn ($query) => $query->where('id_editor', $validated['id_editor']))
            ->first();

        $penulisUser = User::query()
            ->whereHas('penulis', fn ($query) => $query->where('id_penulis', $naskah->id_penulis))
            ->first();

        if ($editorUser) {
            $editorUser->notify(new WorkflowNotification(
                'Anda menerima tugas review naskah baru.',
                'Admin telah mengirim naskah untuk Anda review.',
                route('editor.naskah.show', $id)
            ));
        }

        try {
            $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
            $tanggalSubmit = $naskah->tanggal_submit
                ? \Illuminate\Support\Carbon::parse($naskah->tanggal_submit)->format('d M Y H:i')
                : '-';
            $namaPenulis = $penulisUser?->username ?? '-';
            $namaEditor = $editorUser?->username ?? '-';
            $statusTampilan = 'Pending Review';

            if ($penulisUser && filled($penulisUser->email)) {
                $penulisUser->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Naskah Dikirim ke Editor - '.$naskah->judul,
                    greeting: 'Halo '.$namaPenulis.',',
                    opening: 'Terima kasih telah mengirimkan naskah berjudul "'.$naskah->judul.'" melalui sistem PubliSync.',
                    details: [
                        'Kode Naskah' => $kodeNaskah,
                        'Judul Naskah' => $naskah->judul,
                        'Editor' => $namaEditor,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Status' => $statusTampilan,
                    ],
                    bodyMessage: 'Saat ini naskah Anda telah diteruskan kepada editor '.$namaEditor.' untuk proses review. Mohon menunggu proses peninjauan. Kami akan mengirimkan notifikasi lanjutan apabila terdapat catatan revisi atau pembaruan status naskah.',
                    closing: 'Terima kasih.',
                    actionText: 'Lihat Naskah',
                    actionUrl: route('penulis.naskah.show', $naskah->id_naskah),
                ));
            }

            if ($editorUser && filled($editorUser->email)) {
                $editorUser->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Naskah Baru untuk Direview - '.$naskah->judul,
                    greeting: 'Halo '.$namaEditor.',',
                    opening: 'Anda mendapatkan tugas review naskah baru melalui sistem PubliSync.',
                    details: [
                        'Kode Naskah' => $kodeNaskah,
                        'Judul Naskah' => $naskah->judul,
                        'Nama Penulis' => $namaPenulis,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Tanggal Submit' => $tanggalSubmit,
                    ],
                    bodyMessage: 'Mohon untuk meninjau naskah tersebut melalui dashboard Editor dan memberikan hasil review sesuai kebutuhan.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Detail Naskah',
                    actionUrl: route('editor.naskah.show', $naskah->id_naskah),
                ));
            }
        } catch (Throwable $emailException) {
            Log::warning('Gagal mengirim email workflow assign editor.', [
                'id_naskah' => $naskah->id_naskah,
                'id_editor' => $validated['id_editor'],
                'error' => $emailException->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.naskah.index')
            ->with('status', 'Naskah berhasil dikirim ke editor.');
    }
}
