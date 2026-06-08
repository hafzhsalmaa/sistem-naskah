@extends('layouts.app')

@section('title', 'Detail Riwayat Naskah')

@section('header')
    <div class="layouter-show-header">
        <h1 class="layouter-show-title">Detail Riwayat Naskah</h1>
        <a href="{{ route('layouter.riwayat-naskah.index') }}" class="layouter-show-back-link">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <section class="layouter-show-page">
        <div class="layouter-show-shell">
            @php
                $previewRoute = str_replace('.download', '.preview', $downloadRoute);
            @endphp
            <div class="layouter-show-card">
                <div class="layouter-show-card-header">
                    <p class="layouter-show-card-title">Informasi Naskah</p>
                    <p class="layouter-show-card-subtitle">
                        Penulis: {{ $naskah->nama_penulis }} | Editor: {{ $naskah->nama_editor ?? '-' }} | Layouter: {{ $naskah->nama_layouter ?? '-' }}
                    </p>
                </div>

                <div class="layouter-show-card-body">
                    <div class="layouter-show-detail-grid">
                        <div class="layouter-show-detail-item">
                            <p class="layouter-show-detail-label">Kelas</p>
                            <p class="layouter-show-detail-value">{{ $naskah->kelas }}</p>
                        </div>

                        <div class="layouter-show-detail-item">
                            <p class="layouter-show-detail-label">Kurikulum</p>
                            <p class="layouter-show-detail-value">{{ $naskah->kurikulum }}</p>
                        </div>

                        <div class="layouter-show-detail-item">
                            <p class="layouter-show-detail-label">Kategori Mapel</p>
                            <p class="layouter-show-detail-value">{{ $naskah->kategori_mapel }}</p>
                        </div>

                        <div class="layouter-show-detail-item">
                            <p class="layouter-show-detail-label">Mata Pelajaran</p>
                            <p class="layouter-show-detail-value">{{ $naskah->mata_pelajaran }}</p>
                        </div>

                        <div class="layouter-show-detail-item layouter-show-detail-item-wide">
                            <p class="layouter-show-detail-label">Deskripsi</p>
                            <p class="layouter-show-detail-value is-long">{{ $naskah->deskripsi ?: '-' }}</p>
                        </div>

                        <div class="layouter-show-detail-item">
                            <p class="layouter-show-detail-label">Status</p>
                            <div class="layouter-show-status-wrap">
                                <x-status-naskah-badge :status="$naskah->status_naskah" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layouter-show-card layouter-show-section">
                <div class="layouter-show-card-header">
                    <p class="layouter-show-card-title">Riwayat File / Versi Naskah</p>
                    <p class="layouter-show-card-subtitle">Mencakup naskah penulis, lampiran review editor, file final editor, dan hasil layout jika tersedia.</p>
                </div>

                <div class="layouter-show-table-wrap">
                    <table class="layouter-show-table">
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
                                    <td class="layouter-show-table-strong">{{ $item->label_versi }}</td>
                                    <td>{{ $item->jenis_file ?? '-' }}</td>
                                    <td>{{ $item->uploader ?? '-' }}</td>
                                    <td class="layouter-show-table-date">
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
                                    <td colspan="6" class="layouter-show-empty-cell">
                                        Belum ada data versi naskah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
