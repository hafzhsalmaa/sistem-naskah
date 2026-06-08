<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class EditorSettingController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $editor = $user->editor;

        abort_if(! $editor, 404);

        return view('editor.pengaturan.index', compact('user', 'editor'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $editor = $user->editor;

        abort_if(! $editor, 404);

        $validated = $request->validate([
            'nama_pengguna' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($user->id_user, 'id_user'),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id_user, 'id_user'),
            ],
        ]);

        DB::transaction(function () use ($user, $editor, $validated): void {
            $user->username = $validated['nama_pengguna'];
            $user->email = $validated['email'];
            $user->save();

            $editor->nama_lengkap = $validated['nama_pengguna'];
            $editor->save();
        });

        return back()->with('success', 'Pengaturan profil editor berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password editor berhasil diperbarui.');
    }
}
