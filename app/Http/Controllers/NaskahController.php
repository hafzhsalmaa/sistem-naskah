<?php

namespace App\Http\Controllers;

use App\Models\Naskah;
use App\Models\Layout;
use App\Models\Penulis;
use App\Models\Revisi;
use App\Models\User;
use App\Models\VersiNaskah;
use App\Notifications\NaskahWorkflowEmailNotification;
use App\Notifications\WorkflowNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class NaskahController extends Controller
{
    public function index(Request $request): View
    {
        $penulis = Penulis::query()
            ->where('id_user', $request->user()->getKey())
            ->firstOrFail();

        $latestVersiSubquery = DB::table('versi_naskah as vn')
            ->select('vn.id_naskah', DB::raw('MAX(vn.no_versi) as latest_no_versi'))
            ->groupBy('vn.id_naskah');

        $naskahList = DB::table('naskah')
            ->leftJoinSub($latestVersiSubquery, 'latest_versi', function ($join): void {
                $join->on('latest_versi.id_naskah', '=', 'naskah.id_naskah');
            })
            ->leftJoin('versi_naskah', function ($join): void {
                $join->on('versi_naskah.id_naskah', '=', 'naskah.id_naskah')
                    ->on('versi_naskah.no_versi', '=', 'latest_versi.latest_no_versi');
            })
            ->leftJoin('jadwal_penerbitan', 'jadwal_penerbitan.id_naskah', '=', 'naskah.id_naskah')
            ->where('naskah.id_penulis', $penulis->getKey())
            ->select(
                'naskah.id_naskah',
                'naskah.kode_naskah',
                'naskah.judul',
                'naskah.kelas',
                'naskah.kategori_mapel',
                'naskah.mata_pelajaran',
                'naskah.id_editor',
                'naskah.id_layouter',
                'naskah.status_naskah',
                'jadwal_penerbitan.tanggal_cetak',
                'latest_versi.latest_no_versi',
                'versi_naskah.file_path',
                'versi_naskah.nama_file_asli',
                'versi_naskah.tanggal_upload'
            )
            ->orderByDesc('naskah.id_naskah')
            ->get()
            ->map(function (object $naskah): object {
                $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);

                return $naskah;
            });

        $naskahProses = $naskahList->filter(
            fn (object $naskah): bool => in_array(
                $naskah->status_naskah,
                ['Pending Review', 'Perbaikan Dikirim', 'Diterima', 'Menunggu Layout', 'Proses Layout', 'Revisi Layout'],
                true
            )
        )->values();

        $naskahPerluPerbaikan = $naskahList->filter(
            fn (object $naskah): bool => in_array($naskah->status_naskah, ['Revisi', 'Ditolak'], true)
        )->values();

        $naskahSiapDijadwalkan = $naskahList->filter(
            fn (object $naskah): bool => $naskah->status_naskah === 'Selesai Layout'
                && blank($naskah->tanggal_cetak)
        )->values();

        return view('penulis.naskah.index', compact('naskahProses', 'naskahPerluPerbaikan', 'naskahSiapDijadwalkan'));
    }

    private function resolveDisplayStatus(object $naskah): string
    {
        if ($naskah->status_naskah === 'Diterima') {
            return 'Menunggu Layout';
        }

        if ($naskah->status_naskah === 'Menunggu Layout' && filled($naskah->id_layouter)) {
            return 'Proses Layout';
        }

        if ($naskah->status_naskah === 'Selesai Layout' && blank($naskah->tanggal_cetak)) {
            return 'Menunggu Jadwal Penerbitan';
        }

        return $naskah->status_naskah;
    }

    public function create(): View
    {
        return view('penulis.naskah.create');
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Request $request, int $id): View
    {
        $penulis = Penulis::query()
            ->where('id_user', $request->user()->getKey())
            ->firstOrFail();

        $naskah = Naskah::query()
            ->where('id_naskah', $id)
            ->where('id_penulis', $penulis->getKey())
            ->firstOrFail();
        $naskah->tanggal_cetak = DB::table('jadwal_penerbitan')
            ->where('id_naskah', $naskah->getKey())
            ->value('tanggal_cetak');
        $naskah->status_tampilan = $this->resolveDisplayStatus($naskah);
        $canViewPublishedFiles = filled($naskah->tanggal_cetak);

        $versiList = VersiNaskah::query()
            ->leftJoin('users', 'users.id_user', '=', 'versi_naskah.id_user_pengunggah')
            ->where('versi_naskah.id_naskah', $naskah->getKey())
            ->select(
                'versi_naskah.*',
                'users.username as nama_pengunggah'
            )
            ->get();

        $historyItems = $versiList->map(fn (VersiNaskah $versi): object => (object) [
            'source' => 'versi',
            'ref_id' => $versi->id_versi,
            'label_versi' => 'Versi '.$versi->no_versi,
            'jenis_file' => 'Naskah Penulis',
            'uploader' => $versi->nama_pengunggah ?? $request->user()->username,
            'tanggal_upload' => $versi->tanggal_upload,
            'file_path' => $versi->file_path,
            'nama_file_asli' => $versi->nama_file_asli,
        ]);

        $reviewItems = Revisi::query()
            ->leftJoin('editor', 'editor.id_editor', '=', 'revisi.id_editor')
            ->leftJoin('users', 'users.id_user', '=', 'editor.id_user')
            ->where('revisi.id_naskah', $naskah->getKey())
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
                'uploader' => $revisi->nama_editor ?? 'Editor',
                'tanggal_upload' => $revisi->tanggal_revisi,
                'file_path' => $revisi->file_review_path,
                'nama_file_asli' => $revisi->nama_file_review_asli,
            ]);

        $historyItems = $historyItems->concat($reviewItems);

        $finalEditorFile = null;
        $layoutList = collect();

        if ($canViewPublishedFiles && filled($naskah->file_final_editor_path)) {
            $finalEditorUsername = DB::table('naskah')
                ->leftJoin('editor as assigned_editor', 'assigned_editor.id_editor', '=', 'naskah.id_editor')
                ->leftJoin('users as assigned_editor_user', 'assigned_editor_user.id_user', '=', 'assigned_editor.id_user')
                ->where('naskah.id_naskah', $naskah->getKey())
                ->select(DB::raw("COALESCE(assigned_editor_user.username, 'Editor') as username"))
                ->value('username');

            $finalEditorFile = (object) [
                'source' => 'final_editor',
                'ref_id' => $naskah->getKey(),
                'file_path' => $naskah->file_final_editor_path,
                'nama_file_asli' => $naskah->nama_file_final_editor_asli,
                'tanggal_upload' => $naskah->tanggal_file_final_editor,
            ];

            $historyItems = $historyItems->push((object) [
                'source' => 'final_editor',
                'ref_id' => $naskah->getKey(),
                'label_versi' => 'Versi Final Editor',
                'jenis_file' => 'File Final',
                'uploader' => $finalEditorUsername ?? 'Editor',
                'tanggal_upload' => $naskah->tanggal_file_final_editor,
                'file_path' => $naskah->file_final_editor_path,
                'nama_file_asli' => $naskah->nama_file_final_editor_asli,
            ]);
        }

        if ($canViewPublishedFiles) {
            $layoutList = Layout::query()
                ->leftJoin('layouter', 'layouter.id_layouter', '=', 'layout.id_layouter')
                ->leftJoin('users', 'users.id_user', '=', 'layouter.id_user')
                ->where('layout.id_naskah', $naskah->getKey())
                ->whereNotNull('layout.file_layout')
                ->orderByDesc('layout.id_layout')
                ->select(
                    'layout.id_layout',
                    'layout.file_layout',
                    'layout.nama_file_layout_asli',
                    'layout.tanggal_layout',
                    'layout.status_layout',
                    'users.username as nama_pengunggah'
                )
                ->get();

            $historyItems = $historyItems->concat(
                $layoutList->map(fn (Layout $layout): object => (object) [
                    'source' => 'layout',
                    'ref_id' => $layout->id_layout,
                    'label_versi' => 'Versi Layout',
                    'jenis_file' => 'Hasil Layout',
                    'uploader' => $layout->nama_pengunggah ?? 'Layouter',
                    'tanggal_upload' => $layout->tanggal_layout,
                    'file_path' => $layout->file_layout,
                    'nama_file_asli' => $layout->nama_file_layout_asli,
                ])
            );
        }

        $historyItems = $historyItems
            ->sortBy('tanggal_upload')
            ->values();

        $revisiList = Revisi::query()
            ->where('id_naskah', $naskah->getKey())
            ->orderByDesc('tanggal_revisi')
            ->get();

        return view('penulis.naskah.show', compact('naskah', 'versiList', 'finalEditorFile', 'layoutList', 'historyItems', 'revisiList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:255'],
            'bidang_keahlian' => ['required', 'string', 'in:SD,SMP,SMA,MI,MTS,MA,SMK'],
            'kurikulum' => ['required', 'in:Merdeka,K13'],
            'kategori_mapel' => ['required', 'in:Umum,Bahasa,Agama'],
            'mata_pelajaran' => ['nullable', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'file_naskah' => ['required', 'file', 'mimes:docx', 'max:51200'],
        ], [
            'file_naskah.mimes' => 'File naskah harus berformat DOCX.',
        ]);

        $kelasByJenjang = [
            'SD' => ['1', '2', '3', '4', '5', '6'],
            'MI' => ['1', '2', '3', '4', '5', '6'],
            'SMP' => ['7', '8', '9'],
            'MTS' => ['7', '8', '9'],
            'SMA' => ['10', '11', '12'],
            'MA' => ['10', '11', '12'],
            'SMK' => ['10', '11', '12'],
        ];

        $jenjangNaskah = strtoupper(trim($validated['bidang_keahlian']));
        $kelasNaskah = trim($validated['kelas']);

        if (! in_array($kelasNaskah, $kelasByJenjang[$jenjangNaskah] ?? [], true)) {
            return back()
                ->withErrors(['kelas' => 'Kelas tidak sesuai dengan jenjang naskah yang dipilih.'])
                ->withInput();
        }

        $user = $request->user();
        $penulis = Penulis::query()
            ->where('id_user', $user->getKey())
            ->firstOrFail();

        $filePath = null;

        try {
            $naskah = DB::transaction(function () use ($validated, $penulis, $user, $request, &$filePath): Naskah {
                $now = now();

                $naskah = Naskah::create([
                    'id_penulis' => $penulis->getKey(),
                    'judul' => $validated['judul'],
                    'kelas' => $validated['kelas'],
                    'bidang_keahlian' => $validated['bidang_keahlian'],
                    'kurikulum' => $validated['kurikulum'],
                    'kategori_mapel' => $validated['kategori_mapel'],
                    'mata_pelajaran' => $validated['mata_pelajaran'] ?? '',
                    'deskripsi' => $validated['deskripsi'] ?? '',
                    'tanggal_submit' => $now,
                    'status_naskah' => 'Pending Review',
                ]);

                $naskah->update([
                    'kode_naskah' => Naskah::generateKodeNaskah(
                        $naskah->mata_pelajaran,
                        $naskah->kelas,
                        $naskah->getKey()
                    ),
                ]);

                $filePath = $request->file('file_naskah')->store('naskah');

                VersiNaskah::create([
                    'id_naskah' => $naskah->getKey(),
                    'file_path' => $filePath,
                    'nama_file_asli' => $request->file('file_naskah')->getClientOriginalName(),
                    'no_versi' => 1,
                    'tanggal_upload' => $now,
                    'id_user_pengunggah' => $user->getKey(),
                ]);

                return $naskah;
            });
        } catch (Throwable $th) {
            if ($filePath !== null) {
                Storage::delete($filePath);
            }

            throw $th;
        }

        $adminUsers = User::query()
            ->where('role', 'admin')
            ->get();

        $adminUsers->each(function (User $admin) use ($naskah): void {
            $admin->notify(new WorkflowNotification(
                'Naskah baru menunggu review.',
                'Naskah "'.$naskah->judul.'" baru saja diunggah penulis dan menunggu review admin.',
                route('admin.naskah.index', [], false)
            ));
        });

        try {
            $tanggalSubmit = $naskah->tanggal_submit
                ? \Illuminate\Support\Carbon::parse($naskah->tanggal_submit)->format('d M Y H:i')
                : '-';

            $adminUsers->each(function (User $admin) use ($naskah, $user, $tanggalSubmit): void {
                $admin->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Naskah Baru Masuk - '.$naskah->judul,
                    greeting: 'Halo Admin,',
                    opening: 'Terdapat naskah baru yang telah dikirimkan oleh penulis melalui sistem PubliSync.',
                    details: [
                        'Kode Naskah' => $naskah->kode_naskah ?? '#'.$naskah->getKey(),
                        'Judul Naskah' => $naskah->judul,
                        'Nama Penulis' => $user->username,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Tanggal Submit' => $tanggalSubmit,
                    ],
                    bodyMessage: 'Mohon untuk meninjau data naskah tersebut dan menugaskan editor yang sesuai agar proses review dapat segera dilakukan.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Data Naskah',
                    actionUrl: route('admin.naskah.index'),
                ));
            });
        } catch (Throwable $emailException) {
            Log::warning('Gagal mengirim email workflow naskah baru.', [
                'id_naskah' => $naskah->getKey(),
                'error' => $emailException->getMessage(),
            ]);
        }

        try {
            $tanggalSubmit = $naskah->tanggal_submit
                ? \Illuminate\Support\Carbon::parse($naskah->tanggal_submit)->format('d M Y H:i')
                : '-';

            if (filled($user->email)) {
                $user->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Naskah Berhasil Dikirim - '.$naskah->judul,
                    greeting: 'Halo '.$user->username.',',
                    opening: 'Terima kasih telah mengirimkan naskah melalui sistem PubliSync. Naskah Anda telah berhasil diterima oleh sistem dan masuk ke proses peninjauan.',
                    details: [
                        'Kode Naskah' => $naskah->kode_naskah ?? '#'.$naskah->getKey(),
                        'Judul Naskah' => $naskah->judul,
                        'Jenjang' => $naskah->bidang_keahlian,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Tanggal Submit' => $tanggalSubmit,
                    ],
                    bodyMessage: 'Naskah Anda sedang masuk antrean peninjauan editor. Sistem PubliSync akan mengirimkan notifikasi email pada tahap berikutnya apabila terdapat pembaruan status, catatan revisi, proses layout, atau jadwal penerbitan.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Detail Naskah',
                    actionUrl: route('penulis.naskah.show', $naskah->getKey()),
                ));
            }
        } catch (Throwable $emailException) {
            Log::warning('Gagal mengirim email konfirmasi naskah baru ke penulis.', [
                'id_naskah' => $naskah->getKey(),
                'id_user' => $user->getKey(),
                'error' => $emailException->getMessage(),
            ]);
        }

        return redirect()
            ->route('penulis.naskah.show', $naskah->getKey())
            ->with('status', 'Naskah berhasil ditambahkan.');
    }

    public function storeRevisi(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'file_naskah' => ['required', 'file', 'mimes:docx', 'max:51200'],
            'catatan_penulis' => ['nullable', 'string', 'max:255'],
        ], [
            'file_naskah.mimes' => 'File naskah harus berformat DOCX.',
        ]);

        $user = $request->user();
        $penulis = Penulis::query()
            ->where('id_user', $user->getKey())
            ->firstOrFail();

        $naskah = Naskah::query()
            ->where('id_naskah', $id)
            ->where('id_penulis', $penulis->getKey())
            ->firstOrFail();

        abort_unless($naskah->status_naskah === 'Revisi', 403);

        $filePath = null;
        $tanggalUploadRevisi = null;

        try {
            DB::transaction(function () use ($validated, $request, $user, $naskah, &$filePath, &$tanggalUploadRevisi): void {
                $now = now();
                $tanggalUploadRevisi = $now;

                $versiTerakhir = VersiNaskah::query()
                    ->where('id_naskah', $naskah->getKey())
                    ->max('no_versi') ?? 0;

                $filePath = $request->file('file_naskah')->store('naskah');

                VersiNaskah::create([
                    'id_naskah' => $naskah->getKey(),
                    'file_path' => $filePath,
                    'nama_file_asli' => $request->file('file_naskah')->getClientOriginalName(),
                    'no_versi' => $versiTerakhir + 1,
                    'tanggal_upload' => $now,
                    'id_user_pengunggah' => $user->getKey(),
                ]);

                $revisiTerakhir = Revisi::query()
                    ->where('id_naskah', $naskah->getKey())
                    ->orderByDesc('tanggal_revisi')
                    ->first();

                if ($revisiTerakhir) {
                    $revisiTerakhir->update([
                        'catatan_penulis' => $validated['catatan_penulis'] ?? null,
                        'tanggal_revisi' => $now,
                        'status_revisi' => 'Perbaikan Dikirim',
                    ]);
                } else {
                    Revisi::create([
                        'id_naskah' => $naskah->getKey(),
                        'id_editor' => $naskah->id_editor,
                        'id_penulis' => $naskah->id_penulis,
                        'catatan_penulis' => $validated['catatan_penulis'] ?? null,
                        'tanggal_revisi' => $now,
                        'status_revisi' => 'Perbaikan Dikirim',
                    ]);
                }

                $naskah->update([
                    'status_naskah' => 'Perbaikan Dikirim',
                ]);
            });
        } catch (Throwable $th) {
            if ($filePath !== null) {
                Storage::delete($filePath);
            }

            throw $th;
        }

        $editorUser = User::query()
            ->whereHas('editor', fn ($query) => $query->where('id_editor', $naskah->id_editor))
            ->first();

        if ($editorUser) {
            $editorUser->notify(new WorkflowNotification(
                'Penulis telah mengirim revisi naskah.',
                'Penulis telah mengunggah revisi untuk naskah "'.$naskah->judul.'".',
                route('editor.naskah.show', $naskah->getKey(), false)
            ));
        }

        try {
            if ($editorUser && filled($editorUser->email)) {
                $tanggalUploadFormatted = $tanggalUploadRevisi
                    ? \Illuminate\Support\Carbon::parse($tanggalUploadRevisi)->format('d M Y H:i')
                    : now()->format('d M Y H:i');

                $editorUser->notify(new NaskahWorkflowEmailNotification(
                    subject: 'Perbaikan Naskah Dikirim - '.$naskah->judul,
                    greeting: 'Halo '.($editorUser->username ?? 'Editor').',',
                    opening: 'Penulis '.$user->username.' telah mengirimkan perbaikan untuk naskah berjudul "'.$naskah->judul.'" melalui sistem PubliSync.',
                    details: [
                        'Kode Naskah' => $naskah->kode_naskah ?? '#'.$naskah->getKey(),
                        'Judul Naskah' => $naskah->judul,
                        'Nama Penulis' => $user->username,
                        'Kelas' => $naskah->kelas,
                        'Mata Pelajaran' => $naskah->mata_pelajaran,
                        'Status' => 'Perbaikan Dikirim',
                        'Tanggal Upload Revisi' => $tanggalUploadFormatted,
                    ],
                    bodyMessage: 'Naskah tersebut siap untuk ditinjau kembali berdasarkan catatan revisi yang sebelumnya diberikan. Mohon untuk meninjau kembali naskah tersebut melalui dashboard Editor.',
                    closing: 'Terima kasih.',
                    actionText: 'Buka Detail Naskah',
                    actionUrl: route('editor.naskah.show', $naskah->getKey()),
                ));
            }
        } catch (Throwable $emailException) {
            Log::warning('Gagal mengirim email workflow perbaikan naskah.', [
                'id_naskah' => $naskah->getKey(),
                'id_editor' => $naskah->id_editor,
                'error' => $emailException->getMessage(),
            ]);
        }

        return redirect()
            ->route('penulis.naskah.show', $naskah->getKey())
            ->with('status', 'Perbaikan naskah berhasil dikirim ulang.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $penulis = Penulis::query()
            ->where('id_user', $request->user()->getKey())
            ->firstOrFail();

        $naskah = Naskah::query()
            ->where('id_naskah', $id)
            ->where('id_penulis', $penulis->getKey())
            ->firstOrFail();

        if (! $this->canDeleteInitialNaskah($naskah)) {
            return redirect()
                ->route('penulis.naskah.index')
                ->with('status', 'Naskah tidak dapat dihapus karena sudah masuk proses editor atau bukan upload awal.');
        }

        $versionPaths = VersiNaskah::query()
            ->where('id_naskah', $naskah->getKey())
            ->pluck('file_path')
            ->filter()
            ->all();

        $layoutPaths = Layout::query()
            ->where('id_naskah', $naskah->getKey())
            ->pluck('file_layout')
            ->filter()
            ->all();

        $pathsToDelete = array_values(array_unique(array_merge($versionPaths, $layoutPaths)));

        DB::transaction(function () use ($naskah): void {
            $naskah->delete();
        });

        if ($pathsToDelete !== []) {
            Storage::delete($pathsToDelete);
        }

        return redirect()
            ->route('penulis.naskah.index')
            ->with('status', 'Naskah berhasil dihapus.');
    }

    private function canDeleteInitialNaskah(Naskah $naskah): bool
    {
        if ($naskah->id_editor !== null || $naskah->status_naskah !== 'Pending Review') {
            return false;
        }

        $latestVersion = VersiNaskah::query()
            ->where('id_naskah', $naskah->getKey())
            ->max('no_versi') ?? 0;

        if ((int) $latestVersion > 1) {
            return false;
        }

        return ! Revisi::query()
            ->where('id_naskah', $naskah->getKey())
            ->exists();
    }

    public function downloadVersi(Request $request, int $id, int $versiId): StreamedResponse
    {
        $penulis = Penulis::query()
            ->where('id_user', $request->user()->getKey())
            ->firstOrFail();

        $naskah = Naskah::query()
            ->where('id_naskah', $id)
            ->where('id_penulis', $penulis->getKey())
            ->firstOrFail();

        $versi = VersiNaskah::query()
            ->where('id_versi', $versiId)
            ->where('id_naskah', $naskah->getKey())
            ->firstOrFail();

        $downloadName = $versi->nama_file_asli ?: basename($versi->file_path);

        return Storage::download($versi->file_path, $downloadName);
    }

    public function previewLayout(Request $request, int $id, int $layoutId): BinaryFileResponse
    {
        $penulis = Penulis::query()
            ->where('id_user', $request->user()->getKey())
            ->firstOrFail();

        $naskah = Naskah::query()
            ->where('id_naskah', $id)
            ->where('id_penulis', $penulis->getKey())
            ->where('status_naskah', 'Selesai Layout')
            ->firstOrFail();

        $layout = Layout::query()
            ->where('id_layout', $layoutId)
            ->where('id_naskah', $naskah->getKey())
            ->firstOrFail();

        return $this->previewPdfFile($layout->file_layout);
    }

    public function downloadLayout(Request $request, int $id, int $layoutId): StreamedResponse
    {
        $penulis = Penulis::query()
            ->where('id_user', $request->user()->getKey())
            ->firstOrFail();

        $naskah = Naskah::query()
            ->where('id_naskah', $id)
            ->where('id_penulis', $penulis->getKey())
            ->where('status_naskah', 'Selesai Layout')
            ->firstOrFail();

        $layout = Layout::query()
            ->where('id_layout', $layoutId)
            ->where('id_naskah', $naskah->getKey())
            ->firstOrFail();

        $downloadName = $layout->nama_file_layout_asli ?: basename($layout->file_layout);

        return Storage::download($layout->file_layout, $downloadName);
    }

    public function downloadReviewAttachment(Request $request, int $id, int $revisiId): StreamedResponse
    {
        $revisi = $this->getReviewAttachmentForPenulis($request, $id, $revisiId);

        return Storage::download(
            $revisi->file_review_path,
            $revisi->nama_file_review_asli ?: basename($revisi->file_review_path)
        );
    }

    public function previewReviewAttachment(Request $request, int $id, int $revisiId): BinaryFileResponse
    {
        $revisi = $this->getReviewAttachmentForPenulis($request, $id, $revisiId);

        return $this->previewPdfFile($revisi->file_review_path);
    }

    private function getReviewAttachmentForPenulis(Request $request, int $id, int $revisiId): Revisi
    {
        $penulis = Penulis::query()
            ->where('id_user', $request->user()->getKey())
            ->firstOrFail();

        $naskah = Naskah::query()
            ->where('id_naskah', $id)
            ->where('id_penulis', $penulis->getKey())
            ->firstOrFail();

        $revisi = Revisi::query()
            ->where('id_revisi', $revisiId)
            ->where('id_naskah', $naskah->getKey())
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
