<?php

namespace App\Http\Requests\Admin\Penulis;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StorePenulisRequest extends FormRequest
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
            'alamat' => ['required', 'string', 'max:255'],
            'profesi' => ['required', 'string', 'max:255'],
            'profesi_lainnya' => ['nullable', 'required_if:profesi,Lainnya', 'string', 'max:255'],
            'jurusan_pendidikan' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'profesi_lainnya.required_if' => 'Profesi manual wajib diisi jika memilih Lainnya.',
        ];
    }
}
