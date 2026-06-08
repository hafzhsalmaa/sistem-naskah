<?php

namespace App\Http\Requests\Admin\Editor;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StoreEditorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
                Rule::unique(User::class, 'username'),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'no_hp' => ['required', 'string', 'max:20'],
            'bidang_keahlian' => ['required', 'string', Rule::in(['SD/MI', 'SMP/MTS', 'SMA/MA/SMK'])],
            'kategori_mapel' => ['required', 'string', Rule::in(['Umum', 'Bahasa', 'Agama'])],
            'mata_pelajaran' => ['required', 'string', Rule::in([
                'IPA',
                'IPS',
                'Matematika',
                'Bahasa Indonesia',
                'Bahasa Inggris',
                'Sejarah',
                'Agama',
                'Bahasa Jawa',
            ])],
        ];
    }
}
