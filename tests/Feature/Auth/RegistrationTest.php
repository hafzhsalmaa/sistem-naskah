<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'role' => 'penulis',
            'alamat' => 'Jl. Testing',
            'profesi' => 'Guru',
            'jurusan_pendidikan' => 'Ilmu Komputer',
            'no_hp' => '081234567890',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('penulis.dashboard', absolute: false));
        $this->assertDatabaseHas((new User())->getTable(), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'role' => 'penulis',
        ]);
        $this->assertDatabaseHas('penulis', [
            'nama_lengkap' => 'testuser',
            'jurusan_pendidikan' => 'Ilmu Komputer',
        ]);
    }
}
