@extends('layouts.app')

@section('title', 'Riwayat Naskah Editor')

@section('header')
    <div class="editor-naskah-page-header">
        <div>
            <h1>Riwayat Naskah Editor</h1>
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
            'layouter' => $uniqueFilterValues($riwayatList, 'nama_layouter'),
        ];
    @endphp

    <section class="editor-naskah-page">
        <div class="editor-naskah-shell">
            <div class="editor-naskah-card" data-editor-riwayat-table>
                <div class="editor-naskah-card-header">
                    <div>
                        <p class="editor-naskah-card-title">Riwayat Review Selesai</p>
                        <p class="editor-naskah-card-subtitle">Naskah yang sudah selesai dikerjakan editor dan masuk ke proses lanjutan.</p>
                    </div>

                    <div class="editor-naskah-toolbar">
                        <label class="editor-naskah-search-control">
                            <span class="sr-only">Cari data riwayat naskah editor</span>
                            <input type="search" data-editor-riwayat-search placeholder="Cari Data">
                        </label>

                        <div class="editor-naskah-filter-grid">
                            <select data-editor-riwayat-filter="status" aria-label="Filter status riwayat">
                                <option value="">Status</option>
                                @foreach ($riwayatFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-editor-riwayat-filter="penulis" aria-label="Filter penulis riwayat">
                                <option value="">Penulis</option>
                                @foreach ($riwayatFilters['penulis'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-editor-riwayat-filter="layouter" aria-label="Filter layouter riwayat">
                                <option value="">Layouter</option>
                                @foreach ($riwayatFilters['layouter'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="editor-naskah-reset-button" data-editor-riwayat-reset>Reset</button>

                        <label class="editor-naskah-page-size">
                            <span>Tampil</span>
                            <select data-editor-riwayat-page-size aria-label="Jumlah data riwayat per halaman">
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
                                <th>Layouter</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatList as $naskah)
                                @php
                                    $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                    $statusLabel = $naskah->tanggal_cetak
                                        ? 'Terbit ' . \Illuminate\Support\Carbon::parse($naskah->tanggal_cetak)->translatedFormat('F Y')
                                        : ($naskah->status_naskah === 'Selesai Layout' ? 'Menunggu Jadwal Penerbitan' : $naskah->status_naskah);
                                    $layouterName = $naskah->nama_layouter ?? '-';
                                    $searchText = collect([
                                        $kodeNaskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $layouterName,
                                        $naskah->status_naskah,
                                        $statusLabel,
                                    ])->filter()->implode(' ');
                                @endphp
                                <tr
                                    data-editor-riwayat-row
                                    data-search="{{ $searchText }}"
                                    data-filter-status="{{ $naskah->status_naskah }}"
                                    data-filter-penulis="{{ $naskah->nama_penulis }}"
                                    data-filter-layouter="{{ $naskah->nama_layouter }}"
                                >
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="editor-naskah-code-cell">{{ $kodeNaskah }}</td>
                                    <td class="editor-naskah-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_penulis }}</td>
                                    <td>{{ $layouterName }}</td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$statusLabel" />
                                    </td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('editor.riwayat-naskah.show', $naskah->id_naskah) }}"
                                            class="editor-naskah-detail-button"
                                        >
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr data-editor-riwayat-server-empty>
                                    <td colspan="7" class="editor-naskah-empty">
                                        Belum ada riwayat naskah selesai.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="hidden" data-editor-riwayat-empty>
                                <td colspan="7" class="editor-naskah-empty">
                                    Tidak ada riwayat naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="editor-naskah-pagination-bar">
                    <p class="editor-naskah-pagination-info" data-editor-riwayat-info>Menampilkan 0 dari 0 data</p>
                    <div class="editor-naskah-pagination" data-editor-riwayat-pagination aria-label="Pagination riwayat naskah editor"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
