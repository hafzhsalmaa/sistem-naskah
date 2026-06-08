<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penulis', function (Blueprint $table): void {
            if (Schema::hasColumn('penulis', 'bidang_keahlian')) {
                $table->dropColumn('bidang_keahlian');
            }

            if (Schema::hasColumn('penulis', 'bidang_mapel')) {
                $table->dropColumn('bidang_mapel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penulis', function (Blueprint $table): void {
            if (! Schema::hasColumn('penulis', 'bidang_keahlian')) {
                $table->string('bidang_keahlian')->nullable()->after('profesi');
            }

            if (! Schema::hasColumn('penulis', 'bidang_mapel')) {
                $table->string('bidang_mapel')->nullable()->after('jurusan_pendidikan');
            }
        });
    }
};
