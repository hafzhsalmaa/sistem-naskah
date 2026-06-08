<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penulis', function (Blueprint $table) {
            $table->string('bidang_mapel')->nullable()->after('bidang_keahlian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penulis', function (Blueprint $table) {
            $table->dropColumn('bidang_mapel');
        });
    }
};
