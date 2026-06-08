@extends('layouts.app')

@section('title', 'Detail Naskah Editor')

@section('header')
    @php
        $backRoute = match (request()->query('from')) {
            'revisi' => route('editor.naskah.revisi'),
            'masuk' => route('editor.naskah.masuk'),
            default => route('editor.naskah.masuk'),
        };
    @endphp

    <div class="editor-show-header">
        <h1 class="editor-show-title">Detail Naskah Editor</h1>
        <a href="{{ $backRoute }}" class="editor-show-back-link">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <section class="editor-show-page">
        <div class="editor-show-shell">
            @php
                $pemeriksaanAwalSelesai = (bool) $naskah->cek_kurikulum
                    && (bool) $naskah->cek_silabus
                    && (bool) $naskah->cek_rpp
                    && (bool) $naskah->bebas_sara;
                $canManageFileFinalEditor = in_array($naskah->status_naskah, ['Diterima', 'Menunggu Layout', 'Proses Layout', 'Selesai Layout'], true);
                $reviewLocked = in_array($naskah->status_naskah, ['Diterima', 'Menunggu Layout', 'Proses Layout', 'Selesai Layout'], true);
                $reviewControlsDisabled = $reviewLocked || ! $pemeriksaanAwalSelesai;
                $statusReviewOptions = ['Revisi', 'Diterima'];
                $hasFileFinalEditor = filled($naskah->file_final_editor_path ?? null);
                $isPdfFileFinalEditor = $hasFileFinalEditor
                    && strtolower(pathinfo($naskah->file_final_editor_path, PATHINFO_EXTENSION)) === 'pdf';
            @endphp

            @if (session('status'))
                <div class="editor-show-alert is-success" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="editor-show-alert is-danger">
                    <ul class="editor-show-alert-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="editor-show-main-grid">
                <div class="editor-show-card">
                    <div class="editor-show-card-header">
                        <p class="editor-show-card-title">{{ $naskah->judul }}</p>
                        <p class="editor-show-card-subtitle">Penulis: {{ $naskah->nama_penulis }}</p>
                    </div>

                    <div class="editor-show-card-body">
                        <div class="editor-show-detail-grid">
                            <div class="editor-show-detail-item">
                                <p class="editor-show-detail-label">Kelas</p>
                                <p class="editor-show-detail-value">{{ $naskah->kelas }}</p>
                            </div>
                            <div class="editor-show-detail-item">
                                <p class="editor-show-detail-label">Kurikulum</p>
                                <p class="editor-show-detail-value">{{ $naskah->kurikulum }}</p>
                            </div>
                            <div class="editor-show-detail-item">
                                <p class="editor-show-detail-label">Kategori Mapel</p>
                                <p class="editor-show-detail-value">{{ $naskah->kategori_mapel }}</p>
                            </div>
                            <div class="editor-show-detail-item">
                                <p class="editor-show-detail-label">Mata Pelajaran</p>
                                <p class="editor-show-detail-value">{{ $naskah->mata_pelajaran }}</p>
                            </div>
                        </div>

                        <div class="editor-show-detail-item">
                            <p class="editor-show-detail-label">Deskripsi</p>
                            <p class="editor-show-detail-value is-long">{{ $naskah->deskripsi }}</p>
                        </div>

                        <div class="editor-show-detail-grid is-compact">
                            <div class="editor-show-detail-item">
                                <p class="editor-show-detail-label">Tanggal Upload Terbaru</p>
                                <p class="editor-show-detail-value">
                                    {{ $naskah->tanggal_upload ? \Illuminate\Support\Carbon::parse($naskah->tanggal_upload)->format('d M Y H:i') : '-' }}
                                </p>
                            </div>
                            <div class="editor-show-detail-item">
                                <p class="editor-show-detail-label">Status</p>
                                <div class="editor-show-status-wrap">
                                    <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                                </div>
                            </div>
                        </div>

                        <div class="editor-show-file-panel">
                            <p class="editor-show-detail-label">File Terbaru</p>
                            <div class="editor-show-file-row">
                                <p class="editor-show-file-name">{{ $naskah->nama_file_asli ?? basename($naskah->file_path) }}</p>
                                <div class="file-action-group">
                                    <a
                                        href="{{ route('editor.naskah.download', $naskah->id_naskah) }}"
                                        class="file-action-button file-action-button--download file-action-button--icon"
                                        title="Download file"
                                        aria-label="Download file"
                                    >
                                        <x-icons.download class="h-4 w-4" />
                                    </a>
                                    <button
                                        type="button"
                                        class="file-action-button file-action-button--preview file-action-button--icon"
                                        data-preview-unavailable
                                        title="Preview file"
                                        aria-label="Preview file"
                                    >
                                        <x-icons.eye class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        @if ($canManageFileFinalEditor)
                            <div id="file-final-editor" class="editor-show-file-panel editor-show-final-file-panel">
                                <div class="editor-show-card-header is-compact">
                                    <div>
                                        <p class="editor-show-detail-label">File Final Editor</p>
                                        <p class="editor-show-card-subtitle">
                                            Unggah file naskah final hasil penyuntingan editor. File ini menjadi dokumen utama yang akan diterima Layouter.
                                        </p>
                                    </div>
                                </div>

                                @if ($hasFileFinalEditor)
                                    <div class="editor-show-file-row">
                                        <div class="editor-show-file-meta">
                                            <p class="editor-show-file-name">{{ $naskah->nama_file_final_editor_asli ?? basename($naskah->file_final_editor_path) }}</p>
                                            <p class="editor-show-file-date">
                                                {{ $naskah->tanggal_file_final_editor ? \Illuminate\Support\Carbon::parse($naskah->tanggal_file_final_editor)->format('d M Y H:i') : '-' }}
                                            </p>
                                        </div>
                                        <div class="file-action-group">
                                            <a
                                                href="{{ route('editor.naskah.file-final.download', $naskah->id_naskah) }}"
                                                class="file-action-button file-action-button--download file-action-button--icon"
                                                title="Download file final editor"
                                                aria-label="Download file final editor"
                                            >
                                                <x-icons.download class="h-4 w-4" />
                                            </a>
                                            @if ($isPdfFileFinalEditor)
                                                <a
                                                    href="{{ route('editor.naskah.file-final.preview', $naskah->id_naskah) }}"
                                                    class="file-action-button file-action-button--preview file-action-button--icon"
                                                    target="_blank"
                                                    rel="noopener"
                                                    title="Preview file final editor"
                                                    aria-label="Preview file final editor"
                                                >
                                                    <x-icons.eye class="h-4 w-4" />
                                                </a>
                                            @else
                                                <button
                                                    type="button"
                                                    class="file-action-button file-action-button--preview file-action-button--icon"
                                                    data-preview-unavailable
                                                    title="Preview file final editor"
                                                    aria-label="Preview file final editor"
                                                >
                                                    <x-icons.eye class="h-4 w-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="editor-show-note is-warning">
                                        Upload File Final Editor terlebih dahulu sebelum mengirim naskah ke Layouter.
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('editor.naskah.file-final.store', $naskah->id_naskah) }}" enctype="multipart/form-data" class="editor-show-form editor-show-final-file-form">
                                    @csrf

                                    <div class="editor-show-form-group">
                                        <label for="file_final_editor" class="editor-show-form-label">
                                            {{ $hasFileFinalEditor ? 'Ganti File Final Editor' : 'Upload File Final Editor' }}
                                        </label>
                                        <input
                                            id="file_final_editor"
                                            name="file_final_editor"
                                            type="file"
                                            accept=".doc,.docx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                            class="editor-show-file-input"
                                            required
                                        >
                                        <p class="editor-show-form-help">Format file: DOC, DOCX, atau PDF. Maksimal 50 MB.</p>
                                        <x-input-error :messages="$errors->get('file_final_editor')" class="editor-show-form-error" />
                                    </div>

                                    <button type="submit" class="editor-show-button is-primary">
                                        {{ $hasFileFinalEditor ? 'Ganti File Final' : 'Upload File Final' }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="editor-show-side-stack">
                    <div class="editor-show-card">
                        <div class="editor-show-card-header">
                            <p class="editor-show-card-title">Pemeriksaan Awal</p>
                            <p class="editor-show-card-subtitle">Selesaikan checklist ini sebelum memberi review atau mengubah status naskah.</p>
                        </div>

                        <form method="POST" action="{{ route('editor.naskah.pemeriksaan-awal.update', $naskah->id_naskah) }}" class="editor-show-form">
                            @csrf
                            @method('PATCH')

                            <div class="editor-show-checklist">
                                <label class="editor-show-check-item">
                                    <input type="checkbox" name="cek_kurikulum" value="1" class="editor-show-checkbox" @checked(old('cek_kurikulum', $naskah->cek_kurikulum)) @disabled($reviewLocked)>
                                    <span>
                                        <span class="editor-show-check-label">Sesuai Kurikulum</span>
                                    </span>
                                </label>

                                <label class="editor-show-check-item">
                                    <input type="checkbox" name="cek_silabus" value="1" class="editor-show-checkbox" @checked(old('cek_silabus', $naskah->cek_silabus)) @disabled($reviewLocked)>
                                    <span>
                                        <span class="editor-show-check-label">Sesuai Silabus</span>
                                    </span>
                                </label>

                                <label class="editor-show-check-item">
                                    <input type="checkbox" name="cek_rpp" value="1" class="editor-show-checkbox" @checked(old('cek_rpp', $naskah->cek_rpp)) @disabled($reviewLocked)>
                                    <span>
                                        <span class="editor-show-check-label">Sesuai RPP</span>
                                    </span>
                                </label>

                                <label class="editor-show-check-item">
                                    <input type="checkbox" name="bebas_sara" value="1" class="editor-show-checkbox" @checked(old('bebas_sara', $naskah->bebas_sara)) @disabled($reviewLocked)>
                                    <span>
                                        <span class="editor-show-check-label">Bebas Unsur SARA</span>
                                    </span>
                                </label>
                            </div>

                            @if ($reviewLocked)
                                <div class="editor-show-note is-warning">
                                    Review sudah dikunci karena naskah telah masuk tahap berikutnya.
                                </div>
                            @elseif (! $pemeriksaanAwalSelesai)
                                <div class="editor-show-note is-warning">
                                    Selesaikan pemeriksaan awal terlebih dahulu.
                                </div>
                            @else
                                <div class="editor-show-note is-success">
                                    Pemeriksaan awal sudah lengkap. Editor dapat melanjutkan review naskah.
                                </div>
                            @endif

                            <button
                                type="submit"
                                class="editor-show-button {{ $reviewLocked ? 'is-disabled' : 'is-dark' }}"
                                @disabled($reviewLocked)
                            >
                                Simpan Pemeriksaan Awal
                            </button>
                        </form>
                    </div>

                    <div id="form-review" class="editor-show-card">
                        <div class="editor-show-card-header">
                            <p class="editor-show-card-title">Form Review Editor</p>
                        </div>

                        <form method="POST" action="{{ route('editor.naskah.review', $naskah->id_naskah) }}" enctype="multipart/form-data" class="editor-show-form">
                            @csrf
                            @method('PATCH')

                            <div class="editor-show-form-group">
                                <label for="status_naskah" class="editor-show-form-label">Status</label>
                                <select
                                    id="status_naskah"
                                    name="status_naskah"
                                    class="editor-show-select"
                                    @disabled($reviewControlsDisabled)
                                >
                                    @if (! in_array($naskah->status_naskah, $statusReviewOptions, true))
                                        <option value="{{ $naskah->status_naskah }}" selected>
                                            {{ $naskah->status_tampilan ?? $naskah->status_naskah }}
                                        </option>
                                    @endif
                                    <option value="Revisi" @selected(old('status_naskah', $naskah->status_naskah) === 'Revisi')>Revisi</option>
                                    <option value="Diterima" @selected(old('status_naskah', $naskah->status_naskah) === 'Diterima')>Diterima</option>
                                </select>
                            </div>

                            <div class="editor-show-form-group">
                                <label for="catatan_editor" class="editor-show-form-label">Catatan Editor</label>
                                <textarea
                                    id="catatan_editor"
                                    name="catatan_editor"
                                    rows="5"
                                    class="editor-show-textarea"
                                    @disabled($reviewControlsDisabled)
                                >{{ old('catatan_editor') }}</textarea>
                            </div>

                            <div class="editor-show-form-group">
                                <label for="file_review" class="editor-show-form-label">Lampiran Review/Revisi Editor</label>
                                <input
                                    id="file_review"
                                    name="file_review"
                                    type="file"
                                    accept=".doc,.docx,.pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                    class="editor-show-file-input"
                                    @disabled($reviewControlsDisabled)
                                >
                                <p class="editor-show-form-help">
                                    Opsional. Upload file review/anotasi editor dalam format DOC, DOCX, atau PDF. Maksimal 50 MB.
                                </p>
                                <x-input-error :messages="$errors->get('file_review')" class="editor-show-form-error" />
                            </div>

                            @if ($reviewLocked)
                                <div class="editor-show-note is-warning">
                                    Review sudah dikunci karena naskah telah masuk tahap berikutnya.
                                </div>
                            @elseif (! $pemeriksaanAwalSelesai)
                                <div class="editor-show-note is-warning">
                                    Selesaikan pemeriksaan awal terlebih dahulu.
                                </div>
                            @endif

                            <button
                                type="submit"
                                class="editor-show-button {{ $reviewControlsDisabled ? 'is-disabled' : 'is-dark' }}"
                                @disabled($reviewControlsDisabled)
                            >
                                Simpan Review
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="editor-show-card editor-show-section">
                <div class="editor-show-card-header">
                    <p class="editor-show-card-title">Riwayat File / Versi Naskah</p>
                    <p class="editor-show-card-subtitle">Mencakup naskah penulis, lampiran review editor, file final editor, dan hasil layout jika tersedia.</p>
                </div>

                <div class="editor-show-table-wrap">
                    <table class="editor-show-table">
                        <thead>
                            <tr>
                                <th>Versi</th>
                                <th>Jenis</th>
                                <th>Uploader</th>
                                <th>Tanggal Upload</th>
                                <th>Nama File</th>
                                <th>Aksi File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($historyItems as $item)
                                @php
                                    $isPdfReviewAttachment = $item->source === 'review_attachment'
                                        && strtolower(pathinfo($item->file_path, PATHINFO_EXTENSION)) === 'pdf';

                                    $downloadUrl = $item->source === 'review_attachment'
                                        ? route('editor.naskah.revisi-lampiran.download', ['id' => $naskah->id_naskah, 'revisiId' => $item->ref_id])
                                        : route('editor.naskah.download', ['id' => $naskah->id_naskah, 'versi' => $item->ref_id]);

                                    $previewUrl = $item->source === 'review_attachment'
                                        ? route('editor.naskah.revisi-lampiran.preview', ['id' => $naskah->id_naskah, 'revisiId' => $item->ref_id])
                                        : '#';
                                @endphp
                                <tr>
                                    <td class="editor-show-table-strong">{{ $item->label_versi }}</td>
                                    <td>{{ $item->jenis_file ?? '-' }}</td>
                                    <td>{{ $item->uploader ?? '-' }}</td>
                                    <td class="editor-show-table-date">
                                        {{ $item->tanggal_upload ? \Illuminate\Support\Carbon::parse($item->tanggal_upload)->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td>{{ $item->nama_file_asli ?? basename($item->file_path) }}</td>
                                    <td>
                                        <div class="file-action-group">
                                            <a
                                                href="{{ $downloadUrl }}"
                                                class="file-action-button file-action-button--download file-action-button--icon"
                                                title="Download file"
                                                aria-label="Download file"
                                            >
                                                <x-icons.download class="h-4 w-4" />
                                            </a>
                                            @if ($isPdfReviewAttachment)
                                                <a
                                                    href="{{ $previewUrl }}"
                                                    class="file-action-button file-action-button--preview file-action-button--icon"
                                                    target="_blank"
                                                    rel="noopener"
                                                    title="Preview file"
                                                    aria-label="Preview file"
                                                >
                                                    <x-icons.eye class="h-4 w-4" />
                                                </a>
                                            @else
                                                <button
                                                    type="button"
                                                    class="file-action-button file-action-button--preview file-action-button--icon"
                                                    data-preview-unavailable
                                                    title="Preview file"
                                                    aria-label="Preview file"
                                                >
                                                    <x-icons.eye class="h-4 w-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="editor-show-empty-cell">
                                        Belum ada riwayat versi naskah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="editor-show-card editor-show-section">
                <div class="editor-show-card-header">
                    <p class="editor-show-card-title">Riwayat Revisi</p>
                </div>

                <div class="editor-show-revision-list">
                    @forelse ($revisiList as $revisi)
                        @php
                            $hasReviewAttachment = filled($revisi->file_review_path);
                            $isPdfReviewAttachment = $hasReviewAttachment
                                && strtolower(pathinfo($revisi->file_review_path, PATHINFO_EXTENSION)) === 'pdf';
                        @endphp
                        <div class="editor-show-revision-card">
                            <div class="editor-show-revision-head">
                                <div class="editor-show-revision-meta">
                                    <p class="editor-show-revision-date">
                                        {{ $revisi->tanggal_revisi?->format('d M Y H:i') }}
                                    </p>
                                    <x-status-naskah-badge :status="$revisi->status_revisi" />
                                </div>
                                @if ($hasReviewAttachment)
                                    <div class="editor-show-review-attachment">
                                        <span class="editor-show-review-attachment-label">Lampiran Review</span>
                                        <div class="file-action-group">
                                            @if ($isPdfReviewAttachment)
                                                <a
                                                    href="{{ route('editor.naskah.revisi-lampiran.preview', ['id' => $naskah->id_naskah, 'revisiId' => $revisi->id_revisi]) }}"
                                                    class="file-action-button file-action-button--preview file-action-button--icon"
                                                    target="_blank"
                                                    rel="noopener"
                                                    title="Preview lampiran review"
                                                    aria-label="Preview lampiran review"
                                                >
                                                    <x-icons.eye class="h-4 w-4" />
                                                </a>
                                            @else
                                                <button
                                                    type="button"
                                                    class="file-action-button file-action-button--preview file-action-button--icon"
                                                    data-preview-unavailable
                                                    title="Preview lampiran review"
                                                    aria-label="Preview lampiran review"
                                                >
                                                    <x-icons.eye class="h-4 w-4" />
                                                </button>
                                            @endif
                                            <a
                                                href="{{ route('editor.naskah.revisi-lampiran.download', ['id' => $naskah->id_naskah, 'revisiId' => $revisi->id_revisi]) }}"
                                                class="file-action-button file-action-button--download file-action-button--icon"
                                                title="Download lampiran review"
                                                aria-label="Download lampiran review"
                                            >
                                                <x-icons.download class="h-4 w-4" />
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="editor-show-note-list">
                                <div class="editor-show-note-item">
                                    <p class="editor-show-note-label">Catatan Editor</p>
                                    <p class="editor-show-note-text">{{ $revisi->catatan_editor ?: '-' }}</p>
                                </div>
                                <div class="editor-show-note-item">
                                    <p class="editor-show-note-label">Catatan Penulis</p>
                                    <p class="editor-show-note-text">{{ $revisi->catatan_penulis ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="editor-show-empty-state">
                            Belum ada catatan revisi dari editor.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
