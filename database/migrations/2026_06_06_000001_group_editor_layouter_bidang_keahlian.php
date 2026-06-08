<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['editor', 'layouter'] as $table) {
            DB::table($table)
                ->whereIn('bidang_keahlian', ['SD', 'MI'])
                ->update(['bidang_keahlian' => 'SD/MI']);

            DB::table($table)
                ->whereIn('bidang_keahlian', ['SMP', 'MTS'])
                ->update(['bidang_keahlian' => 'SMP/MTS']);

            DB::table($table)
                ->whereIn('bidang_keahlian', ['SMA', 'MA', 'SMK'])
                ->update(['bidang_keahlian' => 'SMA/MA/SMK']);
        }
    }

    public function down(): void
    {
        foreach (['editor', 'layouter'] as $table) {
            DB::table($table)
                ->where('bidang_keahlian', 'SD/MI')
                ->update(['bidang_keahlian' => 'SD']);

            DB::table($table)
                ->where('bidang_keahlian', 'SMP/MTS')
                ->update(['bidang_keahlian' => 'SMP']);

            DB::table($table)
                ->where('bidang_keahlian', 'SMA/MA/SMK')
                ->update(['bidang_keahlian' => 'SMA']);
        }
    }
};
