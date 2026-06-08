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
        Schema::table('penulis', function (Blueprint $table) {
            $table->string('jurusan_pendidikan')->nullable()->after('bidang_keahlian');
        });

        DB::table('penulis')
            ->whereNull('jurusan_pendidikan')
            ->update([
                'jurusan_pendidikan' => DB::raw('bidang_keahlian'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penulis', function (Blueprint $table) {
            $table->dropColumn('jurusan_pendidikan');
        });
    }
};
