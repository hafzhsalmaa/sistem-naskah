<?php

namespace Tests\Feature;

use App\Models\Naskah;
use App\Models\User;
use App\Models\VersiNaskah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PenulisNaskahTest extends TestCase
{
    use RefreshDatabase;

    public function test_penulis_can_view_create_naskah_form(): void
    {
        $user = User::factory()->create([
            'role' => 'penulis',
        ]);

        DB::table('penulis')->insert([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Penulis Uji',
            'alamat' => 'Jl. Testing',
            'profesi' => 'Guru',
            'jurusan_pendidikan' => 'Pendidikan Matematika',
            'no_hp' => '081234567890',
            'foto_profil' => 'foto.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('penulis.naskah.create'));

        $response->assertOk();
    }

    public function test_penulis_can_store_naskah_and_its_first_version(): void
    {
        Storage::fake();

        $user = User::factory()->create([
            'role' => 'penulis',
        ]);

        DB::table('penulis')->insert([
            'id_user' => $user->id_user,
            'nama_lengkap' => 'Penulis Uji',
            'alamat' => 'Jl. Testing',
            'profesi' => 'Guru',
            'jurusan_pendidikan' => 'Pendidikan Matematika',
            'no_hp' => '081234567890',
            'foto_profil' => 'foto.jpg',
        ]);

        $idPenulis = DB::table('penulis')
            ->where('id_user', $user->id_user)
            ->value('id_penulis');

        $response = $this->actingAs($user)->post(route('penulis.naskah.store'), [
            'judul' => 'Naskah Matematika',
            'kelas' => '8',
            'bidang_keahlian' => 'SMP',
            'kurikulum' => 'Merdeka',
            'kategori_mapel' => 'Umum',
            'mata_pelajaran' => 'Matematika',
            'deskripsi' => 'Deskripsi naskah.',
            'file_naskah' => UploadedFile::fake()->create(
                'naskah.docx',
                256,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ),
        ]);

        $response->assertRedirect(route('penulis.naskah.create', absolute: false));
        $response->assertSessionHas('status', 'Naskah berhasil ditambahkan.');

        $this->assertDatabaseHas('naskah', [
            'id_penulis' => $idPenulis,
            'judul' => 'Naskah Matematika',
            'kelas' => '8',
            'bidang_keahlian' => 'SMP',
            'kurikulum' => 'Merdeka',
            'kategori_mapel' => 'Umum',
            'mata_pelajaran' => 'Matematika',
            'deskripsi' => 'Deskripsi naskah.',
            'status_naskah' => 'Pending Review',
        ]);

        $naskah = Naskah::query()->where('judul', 'Naskah Matematika')->firstOrFail();
        $versi = VersiNaskah::query()->where('id_naskah', $naskah->id_naskah)->firstOrFail();

        $this->assertSame(1, $versi->no_versi);
        $this->assertSame($user->id_user, $versi->id_user_pengunggah);
        Storage::assertExists($versi->file_path);
    }

    public function test_non_penulis_cannot_access_create_naskah_form(): void
    {
        $user = User::factory()->create([
            'role' => 'editor',
        ]);

        $response = $this->actingAs($user)->get(route('penulis.naskah.create'));

        $response->assertForbidden();
    }
}
