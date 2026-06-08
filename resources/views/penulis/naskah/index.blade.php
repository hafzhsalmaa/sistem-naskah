@extends('layouts.app')

@section('title', 'Data Naskah Penulis')

@section('header')
    <div class="penulis-naskah-page-heading">
        <div>
            <h1>Data Naskah Saya</h1>
        </div>
        <a href="{{ route('penulis.naskah.create') }}" class="penulis-naskah-add-button">
            Tambah Naskah
        </a>
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
        $naskahProsesFilters = [
            'status' => $uniqueFilterValues($naskahProses, 'status_tampilan'),
            'kelas' => $uniqueFilterValues($naskahProses, 'kelas'),
            'mata_pelajaran' => $uniqueFilterValues($naskahProses, 'mata_pelajaran'),
        ];
        $naskahPerbaikanFilters = [
            'status' => $uniqueFilterValues($naskahPerluPerbaikan, 'status_tampilan'),
            'kelas' => $uniqueFilterValues($naskahPerluPerbaikan, 'kelas'),
            'mata_pelajaran' => $uniqueFilterValues($naskahPerluPerbaikan, 'mata_pelajaran'),
        ];
        $naskahSiapDijadwalkanFilters = [
            'status' => $uniqueFilterValues($naskahSiapDijadwalkan, 'status_tampilan'),
            'kelas' => $uniqueFilterValues($naskahSiapDijadwalkan, 'kelas'),
            'mata_pelajaran' => $uniqueFilterValues($naskahSiapDijadwalkan, 'mata_pelajaran'),
        ];
    @endphp

    <section class="penulis-naskah-page">
        <div class="penulis-naskah-shell">
            @if (session('status'))
                <div class="penulis-naskah-alert" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            <div class="penulis-naskah-sections">
                <div class="penulis-naskah-card" data-penulis-naskah-table>
                    <div class="penulis-naskah-card-header">
                        <div>
                            <p class="penulis-naskah-card-title">Naskah Proses / Belum Perlu Revisi</p>
                            <p class="penulis-naskah-card-subtitle">Menampilkan naskah yang masih diproses, ditinjau, atau sudah masuk tahap layout.</p>
                        </div>
                        <div class="penulis-naskah-toolbar">
                            <label class="penulis-naskah-search-control">
                                <span class="sr-only">Cari data naskah proses</span>
                                <input type="search" data-penulis-naskah-search placeholder="Cari Data">
                            </label>
                            <div class="penulis-naskah-filter-grid">
                                <select data-penulis-naskah-filter="status" aria-label="Filter status naskah proses">
                                    <option value="">Status</option>
                                    @foreach ($naskahProsesFilters['status'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <select data-penulis-naskah-filter="kelas" aria-label="Filter kelas naskah proses">
                                    <option value="">Kelas</option>
                                    @foreach ($naskahProsesFilters['kelas'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <select data-penulis-naskah-filter="mataPelajaran" aria-label="Filter mata pelajaran naskah proses">
                                    <option value="">Mata Pelajaran</option>
                                    @foreach ($naskahProsesFilters['mata_pelajaran'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="penulis-naskah-reset-button" data-penulis-naskah-reset>Reset</button>
                        </div>
                    </div>

                    <div class="penulis-naskah-table-wrap">
                        <table class="penulis-naskah-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Naskah</th>
                                    <th>Judul</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Tanggal Upload</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($naskahProses as $naskah)
                                    @php
                                        $tanggalUpload = $naskah->tanggal_upload ? \Illuminate\Support\Carbon::parse($naskah->tanggal_upload)->format('d M Y H:i') : '-';
                                        $canDeleteNaskah = $naskah->id_editor === null
                                            && $naskah->status_naskah === 'Pending Review'
                                            && (int) ($naskah->latest_no_versi ?? 1) <= 1;
                                        $deleteTitle = $canDeleteNaskah
                                            ? 'Hapus naskah'
                                            : (
                                                (int) ($naskah->latest_no_versi ?? 1) > 1
                                                    ? 'Naskah revisi tidak dapat dihapus'
                                                    : 'Naskah sudah masuk proses editor'
                                            );
                                        $searchText = implode(' ', [
                                            $naskah->kode_naskah ?: '#'.$naskah->id_naskah,
                                            $naskah->judul,
                                            $naskah->kelas,
                                            $naskah->mata_pelajaran,
                                            $tanggalUpload,
                                            $naskah->status_tampilan,
                                        ]);
                                    @endphp
                                    <tr
                                        data-penulis-naskah-row
                                        data-search="{{ $searchText }}"
                                        data-filter-status="{{ $naskah->status_tampilan }}"
                                        data-filter-kelas="{{ $naskah->kelas }}"
                                        data-filter-mata-pelajaran="{{ $naskah->mata_pelajaran }}"
                                    >
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="penulis-naskah-code-cell">{{ $naskah->kode_naskah ?: '#'.$naskah->id_naskah }}</td>
                                        <td class="penulis-naskah-title-cell">{{ $naskah->judul }}</td>
                                        <td>{{ $naskah->kelas }}</td>
                                        <td>{{ $naskah->mata_pelajaran }}</td>
                                        <td class="penulis-naskah-date-cell">
                                            {{ $tanggalUpload }}
                                        </td>
                                        <td class="penulis-naskah-status-cell">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan" />
                                        </td>
                                        <td class="penulis-naskah-action-cell penulis-naskah-action-cell-centered">
                                            <div class="penulis-naskah-action-group">
                                                <a
                                                    href="{{ route('penulis.naskah.show', $naskah->id_naskah) }}"
                                                    class="penulis-btn-detail"
                                                >
                                                    Lihat Detail
                                                </a>

                                                @if ($canDeleteNaskah)
                                                    <form method="POST" action="{{ route('penulis.naskah.destroy', $naskah->id_naskah) }}" class="js-confirm-delete" data-confirm-message="Apakah Anda yakin ingin menghapus naskah ini?">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="penulis-naskah-delete-button"
                                                            aria-label="Hapus naskah {{ $naskah->judul }}"
                                                            title="{{ $deleteTitle }}"
                                                        >
                                                            <x-icons.trash-compact class="h-4 w-4" />
                                                        </button>
                                                    </form>
                                                @else
                                                    <button
                                                        type="button"
                                                        class="penulis-naskah-delete-button is-disabled"
                                                        aria-label="{{ $deleteTitle }}"
                                                        title="{{ $deleteTitle }}"
                                                        disabled
                                                    >
                                                        <x-icons.trash-compact class="h-4 w-4" />
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="penulis-naskah-server-empty-cell">
                                            <div class="penulis-naskah-empty-state">
                                                <div class="penulis-naskah-empty-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-8 w-8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375H14.25V6.375A3.375 3.375 0 0 0 10.875 3H8.25m.75 12h6m-6 3h6m2.25-12.75V21a.75.75 0 0 1-.75.75H5.25A2.25 2.25 0 0 1 3 19.5V4.5A2.25 2.25 0 0 1 5.25 2.25h5.379a.75.75 0 0 1 .53.22l5.841 5.84a.75.75 0 0 1 .22.53Z" />
                                                    </svg>
                                                </div>
                                                <p class="penulis-naskah-empty-title">Belum ada naskah aktif saat ini</p>
                                                <p class="penulis-naskah-empty-copy">
                                                    Naskah yang sedang diproses, ditinjau, atau sudah masuk tahap layout akan tampil di bagian ini.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class="hidden" data-penulis-naskah-empty>
                                    <td colspan="8" class="penulis-naskah-filter-empty-cell">
                                        Data tidak ditemukan untuk pencarian atau filter aktif.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="penulis-naskah-pagination-bar" data-penulis-naskah-pagination-bar>
                        <p class="penulis-naskah-pagination-info" data-penulis-naskah-info></p>
                        <div class="penulis-naskah-pagination" data-penulis-naskah-pagination></div>
                    </div>
                </div>

                <div class="penulis-naskah-card" data-penulis-naskah-table>
                    <div class="penulis-naskah-card-header">
                        <div>
                            <p class="penulis-naskah-card-title">Naskah Perlu Perbaikan</p>
                            <p class="penulis-naskah-card-subtitle">Menampilkan naskah yang membutuhkan tindak lanjut perbaikan atau penyesuaian dari penulis.</p>
                        </div>
                        <div class="penulis-naskah-toolbar">
                            <label class="penulis-naskah-search-control">
                                <span class="sr-only">Cari data naskah perbaikan</span>
                                <input type="search" data-penulis-naskah-search placeholder="Cari Data">
                            </label>
                            <div class="penulis-naskah-filter-grid">
                                <select data-penulis-naskah-filter="status" aria-label="Filter status naskah perbaikan">
                                    <option value="">Status</option>
                                    @foreach ($naskahPerbaikanFilters['status'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <select data-penulis-naskah-filter="kelas" aria-label="Filter kelas naskah perbaikan">
                                    <option value="">Kelas</option>
                                    @foreach ($naskahPerbaikanFilters['kelas'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <select data-penulis-naskah-filter="mataPelajaran" aria-label="Filter mata pelajaran naskah perbaikan">
                                    <option value="">Mata Pelajaran</option>
                                    @foreach ($naskahPerbaikanFilters['mata_pelajaran'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="penulis-naskah-reset-button" data-penulis-naskah-reset>Reset</button>
                        </div>
                    </div>

                    <div class="penulis-naskah-table-wrap">
                        <table class="penulis-naskah-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Naskah</th>
                                    <th>Judul</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Tanggal Upload</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($naskahPerluPerbaikan as $naskah)
                                    @php
                                        $tanggalUpload = $naskah->tanggal_upload ? \Illuminate\Support\Carbon::parse($naskah->tanggal_upload)->format('d M Y H:i') : '-';
                                        $searchText = implode(' ', [
                                            $naskah->kode_naskah ?: '#'.$naskah->id_naskah,
                                            $naskah->judul,
                                            $naskah->kelas,
                                            $naskah->mata_pelajaran,
                                            $tanggalUpload,
                                            $naskah->status_tampilan,
                                        ]);
                                    @endphp
                                    <tr
                                        data-penulis-naskah-row
                                        data-search="{{ $searchText }}"
                                        data-filter-status="{{ $naskah->status_tampilan }}"
                                        data-filter-kelas="{{ $naskah->kelas }}"
                                        data-filter-mata-pelajaran="{{ $naskah->mata_pelajaran }}"
                                    >
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="penulis-naskah-code-cell">{{ $naskah->kode_naskah ?: '#'.$naskah->id_naskah }}</td>
                                        <td class="penulis-naskah-title-cell">{{ $naskah->judul }}</td>
                                        <td>{{ $naskah->kelas }}</td>
                                        <td>{{ $naskah->mata_pelajaran }}</td>
                                        <td class="penulis-naskah-date-cell">
                                            {{ $tanggalUpload }}
                                        </td>
                                        <td class="penulis-naskah-status-cell">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan" />
                                        </td>
                                        <td class="penulis-naskah-action-cell penulis-naskah-action-cell-centered">
                                            <div class="penulis-naskah-action-group">
                                                <a
                                                    href="{{ route('penulis.naskah.show', $naskah->id_naskah) }}"
                                                    class="penulis-btn-detail"
                                                >
                                                    Lihat Detail
                                                </a>
                                                @if ($naskah->status_naskah === 'Revisi')
                                                    <a
                                                        href="{{ route('penulis.naskah.show', $naskah->id_naskah) }}#upload-perbaikan"
                                                        class="penulis-btn-upload"
                                                    >
                                                        Upload Perbaikan
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="penulis-naskah-server-empty-cell">
                                            <div class="penulis-naskah-empty-state">
                                                <div class="penulis-naskah-empty-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-8 w-8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375H14.25V6.375A3.375 3.375 0 0 0 10.875 3H8.25m.75 12h6m-6 3h6m2.25-12.75V21a.75.75 0 0 1-.75.75H5.25A2.25 2.25 0 0 1 3 19.5V4.5A2.25 2.25 0 0 1 5.25 2.25h5.379a.75.75 0 0 1 .53.22l5.841 5.84a.75.75 0 0 1 .22.53Z" />
                                                    </svg>
                                                </div>
                                                <p class="penulis-naskah-empty-title">Belum ada naskah yang perlu perbaikan saat ini</p>
                                                <p class="penulis-naskah-empty-copy">
                                                    Naskah dengan status revisi akan muncul di sini agar mudah ditindaklanjuti.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class="hidden" data-penulis-naskah-empty>
                                    <td colspan="8" class="penulis-naskah-filter-empty-cell">
                                        Data tidak ditemukan untuk pencarian atau filter aktif.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="penulis-naskah-pagination-bar" data-penulis-naskah-pagination-bar>
                        <p class="penulis-naskah-pagination-info" data-penulis-naskah-info></p>
                        <div class="penulis-naskah-pagination" data-penulis-naskah-pagination></div>
                    </div>
                </div>

                <div class="penulis-naskah-card" data-penulis-naskah-table>
                    <div class="penulis-naskah-card-header">
                        <div>
                            <p class="penulis-naskah-card-title">Naskah Siap Dijadwalkan</p>
                            <p class="penulis-naskah-card-subtitle">Menampilkan naskah yang sudah selesai layout dan menunggu jadwal penerbitan dari admin.</p>
                        </div>
                        <div class="penulis-naskah-toolbar">
                            <label class="penulis-naskah-search-control">
                                <span class="sr-only">Cari data naskah siap dijadwalkan</span>
                                <input type="search" data-penulis-naskah-search placeholder="Cari Data">
                            </label>
                            <div class="penulis-naskah-filter-grid">
                                <select data-penulis-naskah-filter="status" aria-label="Filter status naskah siap dijadwalkan">
                                    <option value="">Status</option>
                                    @foreach ($naskahSiapDijadwalkanFilters['status'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <select data-penulis-naskah-filter="kelas" aria-label="Filter kelas naskah siap dijadwalkan">
                                    <option value="">Kelas</option>
                                    @foreach ($naskahSiapDijadwalkanFilters['kelas'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <select data-penulis-naskah-filter="mataPelajaran" aria-label="Filter mata pelajaran naskah siap dijadwalkan">
                                    <option value="">Mata Pelajaran</option>
                                    @foreach ($naskahSiapDijadwalkanFilters['mata_pelajaran'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="penulis-naskah-reset-button" data-penulis-naskah-reset>Reset</button>
                        </div>
                    </div>

                    <div class="penulis-naskah-table-wrap">
                        <table class="penulis-naskah-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Naskah</th>
                                    <th>Judul</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Tanggal Upload</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($naskahSiapDijadwalkan as $naskah)
                                    @php
                                        $tanggalUpload = $naskah->tanggal_upload ? \Illuminate\Support\Carbon::parse($naskah->tanggal_upload)->format('d M Y H:i') : '-';
                                        $searchText = implode(' ', [
                                            $naskah->kode_naskah ?: '#'.$naskah->id_naskah,
                                            $naskah->judul,
                                            $naskah->kelas,
                                            $naskah->mata_pelajaran,
                                            $tanggalUpload,
                                            $naskah->status_tampilan,
                                        ]);
                                    @endphp
                                    <tr
                                        data-penulis-naskah-row
                                        data-search="{{ $searchText }}"
                                        data-filter-status="{{ $naskah->status_tampilan }}"
                                        data-filter-kelas="{{ $naskah->kelas }}"
                                        data-filter-mata-pelajaran="{{ $naskah->mata_pelajaran }}"
                                    >
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="penulis-naskah-code-cell">{{ $naskah->kode_naskah ?: '#'.$naskah->id_naskah }}</td>
                                        <td class="penulis-naskah-title-cell">{{ $naskah->judul }}</td>
                                        <td>{{ $naskah->kelas }}</td>
                                        <td>{{ $naskah->mata_pelajaran }}</td>
                                        <td class="penulis-naskah-date-cell">
                                            {{ $tanggalUpload }}
                                        </td>
                                        <td class="penulis-naskah-status-cell">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan" />
                                        </td>
                                        <td class="penulis-naskah-action-cell penulis-naskah-action-cell-centered">
                                            <a
                                                href="{{ route('penulis.naskah.show', $naskah->id_naskah) }}"
                                                class="penulis-btn-detail"
                                            >
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="penulis-naskah-server-empty-cell">
                                            <div class="penulis-naskah-empty-state">
                                                <div class="penulis-naskah-empty-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="h-8 w-8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375H14.25V6.375A3.375 3.375 0 0 0 10.875 3H8.25m.75 12h6m-6 3h6m2.25-12.75V21a.75.75 0 0 1-.75.75H5.25A2.25 2.25 0 0 1 3 19.5V4.5A2.25 2.25 0 0 1 5.25 2.25h5.379a.75.75 0 0 1 .53.22l5.841 5.84a.75.75 0 0 1 .22.53Z" />
                                                    </svg>
                                                </div>
                                                <p class="penulis-naskah-empty-title">Belum ada naskah yang siap dijadwalkan</p>
                                                <p class="penulis-naskah-empty-copy">
                                                    Naskah selesai layout yang belum memiliki jadwal terbit akan tampil di bagian ini.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr class="hidden" data-penulis-naskah-empty>
                                    <td colspan="8" class="penulis-naskah-filter-empty-cell">
                                        Data tidak ditemukan untuk pencarian atau filter aktif.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="penulis-naskah-pagination-bar" data-penulis-naskah-pagination-bar>
                        <p class="penulis-naskah-pagination-info" data-penulis-naskah-info></p>
                        <div class="penulis-naskah-pagination" data-penulis-naskah-pagination></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
