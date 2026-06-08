<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Editor\StoreEditorRequest;
use App\Http\Requests\Admin\Layouter\StoreLayouterRequest;
use App\Http\Requests\Admin\Penulis\StorePenulisRequest;
use App\Mail\AdminManualMessage;
use App\Models\Editor;
use App\Models\Layouter;
use App\Models\Penulis;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminManagementDataController extends Controller
{
    public function penulisIndex(): View
    {
        $penulisList = DB::table('penulis')
            ->join('users', 'users.id_user', '=', 'penulis.id_user')
            ->select(
                'penulis.id_penulis',
                'penulis.kode_penulis',
                'penulis.id_user',
                'penulis.nama_lengkap',
                'users.email',
                'penulis.no_hp',
                'penulis.alamat',
                'penulis.profesi',
                'penulis.jurusan_pendidikan'
            )
            ->orderBy('penulis.id_penulis')
            ->get();

        return view('admin.data-penulis.index', compact('penulisList'));
    }

    public function createPenulis(): View
    {
        return view('admin.data-penulis.create');
    }

    public function storePenulis(StorePenulisRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $profesiPenulis = $validated['profesi'] === 'Lainnya'
                ? $validated['profesi_lainnya']
                : $validated['profesi'];

            $user = User::create([
                'username' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'role' => 'penulis',
                'password' => Hash::make($validated['password']),
            ]);

            $penulis = Penulis::create([
                'id_user' => $user->getKey(),
                'nama_lengkap' => $validated['nama_lengkap'],
                'alamat' => $validated['alamat'],
                'profesi' => $profesiPenulis,
                'jurusan_pendidikan' => $validated['jurusan_pendidikan'],
                'no_hp' => $validated['no_hp'],
            ]);

            $penulis->update([
                'kode_penulis' => Penulis::generateKodePenulis(
                    $penulis->jurusan_pendidikan,
                    $penulis->getKey()
                ),
            ]);
        });

        return redirect()
            ->route('admin.data-penulis.index')
            ->with('status', 'Data penulis berhasil ditambahkan.');
    }

    public function editorIndex(): View
    {
        $editorList = DB::table('editor')
            ->join('users', 'users.id_user', '=', 'editor.id_user')
            ->select(
                'editor.id_editor',
                'editor.kode_editor',
                'editor.id_user',
                'editor.nama_lengkap',
                'users.email',
                'editor.no_hp',
                'editor.mata_pelajaran',
                'editor.bidang_keahlian'
            )
            ->orderBy('editor.id_editor')
            ->get();

        return view('admin.data-editor.index', compact('editorList'));
    }

    public function createEditor(): View
    {
        return view('admin.data-editor.create');
    }

    public function storeEditor(StoreEditorRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'username' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'role' => 'editor',
                'password' => Hash::make($validated['password']),
            ]);

            $editor = Editor::create([
                'id_user' => $user->getKey(),
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_hp' => $validated['no_hp'],
                'bidang_keahlian' => $validated['bidang_keahlian'],
                'kategori_mapel' => $validated['kategori_mapel'],
                'mata_pelajaran' => $validated['mata_pelajaran'],
            ]);

            $editor->update([
                'kode_editor' => Editor::generateKodeEditor(
                    $editor->mata_pelajaran,
                    $editor->bidang_keahlian,
                    $editor->getKey()
                ),
            ]);
        });

        return redirect()
            ->route('admin.data-editor.index')
            ->with('status', 'Data editor berhasil ditambahkan.');
    }

    public function layouterIndex(): View
    {
        $layouterList = DB::table('layouter')
            ->join('users', 'users.id_user', '=', 'layouter.id_user')
            ->select(
                'layouter.id_layouter',
                'layouter.kode_layouter',
                'layouter.id_user',
                'layouter.nama_lengkap',
                'users.email',
                'layouter.no_hp',
                'layouter.mata_pelajaran',
                'layouter.bidang_keahlian'
            )
            ->orderBy('layouter.id_layouter')
            ->get();

        return view('admin.data-layouter.index', compact('layouterList'));
    }

    public function createLayouter(): View
    {
        return view('admin.data-layouter.create');
    }

    public function storeLayouter(StoreLayouterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'username' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'role' => 'layouter',
                'password' => Hash::make($validated['password']),
            ]);

            $layouter = Layouter::create([
                'id_user' => $user->getKey(),
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_hp' => $validated['no_hp'],
                'bidang_keahlian' => $validated['bidang_keahlian'],
                'kategori_mapel' => $validated['kategori_mapel'],
                'mata_pelajaran' => $validated['mata_pelajaran'],
            ]);

            $layouter->update([
                'kode_layouter' => Layouter::generateKodeLayouter(
                    $layouter->mata_pelajaran,
                    $layouter->bidang_keahlian,
                    $layouter->getKey()
                ),
            ]);
        });

        return redirect()
            ->route('admin.data-layouter.index')
            ->with('status', 'Data layouter berhasil ditambahkan.');
    }

    public function editPenulis(int $id): View
    {
        $penulis = Penulis::query()
            ->join('users', 'users.id_user', '=', 'penulis.id_user')
            ->where('penulis.id_penulis', $id)
            ->select('penulis.*', 'users.email')
            ->firstOrFail();

        return view('admin.data-penulis.edit', compact('penulis'));
    }

    public function editEditor(int $id): View
    {
        $editor = Editor::query()
            ->join('users', 'users.id_user', '=', 'editor.id_user')
            ->where('editor.id_editor', $id)
            ->select('editor.*', 'users.email')
            ->firstOrFail();

        return view('admin.data-editor.edit', compact('editor'));
    }

    public function editLayouter(int $id): View
    {
        $layouter = Layouter::query()
            ->join('users', 'users.id_user', '=', 'layouter.id_user')
            ->where('layouter.id_layouter', $id)
            ->select('layouter.*', 'users.email')
            ->firstOrFail();

        return view('admin.data-layouter.edit', compact('layouter'));
    }

    public function updatePenulis(Request $request, int $id): RedirectResponse
    {
        $penulis = Penulis::query()->where('id_penulis', $id)->firstOrFail();

        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class, 'username')->ignore($penulis->id_user, 'id_user'),
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$penulis->id_user.',id_user'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'profesi' => ['required', 'string', 'max:255'],
            'jurusan_pendidikan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($penulis, $validated): void {
            User::query()->where('id_user', $penulis->id_user)->update([
                'username' => $validated['nama_lengkap'],
                'email' => $validated['email'],
            ]);

            $penulis->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'],
                'profesi' => $validated['profesi'],
                'jurusan_pendidikan' => $validated['jurusan_pendidikan'],
                'kode_penulis' => Penulis::generateKodePenulis(
                    $validated['jurusan_pendidikan'],
                    $penulis->getKey()
                ),
            ]);
        });

        return redirect()->route('admin.data-penulis.index')->with('status', 'Data penulis berhasil diperbarui.');
    }

    public function updateEditor(Request $request, int $id): RedirectResponse
    {
        $editor = Editor::query()->where('id_editor', $id)->firstOrFail();

        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class, 'username')->ignore($editor->id_user, 'id_user'),
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$editor->id_user.',id_user'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'bidang_keahlian' => ['required', 'string', Rule::in(['SD/MI', 'SMP/MTS', 'SMA/MA/SMK'])],
        ]);

        DB::transaction(function () use ($editor, $validated): void {
            User::query()->where('id_user', $editor->id_user)->update([
                'username' => $validated['nama_lengkap'],
                'email' => $validated['email'],
            ]);

            $editor->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_hp' => $validated['no_hp'],
                'mata_pelajaran' => $validated['mata_pelajaran'],
                'bidang_keahlian' => $validated['bidang_keahlian'],
                'kode_editor' => Editor::generateKodeEditor(
                    $validated['mata_pelajaran'],
                    $validated['bidang_keahlian'],
                    $editor->getKey()
                ),
            ]);
        });

        return redirect()->route('admin.data-editor.index')->with('status', 'Data editor berhasil diperbarui.');
    }

    public function updateLayouter(Request $request, int $id): RedirectResponse
    {
        $layouter = Layouter::query()->where('id_layouter', $id)->firstOrFail();

        $validated = $request->validate([
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class, 'username')->ignore($layouter->id_user, 'id_user'),
            ],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$layouter->id_user.',id_user'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'bidang_keahlian' => ['required', 'string', Rule::in(['SD/MI', 'SMP/MTS', 'SMA/MA/SMK'])],
        ]);

        DB::transaction(function () use ($layouter, $validated): void {
            User::query()->where('id_user', $layouter->id_user)->update([
                'username' => $validated['nama_lengkap'],
                'email' => $validated['email'],
            ]);

            $layouter->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_hp' => $validated['no_hp'],
                'mata_pelajaran' => $validated['mata_pelajaran'],
                'bidang_keahlian' => $validated['bidang_keahlian'],
                'kode_layouter' => Layouter::generateKodeLayouter(
                    $validated['mata_pelajaran'],
                    $validated['bidang_keahlian'],
                    $layouter->getKey()
                ),
            ]);
        });

        return redirect()->route('admin.data-layouter.index')->with('status', 'Data layouter berhasil diperbarui.');
    }

    public function destroyPenulis(int $id): RedirectResponse
    {
        $penulis = Penulis::query()->where('id_penulis', $id)->firstOrFail();
        User::query()->where('id_user', $penulis->id_user)->delete();

        return redirect()->route('admin.data-penulis.index')->with('status', 'Data penulis berhasil dihapus.');
    }

    public function destroyEditor(int $id): RedirectResponse
    {
        $editor = Editor::query()->where('id_editor', $id)->firstOrFail();
        User::query()->where('id_user', $editor->id_user)->delete();

        return redirect()->route('admin.data-editor.index')->with('status', 'Data editor berhasil dihapus.');
    }

    public function destroyLayouter(int $id): RedirectResponse
    {
        $layouter = Layouter::query()->where('id_layouter', $id)->firstOrFail();
        User::query()->where('id_user', $layouter->id_user)->delete();

        return redirect()->route('admin.data-layouter.index')->with('status', 'Data layouter berhasil dihapus.');
    }

    public function sendPenulisEmail(Request $request, int $id): RedirectResponse
    {
        $penulis = Penulis::query()
            ->join('users', 'users.id_user', '=', 'penulis.id_user')
            ->where('penulis.id_penulis', $id)
            ->select('penulis.nama_lengkap', 'users.email')
            ->firstOrFail();

        return $this->sendManualEmail($request, $penulis->email, $penulis->nama_lengkap, 'admin.data-penulis.index');
    }

    public function sendEditorEmail(Request $request, int $id): RedirectResponse
    {
        $editor = Editor::query()
            ->join('users', 'users.id_user', '=', 'editor.id_user')
            ->where('editor.id_editor', $id)
            ->select('editor.nama_lengkap', 'users.email')
            ->firstOrFail();

        return $this->sendManualEmail($request, $editor->email, $editor->nama_lengkap, 'admin.data-editor.index');
    }

    public function sendLayouterEmail(Request $request, int $id): RedirectResponse
    {
        $layouter = Layouter::query()
            ->join('users', 'users.id_user', '=', 'layouter.id_user')
            ->where('layouter.id_layouter', $id)
            ->select('layouter.nama_lengkap', 'users.email')
            ->firstOrFail();

        return $this->sendManualEmail($request, $layouter->email, $layouter->nama_lengkap, 'admin.data-layouter.index');
    }

    public function sendBulkPenulisEmail(Request $request): RedirectResponse
    {
        $ids = (array) $request->input('ids', []);
        $recipients = Penulis::query()
            ->join('users', 'users.id_user', '=', 'penulis.id_user')
            ->whereIn('penulis.id_penulis', $ids)
            ->select('penulis.nama_lengkap as recipient_name', 'users.email')
            ->get();

        return $this->sendBulkManualEmail($request, $recipients, 'admin.data-penulis.index');
    }

    public function sendBulkEditorEmail(Request $request): RedirectResponse
    {
        $ids = (array) $request->input('ids', []);
        $recipients = Editor::query()
            ->join('users', 'users.id_user', '=', 'editor.id_user')
            ->whereIn('editor.id_editor', $ids)
            ->select('editor.nama_lengkap as recipient_name', 'users.email')
            ->get();

        return $this->sendBulkManualEmail($request, $recipients, 'admin.data-editor.index');
    }

    public function sendBulkLayouterEmail(Request $request): RedirectResponse
    {
        $ids = (array) $request->input('ids', []);
        $recipients = Layouter::query()
            ->join('users', 'users.id_user', '=', 'layouter.id_user')
            ->whereIn('layouter.id_layouter', $ids)
            ->select('layouter.nama_lengkap as recipient_name', 'users.email')
            ->get();

        return $this->sendBulkManualEmail($request, $recipients, 'admin.data-layouter.index');
    }

    private function sendManualEmail(Request $request, ?string $email, string $recipientName, string $redirectRoute): RedirectResponse
    {
        $validated = $request->validate([
            'pesan' => ['required', 'string'],
        ]);

        if (! filled($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()
                ->route($redirectRoute)
                ->with('error', 'Email gagal dikirim. Alamat email penerima tidak valid.');
        }

        try {
            Mail::to($email)->send(new AdminManualMessage($recipientName, $validated['pesan']));
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim email manual admin.', [
                'recipient_email' => $email,
                'recipient_name' => $recipientName,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route($redirectRoute)
                ->with('error', 'Email gagal dikirim. Periksa konfigurasi SMTP atau alamat email penerima.');
        }

        return redirect()
            ->route($redirectRoute)
            ->with('status', 'Email berhasil dikirim.');
    }

    private function sendBulkManualEmail(Request $request, Collection $recipients, string $redirectRoute): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct'],
            'pesan' => ['required', 'string', 'max:5000'],
        ]);

        $successCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        $missingCount = max(count($validated['ids']) - $recipients->count(), 0);
        $skippedCount += $missingCount;

        foreach ($recipients as $recipient) {
            $email = $recipient->email ?? null;
            $recipientName = $recipient->recipient_name ?: 'Pengguna';

            if (! filled($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;

                continue;
            }

            try {
                Mail::to($email)->send(new AdminManualMessage($recipientName, $validated['pesan']));
                $successCount++;
            } catch (\Throwable $exception) {
                $failedCount++;

                Log::warning('Gagal mengirim email manual admin massal.', [
                    'recipient_email' => $email,
                    'recipient_name' => $recipientName,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $messageParts = [
            $successCount.' email berhasil dikirim.',
            $skippedCount.' dilewati karena email kosong/tidak valid atau data tidak ditemukan.',
            $failedCount.' gagal dikirim.',
        ];

        $flashKey = $successCount > 0 ? 'status' : 'error';

        return redirect()
            ->route($redirectRoute)
            ->with($flashKey, implode(' ', $messageParts));
    }
}
