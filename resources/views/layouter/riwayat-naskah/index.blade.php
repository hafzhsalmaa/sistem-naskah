@extends('layouts.app')

@section('title', 'Riwayat Naskah Layouter')

@section('header')
    <div class="layouter-naskah-page-header">
        <div>
            <h1>Riwayat Naskah Layouter</h1>
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
        $riwayatFilters = [
            'status' => $uniqueFilterValues($riwayatList, 'status_naskah'),
            'penulis' => $uniqueFilterValues($riwayatList, 'nama_penulis'),
            'editor' => $uniqueFilterValues($riwayatList, 'nama_editor'),
        ];
    @endphp

    <section class="layouter-naskah-page">
        <div class="layouter-naskah-shell">
            @if (session('status'))
                <div class="layouter-naskah-alert" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            <div class="layouter-naskah-card" data-layouter-riwayat-table>
                <div class="layouter-naskah-card-header">
                    <div>
                        <p class="layouter-naskah-card-title">Riwayat Layout Selesai</p>
                        <p class="layouter-naskah-card-subtitle">Naskah yang sudah diselesaikan oleh layouter.</p>
                    </div>

                    <div class="layouter-naskah-toolbar">
                        <label class="layouter-naskah-search-control">
                            <span class="sr-only">Cari riwayat naskah layouter</span>
                            <input type="search" data-layouter-riwayat-search placeholder="Cari Data">
                        </label>

                        <div class="layouter-naskah-filter-grid">
                            <select data-layouter-riwayat-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($riwayatFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-layouter-riwayat-filter="penulis" aria-label="Filter penulis">
                                <option value="">Penulis</option>
                                @foreach ($riwayatFilters['penulis'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-layouter-riwayat-filter="editor" aria-label="Filter editor">
                                <option value="">Editor</option>
                                @foreach ($riwayatFilters['editor'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="layouter-naskah-reset-button" data-layouter-riwayat-reset>Reset</button>

                        <label class="layouter-naskah-page-size">
                            <span>Tampil</span>
                            <select data-layouter-riwayat-page-size aria-label="Jumlah data per halaman">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="layouter-naskah-table-wrap">
                    <table class="layouter-naskah-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Naskah</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Editor</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatList as $naskah)
                                @php
                                    $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                    $editorName = $naskah->nama_editor ?? '-';
                                    $searchText = collect([
                                        $kodeNaskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $editorName,
                                        $naskah->status_naskah,
                                        $naskah->tanggal_cetak,
                                    ])->filter()->implode(' ');
                                @endphp
                                <tr
                                    data-layouter-riwayat-row
                                    data-search="{{ $searchText }}"
                                    data-filter-status="{{ $naskah->status_naskah }}"
                                    data-filter-penulis="{{ $naskah->nama_penulis }}"
                                    data-filter-editor="{{ $naskah->nama_editor }}"
                                >
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="layouter-naskah-code-cell">{{ $kodeNaskah }}</td>
                                    <td class="layouter-naskah-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_penulis }}</td>
                                    <td>{{ $editorName }}</td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$naskah->status_naskah" />
                                    </td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('layouter.riwayat-naskah.show', $naskah->id_naskah) }}"
                                            class="layouter-naskah-detail-button"
                                        >
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr data-layouter-riwayat-server-empty>
                                    <td colspan="7" class="layouter-naskah-empty">
                                        Belum ada riwayat naskah selesai.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="hidden" data-layouter-riwayat-empty>
                                <td colspan="7" class="layouter-naskah-empty">
                                    Tidak ada riwayat naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="layouter-naskah-pagination-bar">
                    <p class="layouter-naskah-pagination-info" data-layouter-riwayat-info>Menampilkan 0 dari 0 data</p>
                    <div class="layouter-naskah-pagination" data-layouter-riwayat-pagination aria-label="Pagination riwayat naskah layouter"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
