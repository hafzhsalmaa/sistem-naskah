<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('naskah', 'id_editor_final')) {
            return;
        }

        try {
            Schema::table('naskah', function (Blueprint $table): void {
                $table->dropForeign(['id_editor_final']);
            });
        } catch (\Throwable $exception) {
            // The column may exist without its foreign key in some restored demo databases.
        }

        Schema::table('naskah', function (Blueprint $table): void {
            $table->dropColumn('id_editor_final');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('naskah', 'id_editor_final')) {
            return;
        }

        Schema::table('naskah', function (Blueprint $table): void {
            $table->unsignedBigInteger('id_editor_final')->nullable()->after('tanggal_file_final_editor');
        });

        Schema::table('naskah', function (Blueprint $table): void {
            $table->foreign('id_editor_final')
                ->references('id_editor')
                ->on('editor')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }
};
