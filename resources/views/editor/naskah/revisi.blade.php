@extends('layouts.app')

@section('title', 'Manajemen Revisi Editor')

@section('header')
    <div class="editor-naskah-page-header">
        <div>
            <h1>Manajemen Revisi Editor</h1>
        </div>
    </div>
@endsection

@section('content')
    @php
        $uniqueFilterValues = static fn ($items, string $key) => $items
            ->pluck($key)
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->sort()
            ->values();
        $naskahRevisiFilters = [
            'status' => $uniqueFilterValues($naskahRevisi, 'status_tampilan'),
            'penulis' => $uniqueFilterValues($naskahRevisi, 'nama_penulis'),
        ];
        $naskahSelesaiFilters = [
            'status' => $uniqueFilterValues($naskahSelesaiReview, 'status_tampilan'),
            'penulis' => $uniqueFilterValues($naskahSelesaiReview, 'nama_penulis'),
        ];
    @endphp

    <section class="editor-naskah-page">
        <div x-data="{ activeLayouterModal: null }" class="editor-naskah-shell">
            @if (session('status'))
                <div class="editor-naskah-alert" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('id_layouter'))
                <div class="editor-naskah-alert editor-naskah-alert-danger">
                    {{ $errors->first('id_layouter') }}
                </div>
            @endif

            <div class="editor-naskah-stack">
                <div id="naskah-revisi" class="editor-naskah-card" data-editor-revisi-table>
                    <div class="editor-naskah-card-header">
                        <div>
                            <p class="editor-naskah-card-title">Naskah Revisi</p>
                            <p class="editor-naskah-card-subtitle">Naskah yang sedang berjalan dalam proses revisi antara editor dan penulis.</p>
                        </div>

                        <div class="editor-naskah-toolbar">
                            <label class="editor-naskah-search-control">
                                <span class="sr-only">Cari data naskah revisi</span>
                                <input type="search" data-editor-revisi-search placeholder="Cari Data">
                            </label>

                            <div class="editor-naskah-filter-grid">
                                <select data-editor-revisi-filter="status" aria-label="Filter status naskah revisi">
                                    <option value="">Status</option>
                                    @foreach ($naskahRevisiFilters['status'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <select data-editor-revisi-filter="penulis" aria-label="Filter penulis naskah revisi">
                                    <option value="">Penulis</option>
                                    @foreach ($naskahRevisiFilters['penulis'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="button" class="editor-naskah-reset-button" data-editor-revisi-reset>Reset</button>

                            <label class="editor-naskah-page-size">
                                <span>Tampil</span>
                                <select data-editor-revisi-page-size aria-label="Jumlah data revisi per halaman">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="editor-naskah-table-wrap">
                        <table class="editor-naskah-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Naskah</th>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Tanggal Revisi Terakhir</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($naskahRevisi as $naskah)
                                    @php
                                        $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                        $tanggalRevisi = $naskah->tanggal_revisi_terakhir
                                            ? \Illuminate\Support\Carbon::parse($naskah->tanggal_revisi_terakhir)->format('d M Y H:i')
                                            : '-';
                                        $searchText = collect([
                                            $kodeNaskah,
                                            $naskah->judul,
                                            $naskah->nama_penulis,
                                            $tanggalRevisi,
                                            $naskah->status_tampilan,
                                        ])->filter()->implode(' ');
                                    @endphp
                                    <tr
                                        data-editor-revisi-row
                                        data-search="{{ $searchText }}"
                                        data-filter-status="{{ $naskah->status_tampilan }}"
                                        data-filter-penulis="{{ $naskah->nama_penulis }}"
                                    >
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="editor-naskah-code-cell">{{ $kodeNaskah }}</td>
                                        <td class="editor-naskah-title-cell">{{ $naskah->judul }}</td>
                                        <td>{{ $naskah->nama_penulis }}</td>
                                        <td class="whitespace-nowrap">{{ $tanggalRevisi }}</td>
                                        <td class="text-center">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan" />
                                        </td>
                                        <td class="text-center">
                                            <a
                                                href="{{ route('editor.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'revisi']) }}"
                                                class="editor-naskah-detail-button"
                                            >
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-editor-revisi-server-empty>
                                        <td colspan="7" class="editor-naskah-empty">
                                            Belum ada naskah revisi editor.
                                        </td>
                                    </tr>
                                @endforelse

                                <tr class="hidden" data-editor-revisi-empty>
                                    <td colspan="7" class="editor-naskah-empty">
                                        Tidak ada naskah revisi yang cocok dengan pencarian atau filter.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="editor-naskah-pagination-bar">
                        <p class="editor-naskah-pagination-info" data-editor-revisi-info>Menampilkan 0 dari 0 data</p>
                        <div class="editor-naskah-pagination" data-editor-revisi-pagination aria-label="Pagination naskah revisi"></div>
                    </div>
                </div>

                <div id="selesai-review" class="editor-naskah-card" data-editor-revisi-table>
                    <div class="editor-naskah-card-header">
                        <div>
                            <p class="editor-naskah-card-title">Selesai Review</p>
                            <p class="editor-naskah-card-subtitle">Naskah yang sudah selesai ditinjau editor dan menunggu keputusan akhir atau pengiriman ke layouter.</p>
                        </div>

                        <div class="editor-naskah-toolbar">
                            <label class="editor-naskah-search-control">
                                <span class="sr-only">Cari data selesai review</span>
                                <input type="search" data-editor-revisi-search placeholder="Cari Data">
                            </label>

                            <div class="editor-naskah-filter-grid">
                                <select data-editor-revisi-filter="status" aria-label="Filter status selesai review">
                                    <option value="">Status</option>
                                    @foreach ($naskahSelesaiFilters['status'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <select data-editor-revisi-filter="penulis" aria-label="Filter penulis selesai review">
                                    <option value="">Penulis</option>
                                    @foreach ($naskahSelesaiFilters['penulis'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="button" class="editor-naskah-reset-button" data-editor-revisi-reset>Reset</button>

                            <label class="editor-naskah-page-size">
                                <span>Tampil</span>
                                <select data-editor-revisi-page-size aria-label="Jumlah data selesai review per halaman">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="editor-naskah-table-wrap">
                        <table class="editor-naskah-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Naskah</th>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($naskahSelesaiReview as $naskah)
                                    @php
                                        $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                        $pemeriksaanAwalSelesai = (bool) $naskah->cek_kurikulum
                                            && (bool) $naskah->cek_silabus
                                            && (bool) $naskah->cek_rpp
                                            && (bool) $naskah->bebas_sara;
                                        $hasFileFinalEditor = filled($naskah->file_final_editor_path ?? null);
                                        $searchText = collect([
                                            $kodeNaskah,
                                            $naskah->judul,
                                            $naskah->nama_penulis,
                                            $naskah->status_tampilan,
                                        ])->filter()->implode(' ');
                                    @endphp
                                    <tr
                                        data-editor-revisi-row
                                        data-search="{{ $searchText }}"
                                        data-filter-status="{{ $naskah->status_tampilan }}"
                                        data-filter-penulis="{{ $naskah->nama_penulis }}"
                                    >
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="editor-naskah-code-cell">{{ $kodeNaskah }}</td>
                                        <td class="editor-naskah-title-cell">{{ $naskah->judul }}</td>
                                        <td>{{ $naskah->nama_penulis }}</td>
                                        <td class="text-center">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan" />
                                        </td>
                                        <td>
                                            <div class="editor-naskah-inline-actions">
                                                <a
                                                    href="{{ route('editor.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'revisi']) }}"
                                                    class="editor-naskah-detail-button"
                                                >
                                                    Lihat Detail
                                                </a>

                                                @if ($naskah->status_naskah === 'Diterima' && $pemeriksaanAwalSelesai && $hasFileFinalEditor)
                                                    <button
                                                        type="button"
                                                        @click="activeLayouterModal = 'layouter-{{ $naskah->id_naskah }}'"
                                                        class="editor-naskah-secondary-button"
                                                    >
                                                        Kirim ke Layouter
                                                    </button>
                                                @elseif ($naskah->status_naskah === 'Diterima' && $pemeriksaanAwalSelesai)
                                                    <button
                                                        type="button"
                                                        class="editor-naskah-secondary-button is-disabled"
                                                        disabled
                                                        title="File Final Editor belum diupload. Silakan upload file final editor melalui halaman detail naskah sebelum mengirim ke Layouter."
                                                    >
                                                        Kirim ke Layouter
                                                    </button>
                                                    <a
                                                        href="{{ route('editor.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'revisi']) }}#file-final-editor"
                                                        class="editor-naskah-warning-pill"
                                                        title="Upload File Final Editor di halaman detail naskah"
                                                    >
                                                        Upload File Final Editor
                                                    </a>
                                                @elseif ($naskah->status_naskah === 'Diterima')
                                                    <span class="editor-naskah-warning-pill">
                                                        Lengkapi Pemeriksaan Awal
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($naskah->status_naskah === 'Diterima' && $pemeriksaanAwalSelesai && $hasFileFinalEditor)
                                                <div
                                                    x-show="activeLayouterModal === 'layouter-{{ $naskah->id_naskah }}'"
                                                    x-cloak
                                                    class="editor-naskah-modal-overlay"
                                                >
                                                    <div
                                                        @click.away="activeLayouterModal = null"
                                                        class="editor-naskah-modal-card"
                                                    >
                                                        <div class="editor-naskah-modal-header">
                                                            <div>
                                                                <h2 class="editor-naskah-modal-title">Kirim Naskah ke Layouter</h2>
                                                                <p class="editor-naskah-modal-subtitle">{{ $naskah->judul }}</p>
                                                                <p class="editor-naskah-modal-meta">Penulis: {{ $naskah->nama_penulis }}</p>
                                                            </div>

                                                            <button
                                                                type="button"
                                                                @click="activeLayouterModal = null"
                                                                class="editor-naskah-modal-close"
                                                            >
                                                                <span class="sr-only">Tutup</span>
                                                                <x-icons.x-close class="h-5 w-5" />
                                                            </button>
                                                        </div>

                                                        <form
                                                            method="POST"
                                                            action="{{ route('editor.naskah.kirim-layouter', $naskah->id_naskah) }}"
                                                            class="editor-naskah-modal-form"
                                                        >
                                                            @csrf

                                                            <div class="editor-naskah-modal-info is-success">
                                                                File Final Editor sudah tersedia dan akan menjadi dokumen utama untuk Layouter.
                                                            </div>

                                                            <div class="editor-naskah-modal-field">
                                                                <label for="id_layouter_{{ $naskah->id_naskah }}">
                                                                    Pilih Layouter
                                                                </label>
                                                                <select
                                                                    id="id_layouter_{{ $naskah->id_naskah }}"
                                                                    name="id_layouter"
                                                                    required
                                                                >
                                                                    <option value="">Pilih layouter</option>
                                                                    @foreach (($layouterMap[$naskah->id_naskah] ?? collect()) as $layouter)
                                                                        <option value="{{ $layouter->id_layouter }}">
                                                                            {{ $layouter->username }} &bull; {{ $layouter->naskah_aktif_count ?? 0 }} Naskah Aktif
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="editor-naskah-modal-actions">
                                                                <button
                                                                    type="button"
                                                                    @click="activeLayouterModal = null"
                                                                    class="editor-naskah-modal-secondary"
                                                                >
                                                                    Batal
                                                                </button>
                                                                <button
                                                                    type="submit"
                                                                    class="editor-naskah-modal-primary"
                                                                >
                                                                    Kirim
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr data-editor-revisi-server-empty>
                                        <td colspan="6" class="editor-naskah-empty">
                                            Belum ada naskah selesai review.
                                        </td>
                                    </tr>
                                @endforelse

                                <tr class="hidden" data-editor-revisi-empty>
                                    <td colspan="6" class="editor-naskah-empty">
                                        Tidak ada naskah selesai review yang cocok dengan pencarian atau filter.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="editor-naskah-pagination-bar">
                        <p class="editor-naskah-pagination-info" data-editor-revisi-info>Menampilkan 0 dari 0 data</p>
                        <div class="editor-naskah-pagination" data-editor-revisi-pagination aria-label="Pagination selesai review"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
