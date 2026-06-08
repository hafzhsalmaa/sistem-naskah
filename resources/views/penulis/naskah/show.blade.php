@extends('layouts.app')

@section('title', 'Detail Naskah')

@section('header')
    <div class="penulis-show-header">
        <h1 class="penulis-show-title">Detail Naskah</h1>
        <a href="{{ route('penulis.naskah.index') }}" class="penulis-show-back-link">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <section class="penulis-show-page">
        <div class="penulis-show-shell">
            @if (session('status'))
                <div class="penulis-show-alert" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            <div class="penulis-show-stack">
                <div class="penulis-show-card">
                    <div class="penulis-show-card-header">
                        <p class="penulis-show-card-title">Informasi Naskah</p>
                    </div>
                    <div class="penulis-show-detail-grid">
                        <div class="penulis-show-detail-item">
                            <dt class="penulis-show-detail-label">Judul</dt>
                            <dd class="penulis-show-detail-value">{{ $naskah->judul }}</dd>
                        </div>

                        <div class="penulis-show-detail-item">
                            <dt class="penulis-show-detail-label">Kelas</dt>
                            <dd class="penulis-show-detail-value">{{ $naskah->kelas }}</dd>
                        </div>

                        <div class="penulis-show-detail-item">
                            <dt class="penulis-show-detail-label">Kurikulum</dt>
                            <dd class="penulis-show-detail-value">{{ $naskah->kurikulum }}</dd>
                        </div>

                        <div class="penulis-show-detail-item">
                            <dt class="penulis-show-detail-label">Kategori Mapel</dt>
                            <dd class="penulis-show-detail-value">{{ $naskah->kategori_mapel }}</dd>
                        </div>

                        <div class="penulis-show-detail-item">
                            <dt class="penulis-show-detail-label">Mata Pelajaran</dt>
                            <dd class="penulis-show-detail-value">{{ $naskah->mata_pelajaran }}</dd>
                        </div>

                        <div class="penulis-show-detail-item">
                            <dt class="penulis-show-detail-label">Status Naskah</dt>
                            <dd class="penulis-show-status-wrap">
                                <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                            </dd>
                        </div>

                        <div class="penulis-show-detail-item penulis-show-detail-item-wide">
                            <dt class="penulis-show-detail-label">Deskripsi</dt>
                            <dd class="penulis-show-detail-value">{{ $naskah->deskripsi ?: '-' }}</dd>
                        </div>
                    </div>
                </div>

                <div class="penulis-show-card">
                    <div class="penulis-show-card-header">
                        <p class="penulis-show-card-title">Riwayat File / Versi Naskah</p>
                        <p class="penulis-show-card-subtitle">Mencakup naskah penulis, lampiran review editor, file final editor, dan hasil layout jika tersedia.</p>
                    </div>
                    <div class="penulis-show-table-wrap">
                        <table class="penulis-show-table">
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
                                        $isPdfPreviewable = in_array($item->source, ['review_attachment', 'final_editor', 'layout'], true)
                                            && strtolower(pathinfo($item->file_path, PATHINFO_EXTENSION)) === 'pdf';

                                        $downloadUrl = match ($item->source) {
                                            'versi' => route('penulis.naskah.versi.download', ['id' => $naskah->id_naskah, 'versiId' => $item->ref_id]),
                                            'review_attachment' => route('penulis.naskah.revisi-lampiran.download', ['id' => $naskah->id_naskah, 'revisiId' => $item->ref_id]),
                                            'final_editor' => route('penulis.riwayat-naskah.download', ['id' => $naskah->id_naskah, 'source' => 'final_editor', 'ref' => $item->ref_id]),
                                            'layout' => route('penulis.naskah.layout.download', ['id' => $naskah->id_naskah, 'layoutId' => $item->ref_id]),
                                            default => '#',
                                        };

                                        $previewUrl = match ($item->source) {
                                            'review_attachment' => route('penulis.naskah.revisi-lampiran.preview', ['id' => $naskah->id_naskah, 'revisiId' => $item->ref_id]),
                                            'final_editor' => route('penulis.riwayat-naskah.preview', ['id' => $naskah->id_naskah, 'source' => 'final_editor', 'ref' => $item->ref_id]),
                                            'layout' => route('penulis.naskah.layout.preview', ['id' => $naskah->id_naskah, 'layoutId' => $item->ref_id]),
                                            default => '#',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="penulis-show-table-strong">{{ $item->label_versi }}</td>
                                        <td>{{ $item->jenis_file ?? '-' }}</td>
                                        <td>{{ $item->uploader ?? '-' }}</td>
                                        <td class="penulis-show-table-date">
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
                                                @if ($isPdfPreviewable)
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
                                        <td colspan="6" class="penulis-show-empty-cell">
                                            Belum ada riwayat file naskah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="penulis-show-card">
                    <div class="penulis-show-card-header">
                        <p class="penulis-show-card-title">Catatan Editor</p>
                    </div>
                    <div class="penulis-show-revision-list">
                        @forelse ($revisiList as $revisi)
                            @php
                                $hasReviewAttachment = filled($revisi->file_review_path);
                                $isPdfReviewAttachment = $hasReviewAttachment
                                    && strtolower(pathinfo($revisi->file_review_path, PATHINFO_EXTENSION)) === 'pdf';
                            @endphp
                            <div class="penulis-show-revision-card">
                                <div class="penulis-show-revision-head">
                                    <div class="penulis-show-revision-meta">
                                        <p class="penulis-show-revision-date">
                                            {{ $revisi->tanggal_revisi?->format('d M Y H:i') ?? '-' }}
                                        </p>
                                        <x-status-naskah-badge :status="$revisi->status_revisi" class="status-naskah-badge--sm" />
                                    </div>
                                    @if ($hasReviewAttachment)
                                        <div class="penulis-show-review-attachment">
                                            <span class="penulis-show-review-attachment-label">Lampiran Review</span>
                                            <div class="file-action-group">
                                                @if ($isPdfReviewAttachment)
                                                    <a
                                                        href="{{ route('penulis.naskah.revisi-lampiran.preview', ['id' => $naskah->id_naskah, 'revisiId' => $revisi->id_revisi]) }}"
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
                                                    href="{{ route('penulis.naskah.revisi-lampiran.download', ['id' => $naskah->id_naskah, 'revisiId' => $revisi->id_revisi]) }}"
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
                                <div class="penulis-show-note-list">
                                    <div>
                                        <p class="penulis-show-note-label">Catatan Editor</p>
                                        <p class="penulis-show-note-text">{{ $revisi->catatan_editor ?: '-' }}</p>
                                    </div>
                                    @if ($revisi->catatan_penulis)
                                        <div>
                                            <p class="penulis-show-note-label">Catatan Penulis</p>
                                            <p class="penulis-show-note-text">{{ $revisi->catatan_penulis }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="penulis-show-empty-state">
                                Belum ada catatan revisi dari editor.
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($naskah->status_naskah === 'Revisi')
                    <div id="upload-perbaikan" class="penulis-show-card">
                        <div class="penulis-show-card-header">
                            <p class="penulis-show-card-title">Upload Perbaikan</p>
                            <p class="penulis-show-card-subtitle">Unggah file revisi terbaru untuk dikirim kembali ke editor.</p>
                        </div>
                        <div class="penulis-show-card-body">
                            <form method="POST" action="{{ route('penulis.naskah.revisi.store', $naskah->id_naskah) }}" enctype="multipart/form-data" class="penulis-show-form">
                                @csrf

                                <div class="penulis-show-form-group">
                                    <label for="file_naskah" class="penulis-show-form-label">File Naskah</label>
                                    <input
                                        id="file_naskah"
                                        name="file_naskah"
                                        type="file"
                                        accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                        class="penulis-show-file-input"
                                        required
                                    >
                                    <p class="penulis-show-form-help">Format file: DOCX. Ukuran maksimal 50 MB.</p>
                                    <x-input-error :messages="$errors->get('file_naskah')" class="penulis-show-form-error" />
                                </div>

                                <div class="penulis-show-form-group">
                                    <label for="catatan_penulis" class="penulis-show-form-label">Catatan Penulis</label>
                                    <textarea
                                        id="catatan_penulis"
                                        name="catatan_penulis"
                                        rows="4"
                                        class="penulis-show-textarea"
                                    >{{ old('catatan_penulis') }}</textarea>
                                    <p class="penulis-show-form-help">Opsional, gunakan jika ingin memberi keterangan tambahan ke editor.</p>
                                    <x-input-error :messages="$errors->get('catatan_penulis')" class="penulis-show-form-error" />
                                </div>

                                <div class="penulis-show-form-actions">
                                    <button type="submit" class="penulis-show-submit-button">
                                        Kirim Perbaikan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
