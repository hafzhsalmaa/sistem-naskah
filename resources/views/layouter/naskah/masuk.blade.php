@extends('layouts.app')

@section('title', 'Daftar Naskah Masuk Layouter')

@section('header')
    <div class="layouter-naskah-page-header">
        <div>
            <h1>Daftar Naskah Masuk Layouter</h1>
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
        $naskahAktifFilters = [
            'status' => $uniqueFilterValues($naskahAktif, 'status_tampilan'),
            'penulis' => $uniqueFilterValues($naskahAktif, 'nama_penulis'),
            'editor' => $uniqueFilterValues($naskahAktif, 'nama_editor'),
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

            <div class="layouter-naskah-card" data-layouter-masuk-table>
                <div class="layouter-naskah-card-header">
                    <div>
                        <p class="layouter-naskah-card-title">Naskah Masuk</p>
                        <p class="layouter-naskah-card-subtitle">Naskah baru yang sudah dikirim ke layouter dan menunggu mulai dikerjakan.</p>
                    </div>

                    <div class="layouter-naskah-toolbar">
                        <label class="layouter-naskah-search-control">
                            <span class="sr-only">Cari data naskah masuk layouter</span>
                            <input type="search" data-layouter-masuk-search placeholder="Cari Data">
                        </label>

                        <div class="layouter-naskah-filter-grid">
                            <select data-layouter-masuk-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($naskahAktifFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-layouter-masuk-filter="penulis" aria-label="Filter penulis">
                                <option value="">Penulis</option>
                                @foreach ($naskahAktifFilters['penulis'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-layouter-masuk-filter="editor" aria-label="Filter editor">
                                <option value="">Editor</option>
                                @foreach ($naskahAktifFilters['editor'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="layouter-naskah-reset-button" data-layouter-masuk-reset>Reset</button>

                        <label class="layouter-naskah-page-size">
                            <span>Tampil</span>
                            <select data-layouter-masuk-page-size aria-label="Jumlah data per halaman">
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
                                <th>Mata Pelajaran</th>
                                <th>Tanggal Diterima</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($naskahAktif as $naskah)
                                @php
                                    $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                    $tanggalDiterima = $naskah->tanggal_upload_terbaru
                                        ? \Illuminate\Support\Carbon::parse($naskah->tanggal_upload_terbaru)->format('d M Y H:i')
                                        : '-';
                                    $editorName = $naskah->nama_editor ?? '-';
                                    $searchText = collect([
                                        $kodeNaskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $editorName,
                                        $naskah->mata_pelajaran,
                                        $tanggalDiterima,
                                        $naskah->status_tampilan,
                                    ])->filter()->implode(' ');
                                @endphp

                                <tr
                                    data-layouter-masuk-row
                                    data-search="{{ $searchText }}"
                                    data-filter-status="{{ $naskah->status_tampilan }}"
                                    data-filter-penulis="{{ $naskah->nama_penulis }}"
                                    data-filter-editor="{{ $naskah->nama_editor }}"
                                >
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="layouter-naskah-code-cell">{{ $kodeNaskah }}</td>
                                    <td class="layouter-naskah-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_penulis }}</td>
                                    <td>{{ $editorName }}</td>
                                    <td>{{ $naskah->mata_pelajaran }}</td>
                                    <td class="whitespace-nowrap">{{ $tanggalDiterima }}</td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$naskah->status_tampilan" />
                                    </td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('layouter.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'masuk']) }}"
                                            class="layouter-naskah-detail-button"
                                        >
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr data-layouter-masuk-server-empty>
                                    <td colspan="9" class="layouter-naskah-empty">
                                        Belum ada naskah masuk yang menunggu proses layout.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="hidden" data-layouter-masuk-empty>
                                <td colspan="9" class="layouter-naskah-empty">
                                    Tidak ada naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="layouter-naskah-pagination-bar">
                    <p class="layouter-naskah-pagination-info" data-layouter-masuk-info>Menampilkan 0 dari 0 data</p>
                    <div class="layouter-naskah-pagination" data-layouter-masuk-pagination aria-label="Pagination naskah masuk layouter"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
