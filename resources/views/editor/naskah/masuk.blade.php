@extends('layouts.app')

@section('title', 'Daftar Naskah Masuk Editor')

@section('header')
    <div class="editor-naskah-page-header">
        <div>
            <h1>Daftar Naskah Masuk Editor</h1>
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
        $naskahMasukFilters = [
            'status' => $uniqueFilterValues($naskahMasuk, 'status_naskah'),
            'penulis' => $uniqueFilterValues($naskahMasuk, 'nama_penulis'),
        ];
    @endphp

    <section class="editor-naskah-page">
        <div class="editor-naskah-shell">
            @if (session('status'))
                <div class="editor-naskah-alert" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            <div id="naskah-masuk" class="editor-naskah-card" data-editor-masuk-table>
                <div class="editor-naskah-card-header">
                    <div>
                        <p class="editor-naskah-card-title">Naskah Masuk</p>
                        <p class="editor-naskah-card-subtitle">Naskah baru yang siap dicek dan direview editor.</p>
                    </div>

                    <div class="editor-naskah-toolbar">
                        <label class="editor-naskah-search-control">
                            <span class="sr-only">Cari data naskah masuk editor</span>
                            <input type="search" data-editor-masuk-search placeholder="Cari Data">
                        </label>

                        <div class="editor-naskah-filter-grid">
                            <select data-editor-masuk-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($naskahMasukFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-editor-masuk-filter="penulis" aria-label="Filter nama penulis">
                                <option value="">Nama Penulis</option>
                                @foreach ($naskahMasukFilters['penulis'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="editor-naskah-reset-button" data-editor-masuk-reset>Reset</button>

                        <label class="editor-naskah-page-size">
                            <span>Tampil</span>
                            <select data-editor-masuk-page-size aria-label="Jumlah data per halaman">
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
                                <th>Nama Penulis</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($naskahMasuk as $naskah)
                                @php
                                    $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                    $searchText = collect([
                                        $kodeNaskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $naskah->file_path,
                                        'Download File',
                                        $naskah->status_naskah,
                                    ])->filter()->implode(' ');
                                @endphp

                                <tr
                                    data-editor-masuk-row
                                    data-search="{{ $searchText }}"
                                    data-filter-status="{{ $naskah->status_naskah }}"
                                    data-filter-penulis="{{ $naskah->nama_penulis }}"
                                >
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="editor-naskah-code-cell">{{ $kodeNaskah }}</td>
                                    <td class="editor-naskah-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_penulis }}</td>
                                    <td class="text-center">
                                        <div class="file-action-group">
                                            <a
                                                href="{{ route('editor.naskah.download', $naskah->id_naskah) }}"
                                                class="file-action-button file-action-button--download file-action-button--icon"
                                                title="Download file"
                                                aria-label="Download file"
                                            >
                                                <x-icons.download class="h-4 w-4" />
                                            </a>
                                            <button
                                                type="button"
                                                class="file-action-button file-action-button--preview file-action-button--icon"
                                                data-preview-unavailable
                                                title="Preview file"
                                                aria-label="Preview file"
                                            >
                                                <x-icons.eye class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$naskah->status_naskah" />
                                    </td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('editor.naskah.show', ['id' => $naskah->id_naskah, 'from' => 'masuk']) }}"
                                            class="editor-naskah-detail-button"
                                        >
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr data-editor-masuk-server-empty>
                                    <td colspan="7" class="editor-naskah-empty">
                                        Belum ada naskah masuk editor.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="hidden" data-editor-masuk-empty>
                                <td colspan="7" class="editor-naskah-empty">
                                    Tidak ada naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="editor-naskah-pagination-bar">
                    <p class="editor-naskah-pagination-info" data-editor-masuk-info>Menampilkan 0 dari 0 data</p>
                    <div class="editor-naskah-pagination" data-editor-masuk-pagination aria-label="Pagination naskah masuk editor"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
