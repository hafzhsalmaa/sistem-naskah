<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('editor', function (Blueprint $table): void {
            $table->string('no_hp')->nullable()->after('nama_lengkap');
        });

        Schema::table('layouter', function (Blueprint $table): void {
            $table->string('no_hp')->nullable()->after('nama_lengkap');
        });
    }

    public function down(): void
    {
        Schema::table('editor', function (Blueprint $table): void {
            $table->dropColumn('no_hp');
        });

        Schema::table('layouter', function (Blueprint $table): void {
            $table->dropColumn('no_hp');
        });
    }
};
