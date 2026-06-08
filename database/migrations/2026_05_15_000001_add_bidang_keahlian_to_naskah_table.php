<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('naskah', function (Blueprint $table): void {
            $table->string('bidang_keahlian')->nullable()->after('kelas');
        });

        DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->where(function ($query): void {
                $query->whereNull('naskah.bidang_keahlian')
                    ->orWhere('naskah.bidang_keahlian', '');
            })
            ->update([
                'naskah.bidang_keahlian' => DB::raw('penulis.bidang_keahlian'),
            ]);

        DB::table('naskah')
            ->join('penulis', 'penulis.id_penulis', '=', 'naskah.id_penulis')
            ->where(function ($query): void {
                $query->whereNull('naskah.mata_pelajaran')
                    ->orWhere('naskah.mata_pelajaran', '');
            })
            ->whereNotNull('penulis.bidang_mapel')
            ->where('penulis.bidang_mapel', '<>', '')
            ->update([
                'naskah.mata_pelajaran' => DB::raw('penulis.bidang_mapel'),
            ]);
    }

    public function down(): void
    {
        Schema::table('naskah', function (Blueprint $table): void {
            $table->dropColumn('bidang_keahlian');
        });
    }
};
