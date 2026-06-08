@extends('layouts.app')

@section('title', 'Detail Riwayat Naskah')

@section('header')
    <div class="penulis-show-header">
        <h1 class="penulis-show-title">Detail Riwayat Naskah</h1>
        <a href="{{ route('penulis.riwayat-naskah.index') }}" class="penulis-show-back-link">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <section class="penulis-show-page">
        <div class="penulis-show-shell">
            @php
                $jadwalTerbit = $naskah->tanggal_cetak
                    ? \Illuminate\Support\Carbon::parse($naskah->tanggal_cetak)->translatedFormat('F Y')
                    : null;
                $statusRiwayat = $jadwalTerbit ? 'Terbit '.$jadwalTerbit : $naskah->status_naskah;
                $previewRoute = str_replace('.download', '.preview', $downloadRoute);
            @endphp

            <div class="penulis-show-stack">
                <div class="penulis-show-card">
                    <div class="penulis-show-card-header">
                        <p class="penulis-show-card-title">Informasi Naskah</p>
                        <p class="penulis-show-card-subtitle">
                            Penulis: {{ $naskah->nama_penulis }} | Editor: {{ $naskah->nama_editor ?? '-' }} | Layouter: {{ $naskah->nama_layouter ?? '-' }}
                        </p>
                    </div>
                    <div class="penulis-show-detail-grid">
                        <div class="penulis-show-detail-item">
                            <dt class="penulis-show-detail-label">Kode Naskah</dt>
                            <dd class="penulis-show-detail-value">{{ $naskah->kode_naskah ?? '#'.$naskah->id_naskah }}</dd>
                        </div>

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
                                <x-status-naskah-badge :status="$statusRiwayat" />
                            </dd>
                        </div>

                        <div class="penulis-show-detail-item">
                            <dt class="penulis-show-detail-label">Jadwal Terbit</dt>
                            <dd class="penulis-show-detail-value">{{ $jadwalTerbit ?? 'Belum dijadwalkan' }}</dd>
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
                                        <td colspan="6" class="penulis-show-empty-cell">
                                            Belum ada data versi naskah.
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
                            <div class="penulis-show-revision-card">
                                <div class="penulis-show-revision-head">
                                    <p class="penulis-show-revision-date">
                                        {{ $revisi->tanggal_revisi?->format('d M Y H:i') ?? '-' }}
                                    </p>
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
                                Tidak ada riwayat catatan atau diskusi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
