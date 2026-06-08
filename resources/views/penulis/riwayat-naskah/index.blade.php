@extends('layouts.app')

@section('title', 'Riwayat Naskah Penulis')

@section('header')
    <div class="penulis-riwayat-page-header">
        <div>
            <h1>Riwayat Naskah Penulis</h1>
        </div>
    </div>
@endsection

@section('content')
    @php
        $uniqueFilterValues = static fn ($collection, string $key) => $collection
            ->pluck($key)
            ->filter(fn ($value) => filled($value))
            ->unique()
            ->sort()
            ->values();

        $bulanTerbitOptions = $riwayatList
            ->pluck('tanggal_cetak')
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value) => \Illuminate\Support\Carbon::parse($value)->translatedFormat('F Y'))
            ->unique()
            ->sort()
            ->values();

        $riwayatFilters = [
            'editor' => $uniqueFilterValues($riwayatList, 'nama_editor'),
            'layouter' => $uniqueFilterValues($riwayatList, 'nama_layouter'),
            'tanggal' => $bulanTerbitOptions,
            'status' => collect([
                ...$bulanTerbitOptions->map(fn ($value) => 'Terbit '.$value)->all(),
            ])->unique()->values(),
        ];
    @endphp

    <section class="penulis-riwayat-page">
        <div class="penulis-riwayat-shell">
            <div class="penulis-riwayat-card" data-penulis-riwayat-table>
                <div class="penulis-riwayat-card-header">
                    <div>
                        <p class="penulis-riwayat-card-title">Riwayat Naskah Selesai</p>
                        <p class="penulis-riwayat-card-subtitle">Naskah milik penulis yang proses utama sudah selesai dan menunggu jadwal penerbitan.</p>
                    </div>

                    <div class="penulis-riwayat-toolbar">
                        <label class="penulis-riwayat-search-control">
                            <span class="sr-only">Cari riwayat naskah penulis</span>
                            <input type="search" placeholder="Cari Data" data-penulis-riwayat-search>
                        </label>

                        <div class="penulis-riwayat-filter-grid">
                            <select data-penulis-riwayat-filter="editor" aria-label="Filter editor">
                                <option value="">Editor</option>
                                @foreach ($riwayatFilters['editor'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-penulis-riwayat-filter="layouter" aria-label="Filter layouter">
                                <option value="">Layouter</option>
                                @foreach ($riwayatFilters['layouter'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-penulis-riwayat-filter="tanggal" aria-label="Filter tanggal terbit">
                                <option value="">Tanggal Terbit</option>
                                @foreach ($riwayatFilters['tanggal'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-penulis-riwayat-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($riwayatFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="penulis-riwayat-reset-button" data-penulis-riwayat-reset>
                            Reset
                        </button>

                        <label class="penulis-riwayat-page-size">
                            <span>Tampil</span>
                            <select data-penulis-riwayat-page-size aria-label="Jumlah data per halaman">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="penulis-riwayat-table-wrap">
                    <table class="penulis-riwayat-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Naskah</th>
                                <th>Judul</th>
                                <th>Editor</th>
                                <th>Layouter</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatList as $naskah)
                                @php
                                    $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                    $tanggalTerbit = $naskah->tanggal_cetak
                                        ? \Illuminate\Support\Carbon::parse($naskah->tanggal_cetak)->translatedFormat('F Y')
                                        : '';
                                    $statusLabel = $tanggalTerbit !== ''
                                        ? 'Terbit '.$tanggalTerbit
                                        : ($naskah->status_naskah ?? '-');
                                    $searchText = collect([
                                        $kodeNaskah,
                                        $naskah->judul,
                                        $naskah->nama_editor,
                                        $naskah->nama_layouter,
                                        $statusLabel,
                                        $tanggalTerbit,
                                    ])->filter()->implode(' ');
                                @endphp

                                <tr
                                    data-penulis-riwayat-row
                                    data-search="{{ $searchText }}"
                                    data-filter-editor="{{ $naskah->nama_editor }}"
                                    data-filter-layouter="{{ $naskah->nama_layouter }}"
                                    data-filter-tanggal="{{ $tanggalTerbit }}"
                                    data-filter-status="{{ $statusLabel }}"
                                >
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="penulis-riwayat-code-cell">{{ $kodeNaskah }}</td>
                                    <td class="penulis-riwayat-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_editor ?? '-' }}</td>
                                    <td>{{ $naskah->nama_layouter ?? '-' }}</td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$statusLabel" />
                                    </td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('penulis.riwayat-naskah.show', $naskah->id_naskah) }}"
                                            class="penulis-riwayat-detail-button"
                                        >
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr data-penulis-riwayat-server-empty>
                                    <td colspan="7" class="penulis-riwayat-empty">
                                        Belum ada riwayat naskah selesai.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="penulis-riwayat-filter-empty hidden" data-penulis-riwayat-empty>
                                <td colspan="7" class="penulis-riwayat-empty">
                                    Tidak ada riwayat naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="penulis-riwayat-pagination-bar">
                    <p class="penulis-riwayat-pagination-info" data-penulis-riwayat-info>Menampilkan 0 dari 0 data</p>
                    <div class="penulis-riwayat-pagination" data-penulis-riwayat-pagination aria-label="Pagination riwayat naskah penulis"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
