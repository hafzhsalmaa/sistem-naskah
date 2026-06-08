@extends('layouts.app')

@section('title', 'Detail Naskah Layouter')

@section('header')
    @php
        $backRoute = match (request()->query('from')) {
            'layout-selesai' => route('layouter.naskah.layout-selesai'),
            'masuk' => route('layouter.naskah.masuk'),
            default => route('layouter.naskah.masuk'),
        };
    @endphp

    <div class="layouter-show-header">
        <h1 class="layouter-show-title">Detail Naskah Layouter</h1>
        <a href="{{ $backRoute }}" class="layouter-show-back-link">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    @php
        $isMenungguLayout = $naskah->status_naskah === 'Menunggu Layout';
        $isProsesLayout = $naskah->status_naskah === 'Proses Layout';
        $isSelesaiLayout = $naskah->status_naskah === 'Selesai Layout';
        $canPreviewLayout = $naskah->file_layout && strtolower(pathinfo($naskah->file_layout, PATHINFO_EXTENSION)) === 'pdf';
        $canPreviewFileUtama = filled($fileUtama['path'] ?? null)
            && strtolower(pathinfo($fileUtama['path'], PATHINFO_EXTENSION)) === 'pdf';
    @endphp

    <section class="layouter-show-page">
        <div class="layouter-show-shell">
            @if (session('status'))
                <div class="layouter-show-alert is-success" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="layouter-show-alert is-danger">
                    <ul class="layouter-show-alert-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="layouter-show-main-grid">
                <div class="layouter-show-card">
                    <div class="layouter-show-card-header">
                        <p class="layouter-show-card-title">{{ $naskah->judul }}</p>
                        <p class="layouter-show-card-subtitle">Penulis: {{ $naskah->nama_penulis }} | Editor: {{ $naskah->nama_editor ?? '-' }}</p>
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
                        </div>

                        <div class="layouter-show-detail-item">
                            <p class="layouter-show-detail-label">Deskripsi</p>
                            <p class="layouter-show-detail-value is-long">{{ $naskah->deskripsi ?: '-' }}</p>
                        </div>

                        <div class="layouter-show-detail-grid is-compact">
                            <div class="layouter-show-detail-item">
                                <p class="layouter-show-detail-label">Status</p>
                                <div class="layouter-show-status-wrap">
                                    <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                                </div>
                            </div>
                            <div class="layouter-show-detail-item">
                                <p class="layouter-show-detail-label">Versi Terakhir</p>
                                <p class="layouter-show-detail-value">Versi {{ $naskah->no_versi_terbaru ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="layouter-show-file-panel">
                            <p class="layouter-show-detail-label">{{ $fileUtama['label'] }}</p>
                            <div class="layouter-show-file-row">
                                <div class="layouter-show-file-meta">
                                    <p>Nama file: {{ $fileUtama['name'] ?? '-' }}</p>
                                    <p>Tanggal upload: {{ $fileUtama['date'] ? \Illuminate\Support\Carbon::parse($fileUtama['date'])->format('d M Y H:i') : '-' }}</p>
                                </div>
                                <div class="layouter-show-file-action">
                                    <div class="file-action-group">
                                        <a
                                            href="{{ route('layouter.naskah.download', $naskah->id_naskah) }}"
                                            class="file-action-button file-action-button--download file-action-button--icon"
                                            title="Download file"
                                            aria-label="Download file"
                                        >
                                            <x-icons.download class="h-4 w-4" />
                                        </a>
                                        @if ($canPreviewFileUtama)
                                            <a
                                                href="{{ route('layouter.naskah.preview', ['id' => $naskah->id_naskah, 'source' => 'main']) }}"
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
                                </div>
                            </div>
                        </div>

                        @if ($naskah->file_layout)
                            <div class="layouter-show-layout-panel">
                                <p class="layouter-show-layout-title">Hasil Layout Terakhir</p>
                                <div class="layouter-show-file-row">
                                    <div class="layouter-show-layout-meta">
                                        <p>Nama file: {{ $naskah->nama_file_layout_asli ?? basename($naskah->file_layout) }}</p>
                                        <p>Tanggal proses/selesai: {{ $naskah->tanggal_selesai_layout ? \Illuminate\Support\Carbon::parse($naskah->tanggal_selesai_layout)->format('d M Y H:i') : '-' }}</p>
                                    </div>
                                    <div class="layouter-show-file-action">
                                        <div class="file-action-group">
                                            @if ($canPreviewLayout)
                                                <a
                                                    href="{{ route('layouter.naskah.preview', $naskah->id_naskah) }}"
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
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="layouter-show-side-stack">
                    @if ($isMenungguLayout)
                        <div class="layouter-show-card">
                            <div class="layouter-show-card-header">
                                <p class="layouter-show-card-title">Mulai Layout</p>
                            </div>

                            <div class="layouter-show-card-body">
                                <p class="layouter-show-detail-value is-long">
                                    Pindahkan naskah ini ke Manajemen Layout untuk mulai mengunggah dan mengelola hasil layout.
                                </p>
                                <form method="POST" action="{{ route('layouter.naskah.mulai-layout', $naskah->id_naskah) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="layouter-show-button is-primary"
                                    >
                                        Mulai Layout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif ($isProsesLayout)
                        <div id="upload-layout" class="layouter-show-card">
                            <div class="layouter-show-card-header">
                                <p class="layouter-show-card-title">Upload Hasil Layout</p>
                            </div>

                            <form method="POST" action="{{ route('layouter.naskah.upload', $naskah->id_naskah) }}" enctype="multipart/form-data" class="layouter-show-form">
                                @csrf

                                <div class="layouter-show-form-group">
                                    <label for="file_layout" class="layouter-show-form-label">File Layout</label>
                                    <input
                                        id="file_layout"
                                        name="file_layout"
                                        type="file"
                                        accept=".docx,.pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf"
                                        class="layouter-show-file-input"
                                        required
                                    >
                                    <p class="layouter-show-form-help">Format file: DOCX atau PDF. Maksimal 50 MB.</p>
                                </div>

                                <button
                                    type="submit"
                                    class="layouter-show-button is-primary"
                                >
                                    Upload Hasil Layout
                                </button>
                            </form>
                        </div>

                        <div class="layouter-show-card">
                            <div class="layouter-show-card-header">
                                <p class="layouter-show-card-title">Selesaikan Layout</p>
                            </div>

                            <div class="layouter-show-card-body">
                                <form method="POST" action="{{ route('layouter.naskah.selesai', $naskah->id_naskah) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="layouter-show-button is-success"
                                    >
                                        Selesaikan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @elseif ($isSelesaiLayout)
                        <div class="layouter-show-card">
                            <div class="layouter-show-card-header">
                                <p class="layouter-show-card-title">Layout Selesai</p>
                            </div>

                            <div class="layouter-show-card-body">
                                <div class="layouter-show-file-meta">
                                    <p>File layout terakhir: {{ $naskah->file_layout ? ($naskah->nama_file_layout_asli ?? basename($naskah->file_layout)) : '-' }}</p>
                                    <p>Tanggal selesai: {{ $naskah->tanggal_selesai_layout ? \Illuminate\Support\Carbon::parse($naskah->tanggal_selesai_layout)->format('d M Y H:i') : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
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

                                    $downloadUrl = match ($item->source) {
                                        'versi' => route('layouter.naskah.download', ['id' => $naskah->id_naskah, 'versi' => $item->ref_id]),
                                        'review_attachment' => route('layouter.naskah.revisi-lampiran.download', ['id' => $naskah->id_naskah, 'revisiId' => $item->ref_id]),
                                        'final_editor' => route('layouter.naskah.download', ['id' => $naskah->id_naskah, 'source' => 'main']),
                                        'layout' => route('layouter.naskah.download', ['id' => $naskah->id_naskah, 'source' => 'layout']),
                                        default => '#',
                                    };

                                    $previewUrl = match ($item->source) {
                                        'review_attachment' => route('layouter.naskah.revisi-lampiran.preview', ['id' => $naskah->id_naskah, 'revisiId' => $item->ref_id]),
                                        'final_editor' => route('layouter.naskah.preview', ['id' => $naskah->id_naskah, 'source' => 'main']),
                                        'layout' => route('layouter.naskah.preview', ['id' => $naskah->id_naskah, 'source' => 'layout']),
                                        default => '#',
                                    };
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
                                    <td colspan="6" class="layouter-show-empty-cell">
                                        Belum ada riwayat versi naskah.
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
