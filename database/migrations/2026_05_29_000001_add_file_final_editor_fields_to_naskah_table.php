<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('naskah', function (Blueprint $table): void {
            if (! Schema::hasColumn('naskah', 'file_final_editor_path')) {
                $table->string('file_final_editor_path')->nullable()->after('status_naskah');
            }

            if (! Schema::hasColumn('naskah', 'nama_file_final_editor_asli')) {
                $table->string('nama_file_final_editor_asli')->nullable()->after('file_final_editor_path');
            }

            if (! Schema::hasColumn('naskah', 'tanggal_file_final_editor')) {
                $table->dateTime('tanggal_file_final_editor')->nullable()->after('nama_file_final_editor_asli');
            }

            if (! Schema::hasColumn('naskah', 'id_editor_final')) {
                $table->unsignedBigInteger('id_editor_final')->nullable()->after('tanggal_file_final_editor');
            }
        });

        Schema::table('naskah', function (Blueprint $table): void {
            if (Schema::hasColumn('naskah', 'id_editor_final')) {
                $table->foreign('id_editor_final')
                    ->references('id_editor')
                    ->on('editor')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            }
        });
    }

    public function down(): void
    {
        Schema::table('naskah', function (Blueprint $table): void {
            if (Schema::hasColumn('naskah', 'id_editor_final')) {
                $table->dropForeign(['id_editor_final']);
            }
        });

        Schema::table('naskah', function (Blueprint $table): void {
            foreach ([
                'id_editor_final',
                'tanggal_file_final_editor',
                'nama_file_final_editor_asli',
                'file_final_editor_path',
            ] as $column) {
                if (Schema::hasColumn('naskah', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
