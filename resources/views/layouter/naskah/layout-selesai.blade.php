@extends('layouts.app')

@section('title', 'Manajemen Layout Layouter')

@section('header')
    <div class="layouter-naskah-page-header">
        <div>
            <h1>Manajemen Layout Layouter</h1>
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
        $naskahSelesaiFilters = [
            'status' => $uniqueFilterValues($naskahSelesai, 'status_naskah'),
            'penulis' => $uniqueFilterValues($naskahSelesai, 'nama_penulis'),
            'editor' => $uniqueFilterValues($naskahSelesai, 'nama_editor'),
        ];
    @endphp

    <section class="layouter-naskah-page">
        <div class="layouter-naskah-shell">
            @if (session('status'))
                <div class="layouter-naskah-alert" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="layouter-naskah-alert layouter-naskah-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="layouter-naskah-card" data-layouter-selesai-table>
                <div class="layouter-naskah-card-header">
                    <div>
                        <p class="layouter-naskah-card-title">Manajemen Layout Aktif</p>
                        <p class="layouter-naskah-card-subtitle">Naskah yang sedang dalam proses layout dan perlu dikelola hingga selesai.</p>
                    </div>

                    <div class="layouter-naskah-toolbar">
                        <label class="layouter-naskah-search-control">
                            <span class="sr-only">Cari data manajemen layout</span>
                            <input type="search" data-layouter-selesai-search placeholder="Cari Data">
                        </label>

                        <div class="layouter-naskah-filter-grid">
                            <select data-layouter-selesai-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($naskahSelesaiFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-layouter-selesai-filter="penulis" aria-label="Filter penulis">
                                <option value="">Penulis</option>
                                @foreach ($naskahSelesaiFilters['penulis'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-layouter-selesai-filter="editor" aria-label="Filter editor">
                                <option value="">Editor</option>
                                @foreach ($naskahSelesaiFilters['editor'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="layouter-naskah-reset-button" data-layouter-selesai-reset>Reset</button>

                        <label class="layouter-naskah-page-size">
                            <span>Tampil</span>
                            <select data-layouter-selesai-page-size aria-label="Jumlah data per halaman">
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
                                <th>Nama Penulis</th>
                                <th>Nama Editor</th>
                                <th>Tanggal Proses</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($naskahSelesai as $naskah)
                                @php
                                    $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                    $tanggalSelesai = $naskah->tanggal_layout
                                        ? \Illuminate\Support\Carbon::parse($naskah->tanggal_layout)->format('d M Y H:i')
                                        : '-';
                                    $editorName = $naskah->nama_editor ?? '-';
                                    $searchText = collect([
                                        $kodeNaskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $editorName,
                                        $tanggalSelesai,
                                        $naskah->status_naskah,
                                    ])->filter()->implode(' ');
                                @endphp

                                <tr
                                    data-layouter-selesai-row
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
                                    <td class="whitespace-nowrap">{{ $tanggalSelesai }}</td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$naskah->status_naskah" />
                                    </td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('layouter.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'layout-selesai']) }}"
                                            class="layouter-naskah-detail-button"
                                        >
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr data-layouter-selesai-server-empty>
                                    <td colspan="8" class="layouter-naskah-empty">
                                        Belum ada naskah yang sedang dalam proses layout.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="hidden" data-layouter-selesai-empty>
                                <td colspan="8" class="layouter-naskah-empty">
                                    Tidak ada naskah proses layout yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="layouter-naskah-pagination-bar">
                    <p class="layouter-naskah-pagination-info" data-layouter-selesai-info>Menampilkan 0 dari 0 data</p>
                    <div class="layouter-naskah-pagination" data-layouter-selesai-pagination aria-label="Pagination manajemen layout"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
