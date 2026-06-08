<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Editor;
use App\Models\Layouter;
use App\Models\Penulis;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', Rule::in(['admin', 'penulis', 'editor', 'layouter'])],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'alamat' => ['nullable', 'required_if:role,penulis', 'string', 'max:255'],
            'profesi' => ['nullable', 'required_if:role,penulis', 'string', 'max:255'],
            'profesi_lainnya' => ['nullable', 'required_if:profesi,Lainnya', 'string', 'max:255'],
            'jurusan_pendidikan' => ['nullable', 'required_if:role,penulis', 'string', 'max:255'],
            'bidang_keahlian' => ['nullable', 'required_if:role,editor,layouter', 'string', Rule::in(['SD/MI', 'SMP/MTS', 'SMA/MA/SMK'])],
            'no_hp' => ['nullable', 'required_if:role,penulis,editor,layouter', 'string', 'max:20'],
            'kategori_mapel' => ['nullable', 'required_if:role,editor,layouter', 'string', Rule::in([
                'Umum',
                'Bahasa',
                'Agama',
            ])],
            'mata_pelajaran' => ['nullable', 'required_if:role,editor,layouter', 'string', Rule::in([
                'IPA',
                'IPS',
                'Matematika',
                'Bahasa Indonesia',
                'Bahasa Inggris',
                'Sejarah',
                'Agama',
                'Bahasa Jawa',
            ])],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
            ]);

            $this->createRoleRecord($user, $validated);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect(route($user->redirectRoute(), absolute: false));
    }

    protected function createRoleRecord(User $user, array $validated): void
    {
        $profesiPenulis = ($validated['profesi'] ?? null) === 'Lainnya'
            ? ($validated['profesi_lainnya'] ?? 'Lainnya')
            : ($validated['profesi'] ?? null);

        match ($user->role) {
            'admin' => Admin::query()->updateOrCreate(
                ['id_user' => $user->getKey()],
                [
                    'email' => $user->email,
                    'username' => $user->username,
                    'password' => $user->password,
                ]
            ),
            'penulis' => $this->createPenulisRecord($user, $validated, $profesiPenulis),
            'editor' => $this->createEditorRecord($user, $validated),
            'layouter' => $this->createLayouterRecord($user, $validated),
        };
    }

    private function createLayouterRecord(User $user, array $validated): Layouter
    {
        $layouter = Layouter::query()->firstOrCreate(
            ['id_user' => $user->getKey()],
            [
                'nama_lengkap' => $user->username,
                'no_hp' => $validated['no_hp'],
                'bidang_keahlian' => $validated['bidang_keahlian'],
                'kategori_mapel' => $validated['kategori_mapel'],
                'mata_pelajaran' => $validated['mata_pelajaran'],
            ]
        );

        if (! $layouter->kode_layouter) {
            $layouter->update([
                'kode_layouter' => Layouter::generateKodeLayouter(
                    $layouter->mata_pelajaran,
                    $layouter->bidang_keahlian,
                    $layouter->getKey()
                ),
            ]);
        }

        return $layouter;
    }

    private function createEditorRecord(User $user, array $validated): Editor
    {
        $editor = Editor::query()->firstOrCreate(
            ['id_user' => $user->getKey()],
            [
                'nama_lengkap' => $user->username,
                'no_hp' => $validated['no_hp'],
                'bidang_keahlian' => $validated['bidang_keahlian'],
                'kategori_mapel' => $validated['kategori_mapel'],
                'mata_pelajaran' => $validated['mata_pelajaran'],
            ]
        );

        if (! $editor->kode_editor) {
            $editor->update([
                'kode_editor' => Editor::generateKodeEditor(
                    $editor->mata_pelajaran,
                    $editor->bidang_keahlian,
                    $editor->getKey()
                ),
            ]);
        }

        return $editor;
    }

    private function createPenulisRecord(User $user, array $validated, ?string $profesiPenulis): Penulis
    {
        $penulis = Penulis::query()->firstOrCreate(
            ['id_user' => $user->getKey()],
            [
                'nama_lengkap' => $user->username,
                'alamat' => $validated['alamat'],
                'profesi' => $profesiPenulis,
                'jurusan_pendidikan' => $validated['jurusan_pendidikan'],
                'no_hp' => $validated['no_hp'],
            ]
        );

        if (! $penulis->kode_penulis) {
            $penulis->update([
                'kode_penulis' => Penulis::generateKodePenulis(
                    $penulis->jurusan_pendidikan,
                    $penulis->getKey()
                ),
            ]);
        }

        return $penulis;
    }
}
