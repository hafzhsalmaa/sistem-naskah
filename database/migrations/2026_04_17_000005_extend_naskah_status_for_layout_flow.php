<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE naskah
            MODIFY status_naskah ENUM(
                'Pending Review',
                'Ditolak',
                'Revisi',
                'Diterima',
                'Menunggu Layout',
                'Proses Layout',
                'Revisi Layout',
                'Selesai Layout'
            ) NOT NULL DEFAULT 'Pending Review'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE naskah
            MODIFY status_naskah ENUM(
                'Pending Review',
                'Ditolak',
                'Revisi',
                'Diterima'
            ) NOT NULL DEFAULT 'Pending Review'
        ");
    }
};
