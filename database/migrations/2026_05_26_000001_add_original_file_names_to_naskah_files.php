<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('versi_naskah', function (Blueprint $table): void {
            if (! Schema::hasColumn('versi_naskah', 'nama_file_asli')) {
                $table->string('nama_file_asli')->nullable()->after('file_path');
            }
        });

        Schema::table('layout', function (Blueprint $table): void {
            if (! Schema::hasColumn('layout', 'nama_file_layout_asli')) {
                $table->string('nama_file_layout_asli')->nullable()->after('file_layout');
            }
        });

        DB::table('versi_naskah')
            ->whereNull('nama_file_asli')
            ->orderBy('id_versi')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    DB::table('versi_naskah')
                        ->where('id_versi', $item->id_versi)
                        ->update([
                            'nama_file_asli' => basename((string) $item->file_path),
                        ]);
                }
            }, 'id_versi');

        DB::table('layout')
            ->whereNull('nama_file_layout_asli')
            ->orderBy('id_layout')
            ->chunkById(100, function ($items): void {
                foreach ($items as $item) {
                    DB::table('layout')
                        ->where('id_layout', $item->id_layout)
                        ->update([
                            'nama_file_layout_asli' => basename((string) $item->file_layout),
                        ]);
                }
            }, 'id_layout');
    }

    public function down(): void
    {
        Schema::table('layout', function (Blueprint $table): void {
            if (Schema::hasColumn('layout', 'nama_file_layout_asli')) {
                $table->dropColumn('nama_file_layout_asli');
            }
        });

        Schema::table('versi_naskah', function (Blueprint $table): void {
            if (Schema::hasColumn('versi_naskah', 'nama_file_asli')) {
                $table->dropColumn('nama_file_asli');
            }
        });
    }
};
