<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('naskah', function (Blueprint $table) {
            $table->string('kategori_mapel')->nullable()->after('kurikulum');
        });

        Schema::table('editor', function (Blueprint $table) {
            $table->string('kategori_mapel')->nullable()->after('bidang_keahlian');
            $table->string('mata_pelajaran')->nullable()->after('kategori_mapel');
        });

        Schema::table('layouter', function (Blueprint $table) {
            $table->string('kategori_mapel')->nullable()->after('bidang_keahlian');
            $table->string('mata_pelajaran')->nullable()->after('kategori_mapel');
        });

        DB::table('naskah')->update([
            'kategori_mapel' => DB::raw('bidang_mapel'),
        ]);

        DB::table('editor')->update([
            'mata_pelajaran' => DB::raw('bidang_mapel'),
            'kategori_mapel' => DB::raw("
                CASE
                    WHEN bidang_mapel IN ('Bahasa Indonesia', 'Bahasa Inggris', 'Bahasa Jawa') THEN 'Bahasa'
                    WHEN bidang_mapel = 'Agama' THEN 'Agama'
                    ELSE 'Umum'
                END
            "),
        ]);

        DB::table('layouter')->update([
            'mata_pelajaran' => DB::raw('bidang_mapel'),
            'kategori_mapel' => DB::raw("
                CASE
                    WHEN bidang_mapel IN ('Bahasa Indonesia', 'Bahasa Inggris', 'Bahasa Jawa') THEN 'Bahasa'
                    WHEN bidang_mapel = 'Agama' THEN 'Agama'
                    ELSE 'Umum'
                END
            "),
        ]);

        Schema::table('naskah', function (Blueprint $table) {
            $table->dropColumn('bidang_mapel');
        });

        Schema::table('editor', function (Blueprint $table) {
            $table->dropColumn('bidang_mapel');
        });

        Schema::table('layouter', function (Blueprint $table) {
            $table->dropColumn('bidang_mapel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naskah', function (Blueprint $table) {
            $table->string('bidang_mapel')->nullable()->after('mata_pelajaran');
        });

        Schema::table('editor', function (Blueprint $table) {
            $table->string('bidang_mapel')->nullable()->after('bidang_keahlian');
        });

        Schema::table('layouter', function (Blueprint $table) {
            $table->string('bidang_mapel')->nullable()->after('bidang_keahlian');
        });

        DB::table('naskah')->update([
            'bidang_mapel' => DB::raw('kategori_mapel'),
        ]);

        DB::table('editor')->update([
            'bidang_mapel' => DB::raw('mata_pelajaran'),
        ]);

        DB::table('layouter')->update([
            'bidang_mapel' => DB::raw('mata_pelajaran'),
        ]);

        Schema::table('naskah', function (Blueprint $table) {
            $table->dropColumn('kategori_mapel');
        });

        Schema::table('editor', function (Blueprint $table) {
            $table->dropColumn(['kategori_mapel', 'mata_pelajaran']);
        });

        Schema::table('layouter', function (Blueprint $table) {
            $table->dropColumn(['kategori_mapel', 'mata_pelajaran']);
        });
    }
};
