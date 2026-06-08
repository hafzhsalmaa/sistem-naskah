@extends('layouts.app')

@section('title', 'Detail Riwayat Naskah')

@section('header')
    <div class="editor-show-header">
        <h1 class="editor-show-title">Detail Riwayat Naskah</h1>
        <a href="{{ route('editor.riwayat-naskah.index') }}" class="editor-show-back-link">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <section class="editor-show-page">
        <div class="editor-show-shell">
            @php
                $previewRoute = str_replace('.download', '.preview', $downloadRoute);
            @endphp
            <div class="editor-show-card">
                <div class="editor-show-card-header">
                    <p class="editor-show-card-title">Informasi Naskah</p>
                    <p class="editor-show-card-subtitle">
                        Penulis: {{ $naskah->nama_penulis }} | Editor: {{ $naskah->nama_editor ?? '-' }} | Layouter: {{ $naskah->nama_layouter ?? '-' }}
                    </p>
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

                        <div class="editor-show-detail-item editor-show-detail-item-wide">
                            <p class="editor-show-detail-label">Deskripsi</p>
                            <p class="editor-show-detail-value is-long">{{ $naskah->deskripsi ?: '-' }}</p>
                        </div>

                        <div class="editor-show-detail-item">
                            <p class="editor-show-detail-label">Status</p>
                            <div class="editor-show-status-wrap">
                                <x-status-naskah-badge :status="$naskah->status_naskah" />
                            </div>
                        </div>
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
                                    $isPdfPreviewable = in_array($item->source, ['review_attachment', 'final_editor', 'layout'], true)
                                        && strtolower(pathinfo($item->file_path, PATHINFO_EXTENSION)) === 'pdf';
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
                                                href="{{ route($downloadRoute, ['id' => $naskah->id_naskah, 'source' => $item->source, 'ref' => $item->ref_id]) }}"
                                                class="file-action-button file-action-button--download file-action-button--icon"
                                                title="Download file"
                                                aria-label="Download file"
                                            >
                                                <x-icons.download class="h-4 w-4" />
                                            </a>
                                            @if ($isPdfPreviewable)
                                                <a
                                                    href="{{ route($previewRoute, ['id' => $naskah->id_naskah, 'source' => $item->source, 'ref' => $item->ref_id]) }}"
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
                                        Belum ada data versi naskah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="editor-show-card editor-show-section">
                <div class="editor-show-card-header">
                    <p class="editor-show-card-title">Riwayat Catatan / Diskusi</p>
                </div>

                <div class="editor-show-revision-list">
                    @forelse ($revisiList as $revisi)
                        <div class="editor-show-revision-card">
                            <div class="editor-show-revision-head">
                                <p class="editor-show-revision-date">
                                    {{ $revisi->tanggal_revisi?->format('d M Y H:i') ?? '-' }}
                                </p>
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
                            Tidak ada riwayat catatan atau diskusi.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
