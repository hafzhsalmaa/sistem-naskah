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
        Schema::create('admin', function (Blueprint $table) {
            $table->bigIncrements('id_admin');
            $table->unsignedBigInteger('id_user')->unique();

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('penulis', function (Blueprint $table) {
            $table->bigIncrements('id_penulis');
            $table->unsignedBigInteger('id_user')->unique();
            $table->string('nama_lengkap');
            $table->string('alamat');
            $table->enum('profesi', ['Guru', 'Dosen']);
            $table->enum('bidang_keahlian', ['SD', 'MI', 'MTS', 'SMP', 'SMA']);
            $table->string('no_hp');
            $table->string('foto_profil');

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('editor', function (Blueprint $table) {
            $table->bigIncrements('id_editor');
            $table->unsignedBigInteger('id_user')->unique();
            $table->string('nama_lengkap');
            $table->enum('bidang_keahlian', ['SD', 'MI', 'MTS', 'SMP', 'SMA']);
            $table->enum('bidang_mapel', ['Umum', 'Bahasa', 'Agama']);

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('layouter', function (Blueprint $table) {
            $table->bigIncrements('id_layouter');
            $table->unsignedBigInteger('id_user')->unique();
            $table->string('nama_lengkap');
            $table->enum('bidang_keahlian', ['SD', 'SMP', 'SMA']);
            $table->enum('bidang_mapel', ['Umum', 'Bahasa', 'Agama']);

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('naskah', function (Blueprint $table) {
            $table->bigIncrements('id_naskah');
            $table->unsignedBigInteger('id_penulis');
            $table->unsignedBigInteger('id_editor')->nullable();
            $table->unsignedBigInteger('id_layouter')->nullable();
            $table->string('judul');
            $table->string('kelas');
            $table->enum('kurikulum', ['Merdeka', 'K13']);
            $table->string('mata_pelajaran');
            $table->enum('bidang_mapel', ['Umum', 'Bahasa', 'Agama']);
            $table->string('deskripsi');
            $table->boolean('cek_kurikulum')->default(false);
            $table->boolean('cek_silabus')->default(false);
            $table->boolean('cek_rpp')->default(false);
            $table->boolean('bebas_sara')->default(false);
            $table->dateTime('tanggal_submit');
            $table->enum('status_naskah', ['Pending Review', 'Ditolak', 'Revisi', 'Diterima'])->default('Pending Review');

            $table->foreign('id_penulis')->references('id_penulis')->on('penulis')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_editor')->references('id_editor')->on('editor')->nullOnDelete()->cascadeOnUpdate();
            $table->foreign('id_layouter')->references('id_layouter')->on('layouter')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::create('versi_naskah', function (Blueprint $table) {
            $table->bigIncrements('id_versi');
            $table->unsignedBigInteger('id_naskah');
            $table->string('file_path');
            $table->integer('no_versi');
            $table->dateTime('tanggal_upload');
            $table->unsignedBigInteger('id_user_pengunggah');

            $table->foreign('id_naskah')->references('id_naskah')->on('naskah')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('id_user_pengunggah')->references('id_user')->on('users')->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::create('revisi', function (Blueprint $table) {
            $table->bigIncrements('id_revisi');
            $table->unsignedBigInteger('id_naskah');
            $table->unsignedBigInteger('id_editor');
            $table->unsignedBigInteger('id_penulis');
            $table->string('catatan_editor');
            $table->string('catatan_penulis')->nullable();
            $table->dateTime('tanggal_revisi');
            $table->enum('status_revisi', ['Pending Review', 'Ditolak', 'Revisi', 'Diterima'])->default('Pending Review');

            $table->foreign('id_naskah')->references('id_naskah')->on('naskah')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('id_editor')->references('id_editor')->on('editor')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_penulis')->references('id_penulis')->on('penulis')->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::create('layout', function (Blueprint $table) {
            $table->bigIncrements('id_layout');
            $table->unsignedBigInteger('id_naskah');
            $table->unsignedBigInteger('id_layouter');
            $table->unsignedBigInteger('id_penulis');
            $table->string('file_layout');
            $table->dateTime('tanggal_layout');
            $table->enum('status_layout', ['Proses Layout', 'Selesai'])->default('Proses Layout');

            $table->foreign('id_naskah')->references('id_naskah')->on('naskah')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('id_layouter')->references('id_layouter')->on('layouter')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('id_penulis')->references('id_penulis')->on('penulis')->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::create('jadwal_penerbitan', function (Blueprint $table) {
            $table->bigIncrements('id_jadwal');
            $table->unsignedBigInteger('id_naskah');
            $table->dateTime('tanggal_cetak');
            $table->string('catatan_admin');

            $table->foreign('id_naskah')->references('id_naskah')->on('naskah')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::create('notifikasi', function (Blueprint $table) {
            $table->bigIncrements('id_notifikasi');
            $table->unsignedBigInteger('id_user_penerima');
            $table->unsignedBigInteger('id_naskah');
            $table->string('pesan');
            $table->boolean('is_read')->default(false);
            $table->dateTime('tanggal_pesan');

            $table->foreign('id_user_penerima')->references('id_user')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('id_naskah')->references('id_naskah')->on('naskah')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
        Schema::dropIfExists('jadwal_penerbitan');
        Schema::dropIfExists('layout');
        Schema::dropIfExists('revisi');
        Schema::dropIfExists('versi_naskah');
        Schema::dropIfExists('naskah');
        Schema::dropIfExists('layouter');
        Schema::dropIfExists('editor');
        Schema::dropIfExists('penulis');
        Schema::dropIfExists('admin');
    }
};
