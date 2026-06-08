<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Editor;
use App\Models\Layouter;
use App\Models\Penulis;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $password = Hash::make('password');

            $adminUser = User::query()->updateOrCreate(
                ['email' => 'admin@publisync.test'],
                [
                    'username' => 'Admin Demo',
                    'role' => 'admin',
                    'password' => $password,
                ]
            );

            Admin::query()->updateOrCreate(
                ['id_user' => $adminUser->getKey()],
                [
                    'email' => $adminUser->email,
                    'username' => $adminUser->username,
                    'password' => $adminUser->password,
                ]
            );

            $penulisUser = User::query()->updateOrCreate(
                ['email' => 'penulis@publisync.test'],
                [
                    'username' => 'Penulis Demo',
                    'role' => 'penulis',
                    'password' => $password,
                ]
            );

            $penulis = Penulis::query()->updateOrCreate(
                ['id_user' => $penulisUser->getKey()],
                [
                    'nama_lengkap' => 'Penulis Demo',
                    'alamat' => 'Jakarta',
                    'profesi' => 'Guru',
                    'jurusan_pendidikan' => 'Pendidikan Bahasa Indonesia',
                    'no_hp' => '081234567801',
                ]
            );

            $penulis->update([
                'kode_penulis' => Penulis::generateKodePenulis(
                    $penulis->jurusan_pendidikan,
                    $penulis->getKey()
                ),
            ]);

            $editorUser = User::query()->updateOrCreate(
                ['email' => 'editor@publisync.test'],
                [
                    'username' => 'Editor Demo',
                    'role' => 'editor',
                    'password' => $password,
                ]
            );

            $editor = Editor::query()->updateOrCreate(
                ['id_user' => $editorUser->getKey()],
                [
                    'nama_lengkap' => 'Editor Demo',
                    'no_hp' => '081234567802',
                    'bidang_keahlian' => 'SD/MI',
                    'kategori_mapel' => 'Umum',
                    'mata_pelajaran' => 'IPA',
                ]
            );

            $editor->update([
                'kode_editor' => Editor::generateKodeEditor(
                    $editor->mata_pelajaran,
                    $editor->bidang_keahlian,
                    $editor->getKey()
                ),
            ]);

            $layouterUser = User::query()->updateOrCreate(
                ['email' => 'layouter@publisync.test'],
                [
                    'username' => 'Layouter Demo',
                    'role' => 'layouter',
                    'password' => $password,
                ]
            );

            $layouter = Layouter::query()->updateOrCreate(
                ['id_user' => $layouterUser->getKey()],
                [
                    'nama_lengkap' => 'Layouter Demo',
                    'no_hp' => '081234567803',
                    'bidang_keahlian' => 'SMA/MA/SMK',
                    'kategori_mapel' => 'Bahasa',
                    'mata_pelajaran' => 'Bahasa Indonesia',
                ]
            );

            $layouter->update([
                'kode_layouter' => Layouter::generateKodeLayouter(
                    $layouter->mata_pelajaran,
                    $layouter->bidang_keahlian,
                    $layouter->getKey()
                ),
            ]);
        });
    }
}
