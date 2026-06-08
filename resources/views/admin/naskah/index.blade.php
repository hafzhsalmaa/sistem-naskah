@extends('layouts.app')

@section('title', 'Data Naskah')

@section('header')
    <div class="admin-naskah-page-header">
        <div>
            <h1 class="admin-naskah-title">Data Naskah</h1>
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

        $naskahBelumDikirimFilters = [
            'kelas' => $uniqueFilterValues($naskahBelumDikirim, 'kelas'),
            'mata_pelajaran' => $uniqueFilterValues($naskahBelumDikirim, 'mata_pelajaran'),
            'nama_penulis' => $uniqueFilterValues($naskahBelumDikirim, 'nama_penulis'),
            'bidang_keahlian' => $uniqueFilterValues($naskahBelumDikirim, 'bidang_keahlian'),
            'kategori_mapel' => $uniqueFilterValues($naskahBelumDikirim, 'kategori_mapel'),
            'status' => $uniqueFilterValues($naskahBelumDikirim, 'status_tampilan'),
        ];

        $naskahSudahDikirimFilters = [
            'kelas' => $uniqueFilterValues($naskahSudahDikirim, 'kelas'),
            'mata_pelajaran' => $uniqueFilterValues($naskahSudahDikirim, 'mata_pelajaran'),
            'nama_penulis' => $uniqueFilterValues($naskahSudahDikirim, 'nama_penulis'),
            'editor' => $uniqueFilterValues($naskahSudahDikirim, 'nama_editor'),
            'bidang_keahlian' => $uniqueFilterValues($naskahSudahDikirim, 'bidang_keahlian'),
            'kategori_mapel' => $uniqueFilterValues($naskahSudahDikirim, 'kategori_mapel'),
            'status' => $uniqueFilterValues($naskahSudahDikirim, 'status_tampilan'),
        ];
    @endphp

    <section class="admin-naskah-page">
        <div class="admin-naskah-shell" x-data="{ activeAssignModal: null }">
            @if (session('status'))
                <div class="admin-naskah-alert admin-naskah-alert-success" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('id_editor'))
                <div class="admin-naskah-alert admin-naskah-alert-danger">
                    {{ $errors->first('id_editor') }}
                </div>
            @endif

            <div class="admin-naskah-card" data-admin-naskah-table>
                <div class="admin-naskah-card-header">
                    <div>
                        <p class="admin-naskah-card-title">Naskah Belum Diproses</p>
                        {{-- <p class="admin-naskah-card-subtitle">Daftar naskah yang masih menunggu penugasan editor.</p> --}}
                    </div>
                    <div class="admin-naskah-toolbar">
                        <label class="admin-naskah-search-control">
                            <x-icons.search class="h-4 w-4" />
                            <span class="sr-only">Cari data naskah belum dikirim</span>
                            <input type="search" data-admin-naskah-search placeholder="Cari data">
                        </label>
                        <div class="admin-naskah-filter-grid">
                            <select data-admin-naskah-filter="kelas" aria-label="Filter kelas">
                                <option value="">Kelas</option>
                                @foreach ($naskahBelumDikirimFilters['kelas'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="mataPelajaran" aria-label="Filter mata pelajaran">
                                <option value="">Mata Pelajaran</option>
                                @foreach ($naskahBelumDikirimFilters['mata_pelajaran'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="penulis" aria-label="Filter nama penulis">
                                <option value="">Penulis</option>
                                @foreach ($naskahBelumDikirimFilters['nama_penulis'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="bidang" aria-label="Filter bidang keahlian">
                                <option value="">Bidang</option>
                                @foreach ($naskahBelumDikirimFilters['bidang_keahlian'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="kategori" aria-label="Filter kategori mapel">
                                <option value="">Kategori</option>
                                @foreach ($naskahBelumDikirimFilters['kategori_mapel'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($naskahBelumDikirimFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="admin-naskah-reset-button" data-admin-naskah-reset>Reset</button>
                        <label class="admin-naskah-page-size">
                            <span>Tampil</span>
                            <select data-admin-naskah-page-size aria-label="Jumlah data naskah belum dikirim per halaman">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                        <div class="admin-naskah-filter-display">
                            <span data-admin-naskah-count>{{ $naskahBelumDikirim->count() }}</span>
                            <span>Belum dikirim</span>
                        </div>
                    </div>
                </div>

                <div class="admin-naskah-table-wrap">
                    <table class="admin-naskah-table">
                        <thead>
                            <tr>
                                <th>Kode Naskah</th>
                                <th>Judul Naskah</th>
                                <th>Nama Penulis</th>
                                <th>Bidang Keahlian</th>
                                <th>Kelas</th>
                                <th>Kategori Mapel</th>
                                <th>Mata Pelajaran</th>
                                <th>File Naskah</th>
                                <th>Tanggal Upload</th>
                                <th class="text-center">Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($naskahBelumDikirim as $naskah)
                                @php
                                    $tanggalUpload = $naskah->tanggal_upload ? \Illuminate\Support\Carbon::parse($naskah->tanggal_upload)->format('d M Y H:i') : '-';
                                    $searchText = implode(' ', [
                                        $naskah->kode_naskah ?? '#'.$naskah->id_naskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $naskah->bidang_keahlian,
                                        $naskah->kelas,
                                        $naskah->kategori_mapel,
                                        $naskah->mata_pelajaran,
                                        $naskah->status_tampilan,
                                        $tanggalUpload,
                                        $naskah->nama_file_asli ?? $naskah->file_path,
                                    ]);
                                @endphp

                                <tr
                                    data-admin-naskah-row
                                    data-search="{{ $searchText }}"
                                    data-filter-kelas="{{ $naskah->kelas }}"
                                    data-filter-mata-pelajaran="{{ $naskah->mata_pelajaran }}"
                                    data-filter-penulis="{{ $naskah->nama_penulis }}"
                                    data-filter-bidang="{{ $naskah->bidang_keahlian }}"
                                    data-filter-kategori="{{ $naskah->kategori_mapel }}"
                                    data-filter-status="{{ $naskah->status_tampilan }}"
                                >
                                    <td class="whitespace-nowrap">{{ $naskah->kode_naskah ?? '#'.$naskah->id_naskah }}</td>
                                    <td class="admin-naskah-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_penulis }}</td>
                                    <td>{{ $naskah->bidang_keahlian }}</td>
                                    <td>{{ $naskah->kelas }}</td>
                                    <td>{{ $naskah->kategori_mapel }}</td>
                                    <td>{{ $naskah->mata_pelajaran }}</td>
                                    <td class="admin-naskah-file-cell">
                                        @if ($naskah->file_path)
                                            <span class="break-all">{{ $naskah->nama_file_asli ?? basename($naskah->file_path) }}</span>
                                        @else
                                            <span class="admin-naskah-muted">Belum ada file</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap">
                                        {{ $tanggalUpload }}
                                    </td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$naskah->status_tampilan" />
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $editors = $editorMap[$naskah->id_naskah] ?? collect();
                                        @endphp

                                        @if ($editors->isEmpty())
                                            <span class="admin-naskah-muted">Editor cocok tidak tersedia</span>
                                        @else
                                            <div class="admin-naskah-action-cell">
                                                <button
                                                    type="button"
                                                    @click="activeAssignModal = 'assign-{{ $naskah->id_naskah }}'"
                                                    class="admin-naskah-action-button"
                                                >
                                                    Kirim ke Editor
                                                </button>
                                            </div>

                                            <div
                                                x-show="activeAssignModal === 'assign-{{ $naskah->id_naskah }}'"
                                                x-cloak
                                                class="editor-naskah-modal-overlay"
                                            >
                                                <div
                                                    @click.away="activeAssignModal = null"
                                                    class="editor-naskah-modal-card"
                                                >
                                                    <div class="editor-naskah-modal-header">
                                                        <div>
                                                            <h2 class="editor-naskah-modal-title">Kirim Naskah ke Editor</h2>
                                                            <p class="editor-naskah-modal-subtitle">{{ $naskah->judul }}</p>
                                                            <p class="editor-naskah-modal-meta">Penulis: {{ $naskah->nama_penulis }}</p>
                                                        </div>

                                                        <button
                                                            type="button"
                                                            @click="activeAssignModal = null"
                                                            class="editor-naskah-modal-close"
                                                        >
                                                            <span class="sr-only">Tutup</span>
                                                            <x-icons.x-close class="h-5 w-5" />
                                                        </button>
                                                    </div>

                                                    <form method="POST" action="{{ route('admin.naskah.assign-editor', $naskah->id_naskah) }}" class="editor-naskah-modal-form">
                                                        @csrf

                                                        <div class="editor-naskah-modal-info">
                                                            Naskah akan dikirim ke editor yang sesuai dengan bidang keahlian, kategori mapel, dan mata pelajaran.
                                                        </div>

                                                        <div class="editor-naskah-modal-field">
                                                            <label for="id_editor_{{ $naskah->id_naskah }}">
                                                                Pilih Editor
                                                            </label>
                                                            <select
                                                                id="id_editor_{{ $naskah->id_naskah }}"
                                                                name="id_editor"
                                                                required
                                                            >
                                                                <option value="">Pilih editor</option>
                                                                @foreach ($editors as $editor)
                                                                    <option value="{{ $editor->id_editor }}" @selected((int) $naskah->id_editor === (int) $editor->id_editor)>
                                                                        {{ $editor->username }} &bull; {{ $editor->naskah_aktif_count ?? 0 }} Naskah Aktif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="editor-naskah-modal-actions">
                                                            <button
                                                                type="button"
                                                                @click="activeAssignModal = null"
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
                                <tr>
                                    <td colspan="11" class="admin-naskah-empty">
                                        Tidak ada naskah yang menunggu pengiriman ke editor.
                                    </td>
                                </tr>
                            @endforelse
                            <tr class="admin-naskah-filter-empty hidden" data-admin-naskah-empty>
                                <td colspan="11" class="admin-naskah-empty">
                                    Tidak ada naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="admin-naskah-pagination-bar">
                    <p class="admin-naskah-pagination-info" data-admin-naskah-info>
                        Menampilkan 0 dari 0 data
                    </p>
                    <div class="admin-naskah-pagination" data-admin-naskah-pagination></div>
                </div>
            </div>

            <div class="admin-naskah-card" data-admin-naskah-table>
                <div class="admin-naskah-card-header">
                    <div>
                        <p class="admin-naskah-card-title">Naskah Sedang / Sudah Diproses</p>
                        {{-- <p class="admin-naskah-card-subtitle">Monitoring progress naskah yang sudah memiliki editor.</p> --}}
                    </div>
                    <div class="admin-naskah-toolbar">
                        <label class="admin-naskah-search-control">
                            <x-icons.search class="h-4 w-4" />
                            <span class="sr-only">Cari data naskah sudah dikirim</span>
                            <input type="search" data-admin-naskah-search placeholder="Cari data">
                        </label>
                        <div class="admin-naskah-filter-grid">
                            <select data-admin-naskah-filter="kelas" aria-label="Filter kelas">
                                <option value="">Kelas</option>
                                @foreach ($naskahSudahDikirimFilters['kelas'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="mataPelajaran" aria-label="Filter mata pelajaran">
                                <option value="">Mata Pelajaran</option>
                                @foreach ($naskahSudahDikirimFilters['mata_pelajaran'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="penulis" aria-label="Filter nama penulis">
                                <option value="">Penulis</option>
                                @foreach ($naskahSudahDikirimFilters['nama_penulis'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="editor" aria-label="Filter editor">
                                <option value="">Editor</option>
                                @foreach ($naskahSudahDikirimFilters['editor'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="bidang" aria-label="Filter bidang keahlian">
                                <option value="">Bidang</option>
                                @foreach ($naskahSudahDikirimFilters['bidang_keahlian'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="kategori" aria-label="Filter kategori mapel">
                                <option value="">Kategori</option>
                                @foreach ($naskahSudahDikirimFilters['kategori_mapel'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-naskah-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($naskahSudahDikirimFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="admin-naskah-reset-button" data-admin-naskah-reset>Reset</button>
                        <label class="admin-naskah-page-size">
                            <span>Tampil</span>
                            <select data-admin-naskah-page-size aria-label="Jumlah data naskah sudah dikirim per halaman">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                        <div class="admin-naskah-filter-display">
                            <span data-admin-naskah-count>{{ $naskahSudahDikirim->count() }}</span>
                            <span>Sudah dikirim</span>
                        </div>
                    </div>
                </div>

                <div class="admin-naskah-table-wrap">
                    <table class="admin-naskah-table">
                        <thead>
                            <tr>
                                <th>Kode Naskah</th>
                                <th>Judul Naskah</th>
                                <th>Nama Penulis</th>
                                <th>Editor</th>
                                <th>Bidang Keahlian</th>
                                <th>Kelas</th>
                                <th>Kategori Mapel</th>
                                <th>Mata Pelajaran</th>
                                <th>File Naskah</th>
                                <th>Tanggal Upload</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($naskahSudahDikirim as $naskah)
                                @php
                                    $tanggalUpload = $naskah->tanggal_upload ? \Illuminate\Support\Carbon::parse($naskah->tanggal_upload)->format('d M Y H:i') : '-';
                                    $searchText = implode(' ', [
                                        $naskah->kode_naskah ?? '#'.$naskah->id_naskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $naskah->nama_editor,
                                        $naskah->bidang_keahlian,
                                        $naskah->kelas,
                                        $naskah->kategori_mapel,
                                        $naskah->mata_pelajaran,
                                        $naskah->status_tampilan,
                                        $tanggalUpload,
                                        $naskah->nama_file_asli ?? $naskah->file_path,
                                    ]);
                                @endphp

                                <tr
                                    data-admin-naskah-row
                                    data-search="{{ $searchText }}"
                                    data-filter-kelas="{{ $naskah->kelas }}"
                                    data-filter-mata-pelajaran="{{ $naskah->mata_pelajaran }}"
                                    data-filter-penulis="{{ $naskah->nama_penulis }}"
                                    data-filter-editor="{{ $naskah->nama_editor }}"
                                    data-filter-bidang="{{ $naskah->bidang_keahlian }}"
                                    data-filter-kategori="{{ $naskah->kategori_mapel }}"
                                    data-filter-status="{{ $naskah->status_tampilan }}"
                                >
                                    <td class="whitespace-nowrap">{{ $naskah->kode_naskah ?? '#'.$naskah->id_naskah }}</td>
                                    <td class="admin-naskah-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_penulis }}</td>
                                    <td>{{ $naskah->nama_editor }}</td>
                                    <td>{{ $naskah->bidang_keahlian }}</td>
                                    <td>{{ $naskah->kelas }}</td>
                                    <td>{{ $naskah->kategori_mapel }}</td>
                                    <td>{{ $naskah->mata_pelajaran }}</td>
                                    <td class="admin-naskah-file-cell">
                                        @if ($naskah->file_path)
                                            <span class="break-all">{{ $naskah->nama_file_asli ?? basename($naskah->file_path) }}</span>
                                        @else
                                            <span class="admin-naskah-muted">Belum ada file</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap">
                                        {{ $tanggalUpload }}
                                    </td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$naskah->status_tampilan" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="admin-naskah-empty">
                                        Belum ada naskah yang dikirim ke editor.
                                    </td>
                                </tr>
                            @endforelse
                            <tr class="admin-naskah-filter-empty hidden" data-admin-naskah-empty>
                                <td colspan="11" class="admin-naskah-empty">
                                    Tidak ada naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="admin-naskah-pagination-bar">
                    <p class="admin-naskah-pagination-info" data-admin-naskah-info>
                        Menampilkan 0 dari 0 data
                    </p>
                    <div class="admin-naskah-pagination" data-admin-naskah-pagination></div>
                </div>
            </div>
        </div>
    </section>
@endsection
