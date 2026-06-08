<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revisi', function (Blueprint $table): void {
            if (! Schema::hasColumn('revisi', 'file_review_path')) {
                $table->string('file_review_path')->nullable()->after('catatan_penulis');
            }

            if (! Schema::hasColumn('revisi', 'nama_file_review_asli')) {
                $table->string('nama_file_review_asli')->nullable()->after('file_review_path');
            }

            if (! Schema::hasColumn('revisi', 'file_review_mime')) {
                $table->string('file_review_mime')->nullable()->after('nama_file_review_asli');
            }
        });
    }

    public function down(): void
    {
        Schema::table('revisi', function (Blueprint $table): void {
            if (Schema::hasColumn('revisi', 'file_review_mime')) {
                $table->dropColumn('file_review_mime');
            }

            if (Schema::hasColumn('revisi', 'nama_file_review_asli')) {
                $table->dropColumn('nama_file_review_asli');
            }

            if (Schema::hasColumn('revisi', 'file_review_path')) {
                $table->dropColumn('file_review_path');
            }
        });
    }
};
