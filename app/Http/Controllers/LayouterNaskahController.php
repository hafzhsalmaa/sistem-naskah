<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Revisi;
use App\Models\User;
use App\Models\VersiNaskah;
use App\Notifications\NaskahWorkflowEmailNotification;
use App\Notifications\WorkflowNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class LayouterNaskahController extends Controller
{
    public function index(Request $request): View
    {
        return view('layouter.naskah.index', $this->buildIndexData($request));
    }

    public function masuk(Request $request): View
    {
        return view('layouter.naskah.masuk', $this->buildIndexData($request));
    }

    public function layoutSelesai(Request $request): View
    {
        return view('layouter.naskah.layout-selesai', $this->buildIndexData($request));
    }

    public function show(Request $request, int $id): View
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $naskah = $this->getAssignedNaskah($layouter->getKey(), $id);
        $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);
        $fileUtama = $this->resolveMainManuscriptFile($naskah);

        $versiList = VersiNaskah::query()
            ->leftJoin('users', 'users.id_user', '=', 'versi_naskah.id_user_pengunggah')
            ->where('versi_naskah.id_naskah', $id)
            ->select(
                'versi_naskah.id_versi',
                'versi_naskah.id_naskah',
                'versi_naskah.file_path',
                'versi_naskah.nama_file_asli',
                'versi_naskah.no_versi',
                'versi_naskah.tanggal_upload',
                'users.username as nama_pengunggah'
            )
            ->get();

        $historyItems = $versiList
            ->map(fn (object $versi): object => (object) [
                'source' => 'versi',
                'ref_id' => $versi->id_versi,
                'label_versi' => 'Versi '.$versi->no_versi,
                'jenis_file' => 'Naskah Penulis',
                'uploader' => $versi->nama_pengunggah,
                'tanggal_upload' => $versi->tanggal_upload,
                'file_path' => $versi->file_path,
                'nama_file_asli' => $versi->nama_file_asli,
            ]);

        $reviewItems = Revisi::query()
            ->leftJoin('editor', 'editor.id_editor', '=', 'revisi.id_editor')
            ->leftJoin('users', 'users.id_user', '=', 'editor.id_user')
            ->where('revisi.id_naskah', $naskah->id_naskah)
            ->whereNotNull('revisi.file_review_path')
            ->select(
                'revisi.id_revisi',
                'revisi.file_review_path',
                'revisi.nama_file_review_asli',
                'revisi.tanggal_revisi',
                'users.username as nama_editor'
            )
            ->get()
            ->map(fn (Revisi $revisi): object => (object) [
                'source' => 'review_attachment',
                'ref_id' => $revisi->id_revisi,
                'label_versi' => 'Review Editor',
                'jenis_file' => 'Lampiran Revisi',
                'uploader' => $revisi->nama_editor ?? $naskah->nama_editor,
                'tanggal_upload' => $revisi->tanggal_revisi,
                'file_path' => $revisi->file_review_path,
                'nama_file_asli' => $revisi->nama_file_review_asli,
            ]);

        $historyItems = $historyItems->concat($reviewItems);

        if (filled($naskah->file_final_editor_path)) {
            $historyItems = $historyItems->push((object) [
                'source' => 'final_editor',
                'ref_id' => $naskah->id_naskah,
                'label_versi' => 'Versi Final Editor',
                'jenis_file' => 'File Final',
                'uploader' => $naskah->nama_editor ?? 'Editor',
                'tanggal_upload' => $naskah->tanggal_file_final_editor,
                'file_path' => $naskah->file_final_editor_path,
                'nama_file_asli' => $naskah->nama_file_final_editor_asli,
            ]);
        }

        if (filled($naskah->file_layout)) {
            $historyItems = $historyItems->push((object) [
                'source' => 'layout',
                'ref_id' => $naskah->id_layout,
                'label_versi' => 'Versi Layout',
                'jenis_file' => 'Hasil Layout',
                'uploader' => $request->user()->username,
                'tanggal_upload' => $naskah->tanggal_selesai_layout,
                'file_path' => $naskah->file_layout,
                'nama_file_asli' => $naskah->nama_file_layout_asli,
            ]);
        }

        $historyItems = $historyItems
            ->sortBy('tanggal_upload')
            ->values();

        return view('layouter.naskah.show', compact('naskah', 'versiList', 'historyItems', 'fileUtama'));
    }

    public function download(Request $request, int $id): StreamedResponse
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $naskah = $this->getAssignedNaskah($layouter->getKey(), $id);

        $versiId = $request->integer('versi');
        $source = $request->query('source');

        if ($versiId) {
            $versi = VersiNaskah::query()
                ->where('id_versi', $versiId)
                ->where('id_naskah', $naskah->id_naskah)
                ->firstOrFail();

            $downloadName = $versi->nama_file_asli ?: basename($versi->file_path);

            return Storage::download($versi->file_path, $downloadName);
        }

        if ($source === 'layout') {
            abort_unless($naskah->file_layout, 404);
            abort_unless(Storage::exists($naskah->file_layout), 404);

            return Storage::download(
                $naskah->file_layout,
                $naskah->nama_file_layout_asli ?: basename($naskah->file_layout)
            );
        }

        $fileUtama = $this->resolveMainManuscriptFile($naskah);

        abort_unless($fileUtama['path'], 404);
        abort_unless(Storage::exists($fileUtama['path']), 404);

        return Storage::download($fileUtama['path'], $fileUtama['name'] ?: basename($fileUtama['path']));
    }

    public function preview(Request $request, int $id): BinaryFileResponse
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $naskah = $this->getAssignedNaskah($layouter->getKey(), $id);

        if ($request->query('source') === 'main') {
            $fileUtama = $this->resolveMainManuscriptFile($naskah);

            abort_unless($fileUtama['path'], 404);

            return $this->previewPdfFile($fileUtama['path']);
        }

        if ($request->query('source') === 'layout') {
            abort_unless($naskah->file_layout, 404);

            return $this->previewPdfFile($naskah->file_layout);
        }

        abort_unless($naskah->file_layout, 404);

        return $this->previewPdfFile($naskah->file_layout);
    }

    public function downloadReviewAttachment(Request $request, int $id, int $revisiId): StreamedResponse
    {
        $revisi = $this->getReviewAttachmentForLayouter($request, $id, $revisiId);

        return Storage::download(
            $revisi->file_review_path,
            $revisi->nama_file_review_asli ?: basename($revisi->file_review_path)
        );
    }

    public function previewReviewAttachment(Request $request, int $id, int $revisiId): BinaryFileResponse
    {
        $revisi = $this->getReviewAttachmentForLayouter($request, $id, $revisiId);

        return $this->previewPdfFile($revisi->file_review_path);
    }

    public function upload(Request $request, int $id): RedirectResponse
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $validated = $request->validate([
            'file_layout' => ['required', 'file', 'mimes:docx,pdf', 'max:51200'],
        ], [
            'file_layout.mimes' => 'File hasil layout harus berformat DOCX atau PDF.',
        ]);

        $naskah = $this->getAssignedNaskah($layouter->getKey(), $id);

        if ($naskah->status_naskah !== 'Proses Layout') {
            return redirect()
                ->route('layouter.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'layout-selesai'])
                ->withErrors(['file_layout' => 'Upload hasil layout hanya tersedia untuk naskah yang sedang dalam proses layout.']);
        }

        $filePath = null;

        try {
            DB::transaction(function () use ($validated, $request, $layouter, $naskah, &$filePath): void {
                $now = now();
                $filePath = $request->file('file_layout')->store('layout');

                $layout = Layout::query()
                    ->where('id_naskah', $naskah->id_naskah)
                    ->where('id_layouter', $layouter->getKey())
                    ->latest('id_layout')
                    ->first();

                if ($layout && $layout->file_layout && $layout->file_layout !== $filePath) {
                    Storage::delete($layout->file_layout);
                }

                if ($layout) {
                    $layout->update([
                        'id_penulis' => $naskah->id_penulis,
                        'file_layout' => $filePath,
                        'nama_file_layout_asli' => $request->file('file_layout')->getClientOriginalName(),
                        'tanggal_layout' => $now,
                        'status_layout' => 'Proses Layout',
                    ]);
                } else {
                    Layout::create([
                        'id_naskah' => $naskah->id_naskah,
                        'id_layouter' => $layouter->getKey(),
                        'id_penulis' => $naskah->id_penulis,
                        'file_layout' => $filePath,
                        'nama_file_layout_asli' => $request->file('file_layout')->getClientOriginalName(),
                        'tanggal_layout' => $now,
                        'status_layout' => 'Proses Layout',
                    ]);
                }

                DB::table('naskah')
                    ->where('id_naskah', $naskah->id_naskah)
                    ->update([
                        'status_naskah' => 'Proses Layout',
                    ]);
            });
        } catch (Throwable $th) {
            if ($filePath !== null) {
                Storage::delete($filePath);
            }

            throw $th;
        }

        $penulisUser = User::query()
            ->whereHas('penulis', fn ($query) => $query->where('id_penulis', $naskah->id_penulis))
            ->first();

        $adminUsers = User::query()
            ->where('role', 'admin')
            ->get();

        $adminUsers->each(function (User $admin) use ($naskah): void {
            $admin->notify(new WorkflowNotification(
                'Layout naskah telah selesai.',
                'Layouter telah mengunggah hasil layout untuk naskah "'.$naskah->judul.'".',
                route('admin.naskah.index')
            ));
        });

        if ($penulisUser) {
            $penulisUser->notify(new WorkflowNotification(
                'Layout naskah telah selesai.',
                'Hasil layout untuk naskah "'.$naskah->judul.'" telah diunggah.',
                route('penulis.naskah.show', $naskah->id_naskah)
            ));
        }

        return redirect()
            ->route('layouter.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'layout-selesai'])
            ->with('status', 'Hasil layout berhasil diunggah.');
    }

    public function mulaiLayout(Request $request, int $id): RedirectResponse
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $naskah = $this->getAssignedNaskah($layouter->getKey(), $id);

        if ($naskah->status_naskah !== 'Menunggu Layout') {
            return redirect()
                ->route('layouter.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'masuk'])
                ->withErrors(['status_naskah' => 'Naskah ini tidak dapat mulai dikerjakan dari status saat ini.']);
        }

        DB::table('naskah')
            ->where('id_naskah', $naskah->id_naskah)
            ->where('id_layouter', $layouter->getKey())
            ->update([
                'status_naskah' => 'Proses Layout',
            ]);

        return redirect()
            ->route('layouter.naskah.layout-selesai')
            ->with('status', 'Naskah dipindahkan ke Manajemen Layout.');
    }

    public function selesai(Request $request, int $id): RedirectResponse
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $naskah = $this->getAssignedNaskah($layouter->getKey(), $id);

        if ($naskah->status_naskah !== 'Proses Layout') {
            return redirect()
                ->route('layouter.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'layout-selesai'])
                ->withErrors(['status_naskah' => 'Naskah hanya dapat diselesaikan saat berada dalam proses layout.']);
        }

        $layout = Layout::query()
            ->where('id_naskah', $naskah->id_naskah)
            ->where('id_layouter', $layouter->getKey())
            ->latest('id_layout')
            ->first();

        if (! $layout || ! $layout->file_layout) {
            return redirect()
                ->route('layouter.naskah.show', $naskah->id_naskah)
                ->withErrors(['file_layout' => 'Upload hasil layout terlebih dahulu sebelum menyelesaikan proses.']);
        }

        $now = now();

        $layout->update([
            'tanggal_layout' => $now,
            'status_layout' => 'Selesai',
        ]);

        DB::table('naskah')
            ->where('id_naskah', $naskah->id_naskah)
            ->update([
                'status_naskah' => 'Selesai Layout',
            ]);

        try {
            $penulisUser = User::query()
                ->whereHas('penulis', fn ($query) => $query->where('id_penulis', $naskah->id_penulis))
                ->first();
            $adminUsers = User::query()
                ->where('role', 'admin')
                ->get();
            $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
            $namaPenulis = $penulisUser?->username ?? $naskah->nama_penulis ?? '-';
            $namaEditor = $naskah->nama_editor ?? '-';
            $namaLayouter = $request->user()->username ?? 'Layouter';
            $statusTampilan = 'Menunggu Jadwal Penerbitan';

            if ($penulisUser && filled($penulisUser->email)) {
                $penulisUser->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Layout Naskah Selesai - '.$naskah->judul,
                    greeting: 'Halo '.$namaPenulis.',',
                    opening: 'Proses layout untuk naskah Anda yang berjudul "'.$naskah->judul.'" telah selesai.',
                    details: [
                        'Kode Naskah' => $kodeNaskah,
                        'Judul Naskah' => $naskah->judul,
                        'Layouter' => $namaLayouter,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Status' => $statusTampilan,
                    ],
                    bodyMessage: 'Saat ini naskah menunggu proses penjadwalan penerbitan oleh Admin. Kami akan mengirimkan notifikasi lanjutan apabila jadwal terbit sudah ditentukan.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Detail Naskah',
                    actionUrl: route('penulis.naskah.show', $naskah->id_naskah),
                ));
            }

            $adminUsers->each(function (User $admin) use ($naskah, $kodeNaskah, $namaPenulis, $namaEditor, $namaLayouter, $statusTampilan): void {
                if (! filled($admin->email)) {
                    return;
                }

                $admin->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Naskah Siap Dijadwalkan - '.$naskah->judul,
                    greeting: 'Halo Admin,',
                    opening: 'Terdapat naskah yang telah selesai melalui proses layout dan siap dijadwalkan untuk penerbitan.',
                    details: [
                        'Kode Naskah' => $kodeNaskah,
                        'Judul Naskah' => $naskah->judul,
                        'Nama Penulis' => $namaPenulis,
                        'Editor' => $namaEditor,
                        'Layouter' => $namaLayouter,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Status' => $statusTampilan,
                    ],
                    bodyMessage: 'Mohon meninjau naskah tersebut dan menentukan jadwal penerbitan melalui sistem PubliSync.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Jadwal Penerbitan',
                    actionUrl: route('admin.jadwal-penerbitan.index'),
                ));
            });
        } catch (Throwable $emailException) {
            Log::warning('Gagal mengirim email workflow layout selesai.', [
                'id_naskah' => $naskah->id_naskah,
                'id_layouter' => $layouter->getKey(),
                'error' => $emailException->getMessage(),
            ]);
        }

        return redirect()
            ->route('layouter.riwayat-naskah.index')
            ->with('status', 'Layout naskah berhasil diselesaikan.');
    }

    private function getAssignedNaskah(int $idLayouter, int $idNaskah): object
    {
        $latestVersiSubquery = DB::table('versi_naskah as vn')
            ->select('vn.id_naskah', DB::raw('MAX(vn.no_versi) as latest_no_versi'))
            ->groupBy('vn.id_naskah');

        $latestLayoutSubquery = DB::table('layout as ly')
            ->select('ly.id_naskah', DB::raw('MAX(ly.id_layout) as latest_id_layout'))
            ->groupBy('ly.id_naskah');

        return DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users as penulis_user', 'penulis_user.id_user', '=', 'penulis.id_user')
            ->leftJoin('editor', 'editor.id_editor', '=', 'naskah.id_editor')
            ->leftJoin('users as editor_user', 'editor_user.id_user', '=', 'editor.id_user')
            ->leftJoinSub($latestVersiSubquery, 'latest_versi', function ($join): void {
                $join->on('latest_versi.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('versi_naskah', function ($join): void {
                $join->on('versi_naskah.id_naskah', '=', 'naskah.id_naskah')
                    ->on('versi_naskah.no_versi', '=', 'latest_versi.latest_no_versi');
            })
            ->leftJoinSub($latestLayoutSubquery, 'latest_layout', function ($join): void {
                $join->on('latest_layout.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('layout', 'layout.id_layout', '=', 'latest_layout.latest_id_layout')
            ->where('naskah.id_naskah', $idNaskah)
            ->where('naskah.id_layouter', $idLayouter)
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.id_penulis',
                'naskah.id_layouter',
                'naskah.judul',
                'naskah.kelas',
                'naskah.kurikulum',
                'naskah.kategori_mapel',
                'naskah.mata_pelajaran',
                'naskah.deskripsi',
                'naskah.status_naskah',
                'naskah.file_final_editor_path',
                'naskah.nama_file_final_editor_asli',
                'naskah.tanggal_file_final_editor',
                'latest_versi.latest_no_versi as no_versi_terbaru',
                'penulis_user.username as nama_penulis',
                'editor_user.username as nama_editor',
                'versi_naskah.file_path',
                'versi_naskah.nama_file_asli',
                'versi_naskah.tanggal_upload',
                'layout.id_layout',
                'layout.file_layout',
                'layout.nama_file_layout_asli',
                'layout.tanggal_layout as tanggal_selesai_layout'
            )
            ->firstOrFail();
    }

    private function buildIndexData(Request $request): array
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $latestVersiSubquery = DB::table('versi_naskah as vn')
            ->select(
                'vn.id_naskah',
                DB::raw('MAX(vn.no_versi) as latest_no_versi'),
                DB::raw('MAX(vn.tanggal_upload) as latest_tanggal_upload')
            )
            ->groupBy('vn.id_naskah');

        $latestLayoutSubquery = DB::table('layout as ly')
            ->select(
                'ly.id_naskah',
                DB::raw('MAX(ly.id_layout) as latest_id_layout'),
                DB::raw('MAX(ly.tanggal_layout) as latest_tanggal_layout')
            )
            ->groupBy('ly.id_naskah');

        $naskahList = DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users as penulis_user', 'penulis_user.id_user', '=', 'penulis.id_user')
            ->leftJoin('editor', 'editor.id_editor', '=', 'naskah.id_editor')
            ->leftJoin('users as editor_user', 'editor_user.id_user', '=', 'editor.id_user')
            ->leftJoinSub($latestVersiSubquery, 'latest_versi', function ($join): void {
                $join->on('latest_versi.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('versi_naskah', function ($join): void {
                $join->on('versi_naskah.id_naskah', '=', 'naskah.id_naskah')
                    ->on('versi_naskah.no_versi', '=', 'latest_versi.latest_no_versi');
            })
            ->leftJoinSub($latestLayoutSubquery, 'latest_layout', function ($join): void {
                $join->on('latest_layout.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('layout', 'layout.id_layout', '=', 'latest_layout.latest_id_layout')
            ->where('naskah.id_layouter', $layouter->getKey())
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.id_layouter',
                'naskah.status_naskah',
                'naskah.mata_pelajaran',
                'naskah.file_final_editor_path',
                'naskah.nama_file_final_editor_asli',
                'naskah.tanggal_file_final_editor',
                'penulis_user.username as nama_penulis',
                'editor_user.username as nama_editor',
                'versi_naskah.file_path',
                'versi_naskah.nama_file_asli',
                'latest_versi.latest_no_versi as no_versi_terbaru',
                'latest_versi.latest_tanggal_upload as tanggal_upload_terbaru',
                'layout.file_layout',
                'layout.nama_file_layout_asli',
                'layout.tanggal_layout'
            )
            ->orderByDesc('naskah.id_naskah')
            ->get()
            ->map(function (object $naskah): object {
                $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);

                return $naskah;
            });

        $naskahAktif = $naskahList->filter(
            fn (object $naskah): bool => $naskah->status_naskah === 'Menunggu Layout'
        )->values();

        $naskahSelesai = $naskahList->filter(
            fn (object $naskah): bool => $naskah->status_naskah === 'Proses Layout'
        )->values();

        return compact('naskahAktif', 'naskahSelesai');
    }

    /**
     * @return array{path: ?string, name: ?string, date: mixed, label: string, source: string}
     */
    private function resolveMainManuscriptFile(object $naskah): array
    {
        if (filled($naskah->file_final_editor_path ?? null)) {
            return [
                'path' => $naskah->file_final_editor_path,
                'name' => $naskah->nama_file_final_editor_asli ?? basename($naskah->file_final_editor_path),
                'date' => $naskah->tanggal_file_final_editor ?? null,
                'label' => 'File Final Editor',
                'source' => 'final_editor',
            ];
        }

        return [
            'path' => $naskah->file_path ?? null,
            'name' => isset($naskah->file_path)
                ? ($naskah->nama_file_asli ?? basename($naskah->file_path))
                : null,
            'date' => $naskah->tanggal_upload ?? ($naskah->tanggal_upload_terbaru ?? null),
            'label' => 'File Naskah Terbaru',
            'source' => 'versi_naskah',
        ];
    }

    private function resolveDisplayStatus(object $naskah): string
    {
        return $naskah->status_naskah;
    }

    private function getReviewAttachmentForLayouter(Request $request, int $id, int $revisiId): Revisi
    {
        $layouter = $request->user()->layouter;

        abort_unless($layouter, 403);

        $naskah = $this->getAssignedNaskah($layouter->getKey(), $id);

        $revisi = Revisi::query()
            ->where('id_revisi', $revisiId)
            ->where('id_naskah', $naskah->id_naskah)
            ->whereNotNull('file_review_path')
            ->firstOrFail();

        abort_unless(Storage::exists($revisi->file_review_path), 404);

        return $revisi;
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
