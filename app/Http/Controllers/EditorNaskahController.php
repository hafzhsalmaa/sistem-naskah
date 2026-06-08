<?php

namespace App\Http\Controllers;

use App\Models\Naskah;
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

class EditorNaskahController extends Controller
{
    public function index(Request $request): View
    {
        return view('editor.naskah.index', $this->buildIndexData($request));
    }

    public function masuk(Request $request): View
    {
        return view('editor.naskah.masuk', $this->buildIndexData($request));
    }

    public function revisi(Request $request): View
    {
        return view('editor.naskah.revisi', $this->buildIndexData($request));
    }

    public function show(Request $request, int $id): View
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getAssignedNaskah($editor->getKey(), $id);
        $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);

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

        $revisiList = Revisi::query()
            ->leftJoin('editor', 'editor.id_editor', '=', 'revisi.id_editor')
            ->leftJoin('users as editor_user', 'editor_user.id_user', '=', 'editor.id_user')
            ->where('revisi.id_naskah', $id)
            ->where('revisi.id_editor', $editor->getKey())
            ->select(
                'revisi.*',
                'editor_user.username as nama_editor'
            )
            ->orderByDesc('revisi.tanggal_revisi')
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
            ])
            ->concat(
                $revisiList
                    ->filter(fn (Revisi $revisi): bool => filled($revisi->file_review_path))
                    ->map(fn (Revisi $revisi): object => (object) [
                        'source' => 'review_attachment',
                        'ref_id' => $revisi->id_revisi,
                        'label_versi' => 'Review Editor',
                        'jenis_file' => 'Lampiran Revisi',
                        'uploader' => $revisi->nama_editor ?? $request->user()->username,
                        'tanggal_upload' => $revisi->tanggal_revisi,
                        'file_path' => $revisi->file_review_path,
                        'nama_file_asli' => $revisi->nama_file_review_asli,
                    ])
            )
            ->sortBy('tanggal_upload')
            ->values();

        return view('editor.naskah.show', compact('naskah', 'versiList', 'historyItems', 'revisiList'));
    }

    public function updatePemeriksaanAwal(Request $request, int $id): RedirectResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getAssignedNaskah($editor->getKey(), $id);

        if ($this->reviewIsLocked($naskah->status_naskah)) {
            return redirect()
                ->route('editor.naskah.show', $naskah->id_naskah)
                ->withErrors(['review_locked' => 'Review sudah dikunci karena naskah telah masuk tahap berikutnya.']);
        }

        $validated = $request->validate([
            'cek_kurikulum' => ['nullable', 'accepted'],
            'cek_silabus' => ['nullable', 'accepted'],
            'cek_rpp' => ['nullable', 'accepted'],
            'bebas_sara' => ['nullable', 'accepted'],
        ]);

        DB::table('naskah')
            ->where('id_naskah', $naskah->id_naskah)
            ->update([
                'cek_kurikulum' => array_key_exists('cek_kurikulum', $validated),
                'cek_silabus' => array_key_exists('cek_silabus', $validated),
                'cek_rpp' => array_key_exists('cek_rpp', $validated),
                'bebas_sara' => array_key_exists('bebas_sara', $validated),
            ]);

        return redirect()
            ->route('editor.naskah.show', $naskah->id_naskah)
            ->with('status', 'Pemeriksaan awal berhasil diperbarui.');
    }

    public function review(Request $request, int $id): RedirectResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getAssignedNaskah($editor->getKey(), $id);

        if ($this->reviewIsLocked($naskah->status_naskah)) {
            return redirect()
                ->route('editor.naskah.show', $naskah->id_naskah)
                ->withErrors(['review_locked' => 'Review sudah dikunci karena naskah telah masuk tahap berikutnya.']);
        }

        $validated = $request->validate([
            'catatan_editor' => ['required', 'string', 'max:255'],
            'status_naskah' => ['required', 'in:Revisi,Diterima'],
            'file_review' => ['nullable', 'file', 'mimes:doc,docx,pdf', 'max:51200'],
        ], [
            'file_review.mimes' => 'Lampiran review harus berformat DOC, DOCX, atau PDF.',
            'file_review.max' => 'Lampiran review maksimal 50 MB.',
        ]);

        if (! $this->initialCheckCompleted($naskah)) {
            return redirect()
                ->route('editor.naskah.show', $naskah->id_naskah)
                ->withErrors(['pemeriksaan_awal' => 'Selesaikan pemeriksaan awal terlebih dahulu.']);
        }

        $reviewFilePath = null;
        $reviewFile = $request->file('file_review');

        if ($validated['status_naskah'] === 'Revisi' && $reviewFile) {
            $reviewFilePath = $reviewFile->store('review-editor');
        }

        try {
            Revisi::create([
                'id_naskah' => $naskah->id_naskah,
                'id_editor' => $editor->getKey(),
                'id_penulis' => $naskah->id_penulis,
                'catatan_editor' => $validated['catatan_editor'],
                'catatan_penulis' => null,
                'file_review_path' => $reviewFilePath,
                'nama_file_review_asli' => $reviewFilePath ? $reviewFile->getClientOriginalName() : null,
                'file_review_mime' => $reviewFilePath ? $reviewFile->getClientMimeType() : null,
                'tanggal_revisi' => now(),
                'status_revisi' => $validated['status_naskah'],
            ]);

            DB::table('naskah')
                ->where('id_naskah', $naskah->id_naskah)
                ->update([
                    'status_naskah' => $validated['status_naskah'],
                ]);
        } catch (Throwable $th) {
            if ($reviewFilePath) {
                Storage::delete($reviewFilePath);
            }

            throw $th;
        }

        $penulisUser = User::query()
            ->whereHas('penulis', fn ($query) => $query->where('id_penulis', $naskah->id_penulis))
            ->first();

        if ($penulisUser && $validated['status_naskah'] === 'Revisi') {
            $penulisUser->notify(new WorkflowNotification(
                'Naskah memerlukan revisi.',
                'Editor memberikan catatan revisi untuk naskah "'.$naskah->judul.'".',
                route('penulis.naskah.show', $naskah->id_naskah)
            ));

            try {
                if (filled($penulisUser->email)) {
                    $namaPenulis = $penulisUser->username ?? 'Penulis';
                    $namaEditor = $request->user()->username ?? 'Editor';
                    $catatanEditor = filled($validated['catatan_editor'])
                        ? $validated['catatan_editor']
                        : 'Catatan revisi dapat dilihat melalui halaman detail naskah di sistem PubliSync.';

                    $penulisUser->notify(new NaskahWorkflowEmailNotification(
                        subject: 'Revisi Naskah Diperlukan - '.$naskah->judul,
                        greeting: 'Halo '.$namaPenulis.',',
                        opening: 'Naskah Anda yang berjudul "'.$naskah->judul.'" telah selesai ditinjau oleh editor '.$namaEditor.'.',
                        details: [
                            'Kode Naskah' => $naskah->kode_naskah ?? '#'.$naskah->id_naskah,
                            'Judul Naskah' => $naskah->judul,
                            'Editor' => $namaEditor,
                            'Kelas' => $naskah->kelas,
                            'Mata Pelajaran' => $naskah->mata_pelajaran,
                            'Status' => 'Revisi',
                            'Catatan Editor' => $catatanEditor,
                        ],
                        bodyMessage: 'Berdasarkan hasil review, naskah tersebut memerlukan perbaikan sebelum dapat dilanjutkan ke tahap berikutnya. Mohon melakukan perbaikan sesuai catatan editor, kemudian unggah kembali file revisi melalui sistem PubliSync.',
                        closing: 'Terima kasih.',
                        actionText: 'Buka Detail Naskah',
                        actionUrl: route('penulis.naskah.show', $naskah->id_naskah),
                    ));
                }
            } catch (Throwable $emailException) {
                Log::warning('Gagal mengirim email workflow revisi naskah.', [
                    'id_naskah' => $naskah->id_naskah,
                    'id_penulis' => $naskah->id_penulis,
                    'error' => $emailException->getMessage(),
                ]);
            }
        }

        if ($penulisUser && $validated['status_naskah'] === 'Diterima') {
            $penulisUser->notify(new WorkflowNotification(
                'Naskah telah diterima editor.',
                'Editor telah menerima naskah "'.$naskah->judul.'".',
                route('penulis.naskah.show', $naskah->id_naskah)
            ));

            try {
                if (filled($penulisUser->email)) {
                    $namaPenulis = $penulisUser->username ?? 'Penulis';
                    $namaEditor = $request->user()->username ?? 'Editor';

                    $penulisUser->notify(new NaskahWorkflowEmailNotification(
                        subject: 'Naskah Diterima Editor - '.$naskah->judul,
                        greeting: 'Halo '.$namaPenulis.',',
                        opening: 'Naskah Anda yang berjudul "'.$naskah->judul.'" telah selesai ditinjau oleh editor '.$namaEditor.' dan dinyatakan diterima untuk dilanjutkan ke tahap berikutnya.',
                        details: [
                            'Kode Naskah' => $naskah->kode_naskah ?? '#'.$naskah->id_naskah,
                            'Judul Naskah' => $naskah->judul,
                            'Editor' => $namaEditor,
                            'Kelas' => $naskah->kelas,
                            'Mata Pelajaran' => $naskah->mata_pelajaran,
                            'Status' => 'Menunggu Layout',
                        ],
                        bodyMessage: 'Saat ini naskah Anda sedang menunggu proses layout. Kami akan mengirimkan notifikasi lanjutan apabila naskah sudah diserahkan kepada layouter atau terdapat pembaruan status berikutnya.',
                        closing: 'Terima kasih.',
                        actionText: 'Buka Detail Naskah',
                        actionUrl: route('penulis.naskah.show', $naskah->id_naskah),
                    ));
                }
            } catch (Throwable $emailException) {
                Log::warning('Gagal mengirim email workflow naskah diterima editor.', [
                    'id_naskah' => $naskah->id_naskah,
                    'id_penulis' => $naskah->id_penulis,
                    'error' => $emailException->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route($this->redirectRouteForReviewStatus($validated['status_naskah']))
            ->with('status', 'Review naskah berhasil disimpan.');
    }

    public function storeRevisi(Request $request, int $id): RedirectResponse
    {
        $request->merge([
            'status_naskah' => $request->input('status_revisi', $request->input('status_naskah')),
        ]);

        return $this->review($request, $id);
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getAssignedNaskah($editor->getKey(), $id);

        if ($this->reviewIsLocked($naskah->status_naskah)) {
            return redirect()
                ->route('editor.naskah.show', $naskah->id_naskah)
                ->withErrors(['review_locked' => 'Review sudah dikunci karena naskah telah masuk tahap berikutnya.']);
        }

        $validated = $request->validate([
            'status_naskah' => ['required', 'in:Revisi,Diterima'],
        ]);

        if (! $this->initialCheckCompleted($naskah)) {
            return redirect()
                ->route('editor.naskah.show', $naskah->id_naskah)
                ->withErrors(['pemeriksaan_awal' => 'Selesaikan pemeriksaan awal terlebih dahulu.']);
        }

        $request->merge([
            'catatan_editor' => 'Status diperbarui oleh editor.',
            'status_naskah' => $validated['status_naskah'],
        ]);

        return $this->review($request, $id);
    }

    public function download(Request $request, int $id): StreamedResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getAssignedNaskah($editor->getKey(), $id);

        $versiId = $request->integer('versi');

        if ($versiId) {
            $versi = VersiNaskah::query()
                ->where('id_versi', $versiId)
                ->where('id_naskah', $naskah->id_naskah)
                ->firstOrFail();

            return Storage::download($versi->file_path);
        }

        abort_unless($naskah->file_path, 404);

        return Storage::download($naskah->file_path);
    }

    public function uploadFileFinal(Request $request, int $id): RedirectResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = Naskah::query()
            ->where('id_naskah', $id)
            ->where('id_editor', $editor->getKey())
            ->firstOrFail();

        if (! $this->canManageFinalEditorFile($naskah->status_naskah)) {
            return redirect()
                ->route('editor.naskah.show', $naskah->getKey())
                ->withErrors(['file_final_editor' => 'File final editor hanya dapat diunggah setelah naskah diterima editor.']);
        }

        $validated = $request->validate([
            'file_final_editor' => ['required', 'file', 'mimes:doc,docx,pdf', 'max:51200'],
        ], [
            'file_final_editor.mimes' => 'File final editor harus berformat DOC, DOCX, atau PDF.',
        ]);

        $oldPath = $naskah->file_final_editor_path;
        $newPath = null;

        try {
            DB::transaction(function () use ($request, $naskah, $editor, &$newPath): void {
                $file = $request->file('file_final_editor');
                $newPath = $file->store('editor-final');

                $naskah->update([
                    'file_final_editor_path' => $newPath,
                    'nama_file_final_editor_asli' => $file->getClientOriginalName(),
                    'tanggal_file_final_editor' => now(),
                ]);
            });
        } catch (Throwable $th) {
            if ($newPath !== null) {
                Storage::delete($newPath);
            }

            throw $th;
        }

        if ($oldPath && $oldPath !== $newPath) {
            Storage::delete($oldPath);
        }

        return redirect()
            ->route('editor.naskah.show', $naskah->getKey())
            ->with('status', 'File final editor berhasil diunggah.');
    }

    public function downloadFileFinal(Request $request, int $id): StreamedResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getAssignedNaskah($editor->getKey(), $id);

        abort_unless($naskah->file_final_editor_path, 404);
        abort_unless(Storage::exists($naskah->file_final_editor_path), 404);

        return Storage::download(
            $naskah->file_final_editor_path,
            $naskah->nama_file_final_editor_asli ?: basename($naskah->file_final_editor_path)
        );
    }

    public function previewFileFinal(Request $request, int $id): BinaryFileResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getAssignedNaskah($editor->getKey(), $id);

        abort_unless($naskah->file_final_editor_path, 404);

        return $this->previewPdfFile($naskah->file_final_editor_path);
    }

    public function downloadReviewAttachment(Request $request, int $id, int $revisiId): StreamedResponse
    {
        $revisi = $this->getReviewAttachmentForEditor($request, $id, $revisiId);

        return Storage::download(
            $revisi->file_review_path,
            $revisi->nama_file_review_asli ?: basename($revisi->file_review_path)
        );
    }

    public function previewReviewAttachment(Request $request, int $id, int $revisiId): BinaryFileResponse
    {
        $revisi = $this->getReviewAttachmentForEditor($request, $id, $revisiId);

        return $this->previewPdfFile($revisi->file_review_path);
    }

    private function getReviewAttachmentForEditor(Request $request, int $id, int $revisiId): Revisi
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $naskah = $this->getAssignedNaskah($editor->getKey(), $id);

        $revisi = Revisi::query()
            ->where('id_revisi', $revisiId)
            ->where('id_naskah', $naskah->id_naskah)
            ->where('id_editor', $editor->getKey())
            ->whereNotNull('file_review_path')
            ->firstOrFail();

        abort_unless(Storage::exists($revisi->file_review_path), 404);

        return $revisi;
    }

    public function kirimLayouter(Request $request, int $id): RedirectResponse
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $validated = $request->validate([
            'id_layouter' => ['required', 'integer'],
        ]);

        $naskah = DB::table('naskah')
            ->where('naskah.id_naskah', $id)
            ->where('naskah.id_editor', $editor->getKey())
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.kelas',
                'naskah.status_naskah',
                'naskah.id_penulis',
                'naskah.cek_kurikulum',
                'naskah.cek_silabus',
                'naskah.cek_rpp',
                'naskah.bebas_sara',
                'naskah.bidang_keahlian',
                'naskah.kategori_mapel',
                'naskah.mata_pelajaran',
                'naskah.file_final_editor_path'
            )
            ->firstOrFail();

        if (! $this->initialCheckCompleted($naskah)) {
            return redirect()
                ->route('editor.naskah.show', $naskah->id_naskah)
                ->withErrors(['pemeriksaan_awal' => 'Selesaikan pemeriksaan awal terlebih dahulu.']);
        }

        if ($naskah->status_naskah !== 'Diterima') {
            return redirect()
                ->route('editor.naskah.revisi')
                ->withErrors(['id_layouter' => 'Hanya naskah berstatus Diterima yang bisa dikirim ke layouter.']);
        }

        if (blank($naskah->file_final_editor_path) || ! Storage::exists($naskah->file_final_editor_path)) {
            return redirect()
                ->route('editor.naskah.revisi')
                ->withErrors(['id_layouter' => 'Upload File Final Editor terlebih dahulu sebelum mengirim naskah ke Layouter.']);
        }

        $layouterExists = DB::table('layouter')
            ->where('id_layouter', $validated['id_layouter'])
            ->exists();

        if (! $layouterExists) {
            return redirect()
                ->route('editor.naskah.revisi')
                ->withErrors(['id_layouter' => 'Layouter yang dipilih tidak ditemukan.']);
        }

        DB::table('naskah')
            ->where('id_naskah', $naskah->id_naskah)
            ->update([
                'id_layouter' => $validated['id_layouter'],
                'status_naskah' => 'Menunggu Layout',
            ]);

        $layouterUser = User::query()
            ->whereHas('layouter', fn ($query) => $query->where('id_layouter', $validated['id_layouter']))
            ->first();
        $penulisUser = User::query()
            ->whereHas('penulis', fn ($query) => $query->where('id_penulis', $naskah->id_penulis))
            ->first();

        if ($layouterUser) {
            $layouterUser->notify(new WorkflowNotification(
                'Anda menerima tugas layout naskah.',
                'Editor telah mengirim naskah untuk proses layout.',
                route('layouter.naskah.show', $naskah->id_naskah)
            ));
        }

        try {
            $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
            $namaPenulis = $penulisUser?->username ?? '-';
            $namaLayouter = $layouterUser?->username ?? '-';
            $namaEditor = $request->user()->username ?? 'Editor';
            $statusTampilan = 'Proses Layout';

            if ($penulisUser && filled($penulisUser->email)) {
                $penulisUser->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Naskah Masuk Proses Layout - '.$naskah->judul,
                    greeting: 'Halo '.$namaPenulis.',',
                    opening: 'Naskah Anda yang berjudul "'.$naskah->judul.'" telah diserahkan kepada layouter '.$namaLayouter.' untuk proses layout.',
                    details: [
                        'Kode Naskah' => $kodeNaskah,
                        'Judul Naskah' => $naskah->judul,
                        'Layouter' => $namaLayouter,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Status' => $statusTampilan,
                    ],
                    bodyMessage: 'Saat ini naskah sedang masuk tahap penyusunan tampilan akhir. Kami akan mengirimkan notifikasi lanjutan apabila proses layout telah selesai.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Detail Naskah',
                    actionUrl: route('penulis.naskah.show', $naskah->id_naskah),
                ));
            }

            if ($layouterUser && filled($layouterUser->email)) {
                $layouterUser->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Naskah Baru untuk Layout - '.$naskah->judul,
                    greeting: 'Halo '.$namaLayouter.',',
                    opening: 'Anda mendapatkan tugas layout naskah baru melalui sistem PubliSync.',
                    details: [
                        'Kode Naskah' => $kodeNaskah,
                        'Judul Naskah' => $naskah->judul,
                        'Nama Penulis' => $namaPenulis,
                        'Editor' => $namaEditor,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Status' => $statusTampilan,
                    ],
                    bodyMessage: 'Mohon untuk memproses layout naskah tersebut melalui dashboard Layouter.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Detail Naskah',
                    actionUrl: route('layouter.naskah.show', $naskah->id_naskah),
                ));
            }
        } catch (Throwable $emailException) {
            Log::warning('Gagal mengirim email workflow kirim layouter.', [
                'id_naskah' => $naskah->id_naskah,
                'id_layouter' => $validated['id_layouter'],
                'error' => $emailException->getMessage(),
            ]);
        }

        return redirect()
            ->route('editor.riwayat-naskah.index')
            ->with('status', 'Naskah berhasil dikirim ke layouter.');
    }

    private function getAssignedNaskah(int $idEditor, int $idNaskah): object
    {
        $latestVersiSubquery = DB::table('versi_naskah as vn')
            ->select('vn.id_naskah', DB::raw('MAX(vn.no_versi) as latest_no_versi'))
            ->groupBy('vn.id_naskah');

        return DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users as penulis_user', 'penulis_user.id_user', '=', 'penulis.id_user')
            ->leftJoinSub($latestVersiSubquery, 'latest_versi', function ($join): void {
                $join->on('latest_versi.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('versi_naskah', function ($join): void {
                $join->on('versi_naskah.id_naskah', '=', 'naskah.id_naskah')
                    ->on('versi_naskah.no_versi', '=', 'latest_versi.latest_no_versi');
            })
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where('naskah.id_naskah', $idNaskah)
            ->where('naskah.id_editor', $idEditor)
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.id_penulis',
                'naskah.judul',
                'naskah.kelas',
                'naskah.kurikulum',
                'naskah.kategori_mapel',
                'naskah.mata_pelajaran',
                'naskah.deskripsi',
                'naskah.cek_kurikulum',
                'naskah.cek_silabus',
                'naskah.cek_rpp',
                'naskah.bebas_sara',
                'naskah.id_layouter',
                'naskah.status_naskah',
                'naskah.file_final_editor_path',
                'naskah.nama_file_final_editor_asli',
                'naskah.tanggal_file_final_editor',
                'jadwal_penerbitan.tanggal_cetak',
                'latest_versi.latest_no_versi as no_versi_terbaru',
                'penulis_user.username as nama_penulis',
                'versi_naskah.file_path',
                'versi_naskah.nama_file_asli',
                'versi_naskah.tanggal_upload'
            )
            ->firstOrFail();
    }

    private function initialCheckCompleted(object $naskah): bool
    {
        return (bool) $naskah->cek_kurikulum
            && (bool) $naskah->cek_silabus
            && (bool) $naskah->cek_rpp
            && (bool) $naskah->bebas_sara;
    }

    private function redirectRouteForReviewStatus(string $status): string
    {
        return match ($status) {
            'Pending Review' => 'editor.naskah.masuk',
            'Revisi', 'Perbaikan Dikirim', 'Ditolak', 'Diterima' => 'editor.naskah.revisi',
            default => 'editor.naskah.masuk',
        };
    }

    private function buildIndexData(Request $request): array
    {
        $editor = $request->user()->editor;

        abort_unless($editor, 403);

        $latestVersiSubquery = DB::table('versi_naskah as vn')
            ->select(
                'vn.id_naskah',
                DB::raw('MAX(vn.no_versi) as latest_no_versi'),
                DB::raw('MAX(vn.tanggal_upload) as latest_tanggal_upload')
            )
            ->groupBy('vn.id_naskah');

        $latestRevisiSubquery = DB::table('revisi as rv')
            ->select(
                'rv.id_naskah',
                DB::raw('MAX(rv.tanggal_revisi) as latest_tanggal_revisi')
            )
            ->groupBy('rv.id_naskah');

        $naskahList = DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->join('users as penulis_user', 'penulis_user.id_user', '=', 'penulis.id_user')
            ->leftJoin('layouter', 'layouter.id_layouter', '=', 'naskah.id_layouter')
            ->leftJoin('users as layouter_user', 'layouter_user.id_user', '=', 'layouter.id_user')
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->leftJoinSub($latestVersiSubquery, 'latest_versi', function ($join): void {
                $join->on('latest_versi.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoinSub($latestRevisiSubquery, 'latest_revisi', function ($join): void {
                $join->on('latest_revisi.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('versi_naskah', function ($join): void {
                $join->on('versi_naskah.id_naskah', '=', 'naskah.id_naskah')
                    ->on('versi_naskah.no_versi', '=', 'latest_versi.latest_no_versi');
            })
            ->where('naskah.id_editor', $editor->getKey())
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.kelas',
                'naskah.mata_pelajaran',
                'naskah.cek_kurikulum',
                'naskah.cek_silabus',
                'naskah.cek_rpp',
                'naskah.bebas_sara',
                'naskah.id_layouter',
                'naskah.status_naskah',
                'naskah.file_final_editor_path',
                'naskah.nama_file_final_editor_asli',
                'naskah.tanggal_file_final_editor',
                'penulis_user.username as nama_penulis',
                'layouter_user.username as nama_layouter',
                'versi_naskah.file_path',
                'versi_naskah.nama_file_asli',
                'latest_versi.latest_no_versi as no_versi_terbaru',
                'latest_versi.latest_tanggal_upload as tanggal_upload_terbaru',
                'latest_revisi.latest_tanggal_revisi as tanggal_revisi_terakhir',
                'jadwal_penerbitan.tanggal_cetak'
            )
            ->orderByDesc('naskah.id_naskah')
            ->get();
        $naskahList = $naskahList->map(function (object $naskah): object {
            $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);

            return $naskah;
        });

        $naskahMasuk = $naskahList->filter(function (object $naskah): bool {
            return $naskah->status_naskah === 'Pending Review';
        })->values();

        $naskahRevisi = $naskahList->filter(function (object $naskah): bool {
            return in_array($naskah->status_naskah, ['Revisi', 'Perbaikan Dikirim'], true);
        })->values();

        $naskahSelesaiReview = $naskahList->filter(function (object $naskah): bool {
            if ($naskah->status_naskah === 'Ditolak') {
                return true;
            }

            return $naskah->status_naskah === 'Diterima' && $naskah->id_layouter === null;
        })->values();

        $layouterMap = [];
        $layouterActiveCountSubquery = DB::table('naskah as active_naskah')
            ->select('active_naskah.id_layouter', DB::raw('COUNT(*) as naskah_aktif_count'))
            ->whereIn('active_naskah.status_naskah', ['Menunggu Layout', 'Proses Layout'])
            ->whereNotNull('active_naskah.id_layouter')
            ->groupBy('active_naskah.id_layouter');

        foreach ($naskahSelesaiReview->where('status_naskah', 'Diterima') as $naskah) {
            $layouterMap[$naskah->id_naskah] = DB::table('layouter')
                ->join('users', 'users.id_user', '=', 'layouter.id_user')
                ->leftJoinSub($layouterActiveCountSubquery, 'layouter_active_count', function ($join): void {
                    $join->on('layouter_active_count.id_layouter', '=', 'layouter.id_layouter');
                })
                ->select(
                    'layouter.id_layouter',
                    'users.username',
                    'layouter.bidang_keahlian',
                    'layouter.kategori_mapel',
                    'layouter.mata_pelajaran',
                    DB::raw('COALESCE(layouter_active_count.naskah_aktif_count, 0) as naskah_aktif_count')
                )
                ->orderBy('users.username')
                ->get();
        }

        return compact('naskahMasuk', 'naskahRevisi', 'naskahSelesaiReview', 'layouterMap');
    }

    private function resolveDisplayStatus(object $naskah): string
    {
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

    private function canManageFinalEditorFile(string $status): bool
    {
        return in_array($status, [
            'Diterima',
            'Menunggu Layout',
            'Proses Layout',
            'Selesai Layout',
        ], true);
    }

    private function reviewIsLocked(string $status): bool
    {
        return in_array($status, [
            'Diterima',
            'Menunggu Layout',
            'Proses Layout',
            'Selesai Layout',
        ], true);
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
