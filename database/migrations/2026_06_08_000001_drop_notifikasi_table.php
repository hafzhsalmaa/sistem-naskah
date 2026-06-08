<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifikasi')) {
            Schema::dropIfExists('notifikasi');
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table): void {
                $table->bigIncrements('id_notifikasi');
                $table->unsignedBigInteger('id_user_penerima');
                $table->unsignedBigInteger('id_naskah');
                $table->string('pesan');
                $table->boolean('is_read')->default(false);
                $table->dateTime('tanggal_pesan');

                $table->foreign('id_user_penerima')
                    ->references('id_user')
                    ->on('users')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();

                $table->foreign('id_naskah')
                    ->references('id_naskah')
                    ->on('naskah')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }
};
