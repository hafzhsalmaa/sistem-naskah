<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminJadwalPenerbitanController;
use App\Http\Controllers\AdminManagementDataController;
use App\Http\Controllers\AdminNaskahController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\EditorNaskahController;
use App\Http\Controllers\EditorSettingController;
use App\Http\Controllers\LayouterController;
use App\Http\Controllers\LayouterNaskahController;
use App\Http\Controllers\LayouterSettingController;
use App\Http\Controllers\NaskahController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PenulisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatNaskahController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(auth()->user()->redirectRoute())
        : view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        return redirect()->route($request->user()->redirectRoute());
    })->name('dashboard');

    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::get('/notifications/{id}', [NotificationController::class, 'redirect'])
        ->name('notifications.redirect');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin', [AdminController::class, 'index'])
        ->middleware('admin')
        ->name('admin.dashboard');

    Route::get('/admin/naskah', [AdminNaskahController::class, 'index'])
        ->middleware('admin')
        ->name('admin.naskah.index');
    Route::post('/admin/naskah/{id}/kirim', [AdminNaskahController::class, 'assignEditor'])
        ->middleware('admin')
        ->name('admin.naskah.assign-editor');
    Route::get('/admin/jadwal-penerbitan', [AdminJadwalPenerbitanController::class, 'index'])
        ->middleware('admin')
        ->name('admin.jadwal-penerbitan.index');
    Route::post('/admin/jadwal-penerbitan/{id}', [AdminJadwalPenerbitanController::class, 'store'])
        ->middleware('admin')
        ->name('admin.jadwal-penerbitan.store');
    Route::patch('/admin/jadwal-penerbitan/{id}', [AdminJadwalPenerbitanController::class, 'update'])
        ->middleware('admin')
        ->name('admin.jadwal-penerbitan.update');
    Route::get('/admin/riwayat-naskah', [AdminJadwalPenerbitanController::class, 'riwayat'])
        ->middleware('admin')
        ->name('admin.riwayat-naskah.index');
    Route::get('/admin/riwayat-naskah/{id}/download', [AdminJadwalPenerbitanController::class, 'download'])
        ->middleware('admin')
        ->name('admin.riwayat-naskah.download');
    Route::get('/admin/riwayat-naskah/{id}/preview', [AdminJadwalPenerbitanController::class, 'preview'])
        ->middleware('admin')
        ->name('admin.riwayat-naskah.preview');
    Route::get('/admin/data-penulis', [AdminManagementDataController::class, 'penulisIndex'])
        ->middleware('admin')
        ->name('admin.data-penulis.index');
    Route::get('/admin/data-penulis/create', [AdminManagementDataController::class, 'createPenulis'])
        ->middleware('admin')
        ->name('admin.data-penulis.create');
    Route::post('/admin/data-penulis', [AdminManagementDataController::class, 'storePenulis'])
        ->middleware('admin')
        ->name('admin.data-penulis.store');
    Route::get('/admin/data-penulis/{id}/edit', [AdminManagementDataController::class, 'editPenulis'])
        ->middleware('admin')
        ->name('admin.data-penulis.edit');
    Route::patch('/admin/data-penulis/{id}', [AdminManagementDataController::class, 'updatePenulis'])
        ->middleware('admin')
        ->name('admin.data-penulis.update');
    Route::delete('/admin/data-penulis/{id}', [AdminManagementDataController::class, 'destroyPenulis'])
        ->middleware('admin')
        ->name('admin.data-penulis.destroy');
    Route::post('/admin/data-penulis/email/bulk', [AdminManagementDataController::class, 'sendBulkPenulisEmail'])
        ->middleware('admin')
        ->name('admin.data-penulis.email.bulk');
    Route::post('/admin/data-penulis/{id}/email', [AdminManagementDataController::class, 'sendPenulisEmail'])
        ->middleware('admin')
        ->name('admin.data-penulis.email');
    Route::get('/admin/data-editor', [AdminManagementDataController::class, 'editorIndex'])
        ->middleware('admin')
        ->name('admin.data-editor.index');
    Route::get('/admin/data-editor/create', [AdminManagementDataController::class, 'createEditor'])
        ->middleware('admin')
        ->name('admin.data-editor.create');
    Route::post('/admin/data-editor', [AdminManagementDataController::class, 'storeEditor'])
        ->middleware('admin')
        ->name('admin.data-editor.store');
    Route::get('/admin/data-editor/{id}/edit', [AdminManagementDataController::class, 'editEditor'])
        ->middleware('admin')
        ->name('admin.data-editor.edit');
    Route::patch('/admin/data-editor/{id}', [AdminManagementDataController::class, 'updateEditor'])
        ->middleware('admin')
        ->name('admin.data-editor.update');
    Route::delete('/admin/data-editor/{id}', [AdminManagementDataController::class, 'destroyEditor'])
        ->middleware('admin')
        ->name('admin.data-editor.destroy');
    Route::post('/admin/data-editor/email/bulk', [AdminManagementDataController::class, 'sendBulkEditorEmail'])
        ->middleware('admin')
        ->name('admin.data-editor.email.bulk');
    Route::post('/admin/data-editor/{id}/email', [AdminManagementDataController::class, 'sendEditorEmail'])
        ->middleware('admin')
        ->name('admin.data-editor.email');
    Route::get('/admin/data-layouter', [AdminManagementDataController::class, 'layouterIndex'])
        ->middleware('admin')
        ->name('admin.data-layouter.index');
    Route::get('/admin/data-layouter/create', [AdminManagementDataController::class, 'createLayouter'])
        ->middleware('admin')
        ->name('admin.data-layouter.create');
    Route::post('/admin/data-layouter', [AdminManagementDataController::class, 'storeLayouter'])
        ->middleware('admin')
        ->name('admin.data-layouter.store');
    Route::get('/admin/data-layouter/{id}/edit', [AdminManagementDataController::class, 'editLayouter'])
        ->middleware('admin')
        ->name('admin.data-layouter.edit');
    Route::patch('/admin/data-layouter/{id}', [AdminManagementDataController::class, 'updateLayouter'])
        ->middleware('admin')
        ->name('admin.data-layouter.update');
    Route::delete('/admin/data-layouter/{id}', [AdminManagementDataController::class, 'destroyLayouter'])
        ->middleware('admin')
        ->name('admin.data-layouter.destroy');
    Route::post('/admin/data-layouter/email/bulk', [AdminManagementDataController::class, 'sendBulkLayouterEmail'])
        ->middleware('admin')
        ->name('admin.data-layouter.email.bulk');
    Route::post('/admin/data-layouter/{id}/email', [AdminManagementDataController::class, 'sendLayouterEmail'])
        ->middleware('admin')
        ->name('admin.data-layouter.email');

    Route::get('/penulis', [PenulisController::class, 'index'])
        ->middleware('penulis')
        ->name('penulis.dashboard');

    Route::middleware('penulis')->group(function () {
        Route::get('/penulis/naskah', [NaskahController::class, 'index'])
            ->name('penulis.naskah.index');
        Route::get('/penulis/riwayat-naskah', [RiwayatNaskahController::class, 'penulisIndex'])
            ->name('penulis.riwayat-naskah.index');
        Route::get('/penulis/riwayat-naskah/{id}', [RiwayatNaskahController::class, 'penulisShow'])
            ->name('penulis.riwayat-naskah.show');
        Route::get('/penulis/riwayat-naskah/{id}/download', [RiwayatNaskahController::class, 'penulisDownload'])
            ->name('penulis.riwayat-naskah.download');
        Route::get('/penulis/riwayat-naskah/{id}/preview', [RiwayatNaskahController::class, 'penulisPreview'])
            ->name('penulis.riwayat-naskah.preview');
        Route::get('/penulis/naskah/create', [NaskahController::class, 'create'])
            ->name('penulis.naskah.create');
        Route::post('/penulis/naskah', [NaskahController::class, 'store'])
            ->name('penulis.naskah.store');
        Route::post('/penulis/naskah/{id}/revisi', [NaskahController::class, 'storeRevisi'])
            ->name('penulis.naskah.revisi.store');
        Route::get('/penulis/naskah/{id}/revisi/{revisiId}/lampiran/download', [NaskahController::class, 'downloadReviewAttachment'])
            ->name('penulis.naskah.revisi-lampiran.download');
        Route::get('/penulis/naskah/{id}/revisi/{revisiId}/lampiran/preview', [NaskahController::class, 'previewReviewAttachment'])
            ->name('penulis.naskah.revisi-lampiran.preview');
        Route::delete('/penulis/naskah/{id}', [NaskahController::class, 'destroy'])
            ->name('penulis.naskah.destroy');
        Route::get('/penulis/naskah/{id}/versi/{versiId}/download', [NaskahController::class, 'downloadVersi'])
            ->name('penulis.naskah.versi.download');
        Route::get('/penulis/naskah/{id}/layout/{layoutId}/preview', [NaskahController::class, 'previewLayout'])
            ->name('penulis.naskah.layout.preview');
        Route::get('/penulis/naskah/{id}/layout/{layoutId}/download', [NaskahController::class, 'downloadLayout'])
            ->name('penulis.naskah.layout.download');
        Route::get('/penulis/naskah/{id}', [NaskahController::class, 'show'])
            ->name('penulis.naskah.show');
    });
    Route::get('/editor', [EditorController::class, 'index'])
        ->middleware('editor')
        ->name('editor.dashboard');
    Route::middleware('editor')->group(function () {
        Route::get('/editor/pengaturan', [EditorSettingController::class, 'index'])
            ->name('editor.pengaturan.index');
        Route::patch('/editor/pengaturan/profil', [EditorSettingController::class, 'updateProfile'])
            ->name('editor.pengaturan.profil');
        Route::patch('/editor/pengaturan/password', [EditorSettingController::class, 'updatePassword'])
            ->name('editor.pengaturan.password');
        Route::get('/editor/naskah', [EditorNaskahController::class, 'index'])
            ->name('editor.naskah.index');
        Route::get('/editor/naskah/masuk', [EditorNaskahController::class, 'masuk'])
            ->name('editor.naskah.masuk');
        Route::get('/editor/naskah/revisi', [EditorNaskahController::class, 'revisi'])
            ->name('editor.naskah.revisi');
        Route::get('/editor/riwayat-naskah', [RiwayatNaskahController::class, 'editorIndex'])
            ->name('editor.riwayat-naskah.index');
        Route::get('/editor/riwayat-naskah/{id}', [RiwayatNaskahController::class, 'editorShow'])
            ->name('editor.riwayat-naskah.show');
        Route::get('/editor/riwayat-naskah/{id}/download', [RiwayatNaskahController::class, 'editorDownload'])
            ->name('editor.riwayat-naskah.download');
        Route::get('/editor/riwayat-naskah/{id}/preview', [RiwayatNaskahController::class, 'editorPreview'])
            ->name('editor.riwayat-naskah.preview');
        Route::get('/editor/naskah/{id}', [EditorNaskahController::class, 'show'])
            ->name('editor.naskah.show');
        Route::get('/editor/naskah/{id}/download', [EditorNaskahController::class, 'download'])
            ->name('editor.naskah.download');
        Route::post('/editor/naskah/{id}/file-final', [EditorNaskahController::class, 'uploadFileFinal'])
            ->name('editor.naskah.file-final.store');
        Route::get('/editor/naskah/{id}/file-final/download', [EditorNaskahController::class, 'downloadFileFinal'])
            ->name('editor.naskah.file-final.download');
        Route::get('/editor/naskah/{id}/file-final/preview', [EditorNaskahController::class, 'previewFileFinal'])
            ->name('editor.naskah.file-final.preview');
        Route::get('/editor/naskah/{id}/revisi/{revisiId}/lampiran/download', [EditorNaskahController::class, 'downloadReviewAttachment'])
            ->name('editor.naskah.revisi-lampiran.download');
        Route::get('/editor/naskah/{id}/revisi/{revisiId}/lampiran/preview', [EditorNaskahController::class, 'previewReviewAttachment'])
            ->name('editor.naskah.revisi-lampiran.preview');
        Route::patch('/editor/naskah/{id}/pemeriksaan-awal', [EditorNaskahController::class, 'updatePemeriksaanAwal'])
            ->name('editor.naskah.pemeriksaan-awal.update');
        Route::patch('/editor/naskah/{id}/review', [EditorNaskahController::class, 'review'])
            ->name('editor.naskah.review');
        Route::post('/editor/naskah/{id}/kirim-layouter', [EditorNaskahController::class, 'kirimLayouter'])
            ->name('editor.naskah.kirim-layouter');
        Route::post('/editor/naskah/{id}/revisi', [EditorNaskahController::class, 'storeRevisi'])
            ->name('editor.naskah.revisi.store');
        Route::patch('/editor/naskah/{id}/status', [EditorNaskahController::class, 'updateStatus'])
            ->name('editor.naskah.status.update');
    });

    Route::get('/layouter', [LayouterController::class, 'index'])
        ->middleware('layouter')
        ->name('layouter.dashboard');
    Route::middleware('layouter')->group(function () {
        Route::get('/layouter/pengaturan', [LayouterSettingController::class, 'index'])
            ->name('layouter.pengaturan.index');
        Route::patch('/layouter/pengaturan/profil', [LayouterSettingController::class, 'updateProfile'])
            ->name('layouter.pengaturan.profil');
        Route::patch('/layouter/pengaturan/password', [LayouterSettingController::class, 'updatePassword'])
            ->name('layouter.pengaturan.password');
        Route::get('/layouter/naskah', [LayouterNaskahController::class, 'index'])
            ->name('layouter.naskah.index');
        Route::get('/layouter/naskah/masuk', [LayouterNaskahController::class, 'masuk'])
            ->name('layouter.naskah.masuk');
        Route::get('/layouter/naskah/layout-selesai', [LayouterNaskahController::class, 'layoutSelesai'])
            ->name('layouter.naskah.layout-selesai');
        Route::get('/layouter/riwayat-naskah', [RiwayatNaskahController::class, 'layouterIndex'])
            ->name('layouter.riwayat-naskah.index');
        Route::get('/layouter/riwayat-naskah/{id}', [RiwayatNaskahController::class, 'layouterShow'])
            ->name('layouter.riwayat-naskah.show');
        Route::get('/layouter/riwayat-naskah/{id}/download', [RiwayatNaskahController::class, 'layouterDownload'])
            ->name('layouter.riwayat-naskah.download');
        Route::get('/layouter/riwayat-naskah/{id}/preview', [RiwayatNaskahController::class, 'layouterPreview'])
            ->name('layouter.riwayat-naskah.preview');
        Route::get('/layouter/naskah/{id}/revisi/{revisiId}/lampiran/download', [LayouterNaskahController::class, 'downloadReviewAttachment'])
            ->name('layouter.naskah.revisi-lampiran.download');
        Route::get('/layouter/naskah/{id}/revisi/{revisiId}/lampiran/preview', [LayouterNaskahController::class, 'previewReviewAttachment'])
            ->name('layouter.naskah.revisi-lampiran.preview');
        Route::get('/layouter/naskah/{id}', [LayouterNaskahController::class, 'show'])
            ->name('layouter.naskah.show');
        Route::get('/layouter/naskah/{id}/download', [LayouterNaskahController::class, 'download'])
            ->name('layouter.naskah.download');
        Route::get('/layouter/naskah/{id}/preview', [LayouterNaskahController::class, 'preview'])
            ->name('layouter.naskah.preview');
        Route::patch('/layouter/naskah/{id}/mulai-layout', [LayouterNaskahController::class, 'mulaiLayout'])
            ->name('layouter.naskah.mulai-layout');
        Route::post('/layouter/naskah/{id}/upload', [LayouterNaskahController::class, 'upload'])
            ->name('layouter.naskah.upload');
        Route::patch('/layouter/naskah/{id}/selesai', [LayouterNaskahController::class, 'selesai'])
            ->name('layouter.naskah.selesai');
    });
});

require __DIR__.'/auth.php';
