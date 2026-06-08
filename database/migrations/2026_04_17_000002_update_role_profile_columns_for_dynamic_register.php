<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE penulis MODIFY profesi VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE penulis MODIFY bidang_keahlian VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE penulis MODIFY foto_profil VARCHAR(255) NULL");

        DB::statement("ALTER TABLE editor MODIFY bidang_keahlian VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE editor MODIFY bidang_mapel VARCHAR(255) NOT NULL");

        DB::statement("ALTER TABLE layouter MODIFY bidang_keahlian VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE layouter MODIFY bidang_mapel VARCHAR(255) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE penulis SET profesi = 'Guru' WHERE profesi NOT IN ('Guru', 'Dosen')");
        DB::statement("UPDATE penulis SET bidang_keahlian = 'SMA' WHERE bidang_keahlian NOT IN ('SD', 'MI', 'MTS', 'SMP', 'SMA')");
        DB::statement("UPDATE penulis SET foto_profil = 'default.png' WHERE foto_profil IS NULL");

        DB::statement("UPDATE editor SET bidang_keahlian = 'SMA' WHERE bidang_keahlian NOT IN ('SD', 'MI', 'MTS', 'SMP', 'SMA')");
        DB::statement("UPDATE editor SET bidang_mapel = 'Umum' WHERE bidang_mapel NOT IN ('Umum', 'Bahasa', 'Agama')");

        DB::statement("UPDATE layouter SET bidang_keahlian = 'SMA' WHERE bidang_keahlian NOT IN ('SD', 'SMP', 'SMA')");
        DB::statement("UPDATE layouter SET bidang_mapel = 'Umum' WHERE bidang_mapel NOT IN ('Umum', 'Bahasa', 'Agama')");

        DB::statement("ALTER TABLE penulis MODIFY profesi ENUM('Guru', 'Dosen') NOT NULL");
        DB::statement("ALTER TABLE penulis MODIFY bidang_keahlian ENUM('SD', 'MI', 'MTS', 'SMP', 'SMA') NOT NULL");
        DB::statement("ALTER TABLE penulis MODIFY foto_profil VARCHAR(255) NOT NULL");

        DB::statement("ALTER TABLE editor MODIFY bidang_keahlian ENUM('SD', 'MI', 'MTS', 'SMP', 'SMA') NOT NULL");
        DB::statement("ALTER TABLE editor MODIFY bidang_mapel ENUM('Umum', 'Bahasa', 'Agama') NOT NULL");

        DB::statement("ALTER TABLE layouter MODIFY bidang_keahlian ENUM('SD', 'SMP', 'SMA') NOT NULL");
        DB::statement("ALTER TABLE layouter MODIFY bidang_mapel ENUM('Umum', 'Bahasa', 'Agama') NOT NULL");
    }
};
