@extends('layouts.app')

@section('title', 'Jadwal Penerbitan')

@section('header')
    <div class="admin-jadwal-page-header">
        <div>
            <h1 class="admin-jadwal-title">Jadwal Penerbitan</h1>
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

        $jadwalFilters = [
            'jenjang' => $uniqueFilterValues($naskahList, 'jenjang'),
            'kelas' => $uniqueFilterValues($naskahList, 'kelas'),
            'status' => collect(['Siap Dijadwalkan']),
            'jadwal' => collect(['Belum ditentukan']),
        ];
    @endphp

    <section class="admin-jadwal-page">
        <div class="admin-jadwal-shell" x-data="{ activeScheduleModal: null }">
            @if (session('status'))
                <div class="admin-jadwal-alert admin-jadwal-alert-success" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="admin-jadwal-alert admin-jadwal-alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="admin-jadwal-card" data-admin-jadwal-table>
                <div class="admin-jadwal-card-header">
                    <div>
                        <p class="admin-jadwal-card-title">Naskah Selesai Layout</p>
                        <p class="admin-jadwal-card-subtitle">Naskah final dari layouter yang siap ditentukan jadwal penerbitannya.</p>
                    </div>

                    <div class="admin-jadwal-toolbar">
                        <label class="admin-jadwal-search-control">
                            <x-icons.search class="h-[15px] w-[15px]" />
                            <span class="sr-only">Cari data jadwal penerbitan</span>
                            <input type="search" placeholder="Cari Data" data-admin-jadwal-search>
                        </label>

                        <div class="admin-jadwal-filter-grid">
                            <select data-admin-jadwal-filter="jenjang" aria-label="Filter jenjang">
                                <option value="">Jenjang</option>
                                @foreach ($jadwalFilters['jenjang'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-jadwal-filter="kelas" aria-label="Filter kelas">
                                <option value="">Kelas</option>
                                @foreach ($jadwalFilters['kelas'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-jadwal-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($jadwalFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-jadwal-filter="jadwal" aria-label="Filter jadwal terbit">
                                <option value="">Jadwal</option>
                                @foreach ($jadwalFilters['jadwal'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="admin-jadwal-reset-button" data-admin-jadwal-reset>
                            Reset
                        </button>

                        <label class="admin-jadwal-page-size">
                            <span>Tampil</span>
                            <select data-admin-jadwal-page-size aria-label="Jumlah data per halaman">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="admin-jadwal-table-wrap">
                    <table class="admin-jadwal-table">
                        <thead>
                            <tr>
                                <th>Kode Naskah</th>
                                <th>Judul Naskah</th>
                                <th>Nama Penulis</th>
                                <th>Nama Editor</th>
                                <th>Nama Layouter</th>
                                <th>Jenjang</th>
                                <th>Kelas</th>
                                <th>File Final Layout</th>
                                <th>Status</th>
                                <th>Jadwal Terbit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($naskahList as $naskah)
                                @php
                                    $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                    $statusLabel = 'Siap Dijadwalkan';
                                    $jadwalLabel = 'Belum ditentukan';
                                    $fileLabel = $naskah->file_layout ? ($naskah->nama_file_layout_asli ?? basename($naskah->file_layout)) : 'Belum ada file final';
                                    $isPdfLayout = $naskah->file_layout && strtolower(pathinfo($naskah->file_layout, PATHINFO_EXTENSION)) === 'pdf';
                                    $searchText = collect([
                                        $kodeNaskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $naskah->nama_editor,
                                        $naskah->nama_layouter,
                                        $naskah->jenjang,
                                        $naskah->kelas,
                                        $fileLabel,
                                        $statusLabel,
                                        $jadwalLabel,
                                    ])->filter()->implode(' ');
                                @endphp

                                <tr
                                    data-admin-jadwal-row
                                    data-search="{{ $searchText }}"
                                    data-filter-jenjang="{{ $naskah->jenjang }}"
                                    data-filter-kelas="{{ $naskah->kelas }}"
                                    data-filter-status="{{ $statusLabel }}"
                                    data-filter-jadwal="{{ $jadwalLabel }}"
                                >
                                    <td class="whitespace-nowrap">{{ $kodeNaskah }}</td>
                                    <td class="admin-jadwal-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_penulis }}</td>
                                    <td>{{ $naskah->nama_editor ?? '-' }}</td>
                                    <td>{{ $naskah->nama_layouter ?? '-' }}</td>
                                    <td>{{ $naskah->jenjang }}</td>
                                    <td>{{ $naskah->kelas }}</td>
                                    <td class="admin-jadwal-file-cell">
                                        @if ($naskah->file_layout)
                                            <span class="break-all">{{ $fileLabel }}</span>
                                        @else
                                            <span class="admin-jadwal-muted">{{ $fileLabel }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$statusLabel" />
                                    </td>
                                    <td class="text-center">
                                        <span class="admin-jadwal-date-badge">{{ $jadwalLabel }}</span>
                                    </td>
                                    <td class="admin-jadwal-action-cell">
                                        <div class="admin-jadwal-action-group">
                                            <a
                                                href="{{ route('admin.riwayat-naskah.download', $naskah->id_naskah) }}"
                                                class="file-action-button file-action-button--download file-action-button--icon"
                                                title="Download file"
                                                aria-label="Download file"
                                            >
                                                <x-icons.download class="h-4 w-4" />
                                            </a>
                                            @if ($isPdfLayout)
                                                <a
                                                    href="{{ route('admin.riwayat-naskah.preview', $naskah->id_naskah) }}"
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
                                            <button
                                                type="button"
                                                @click="activeScheduleModal = 'schedule-{{ $naskah->id_naskah }}'"
                                                class="admin-jadwal-action-button is-primary"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v10.5A2.25 2.25 0 0 1 18.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A1.5 1.5 0 0 1 4.5 6.75Z" />
                                                </svg>
                                                Tentukan
                                            </button>
                                        </div>

                                        <div
                                            x-show="activeScheduleModal === 'schedule-{{ $naskah->id_naskah }}'"
                                            x-cloak
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
                                        >
                                            <div
                                                @click.away="activeScheduleModal = null"
                                                class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl"
                                            >
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <h2 class="text-lg font-semibold text-gray-900">Tentukan Jadwal Penerbitan</h2>
                                                        <p class="mt-1 text-sm text-gray-600">{{ $kodeNaskah }} - {{ $naskah->judul }}</p>
                                                    </div>

                                                    <button
                                                        type="button"
                                                        @click="activeScheduleModal = null"
                                                        class="text-gray-400 transition hover:text-gray-600"
                                                    >
                                                        <span class="sr-only">Tutup</span>
                                                        <x-icons.x-close class="h-5 w-5" />
                                                    </button>
                                                </div>

                                                <form method="POST" action="{{ route('admin.jadwal-penerbitan.store', $naskah->id_naskah) }}" class="mt-5 space-y-4">
                                                    @csrf

                                                    <div>
                                                        <label for="bulan_{{ $naskah->id_naskah }}" class="block text-sm font-medium text-gray-700">Bulan</label>
                                                        <select
                                                            id="bulan_{{ $naskah->id_naskah }}"
                                                            name="bulan"
                                                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            required
                                                        >
                                                            <option value="">Pilih bulan</option>
                                                            @foreach (range(1, 12) as $bulan)
                                                                <option value="{{ $bulan }}" @selected((int) old('bulan') === $bulan)>
                                                                    {{ \Illuminate\Support\Carbon::createFromDate(null, $bulan, 1)->translatedFormat('F') }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label for="tahun_{{ $naskah->id_naskah }}" class="block text-sm font-medium text-gray-700">Tahun</label>
                                                        <input
                                                            id="tahun_{{ $naskah->id_naskah }}"
                                                            name="tahun"
                                                            type="number"
                                                            min="2024"
                                                            max="2100"
                                                            value="{{ old('tahun', now()->year) }}"
                                                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            required
                                                        >
                                                    </div>

                                                    <div class="flex justify-end gap-3">
                                                        <button
                                                            type="button"
                                                            @click="activeScheduleModal = null"
                                                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50"
                                                        >
                                                            Batal
                                                        </button>
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-500"
                                                        >
                                                            Simpan Jadwal
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr data-admin-jadwal-server-empty>
                                    <td colspan="11" class="admin-jadwal-empty">
                                        Tidak ada naskah yang menunggu penjadwalan penerbitan.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="admin-jadwal-filter-empty hidden" data-admin-jadwal-empty>
                                <td colspan="11" class="admin-jadwal-empty">
                                    Tidak ada naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="admin-jadwal-pagination-bar">
                    <p class="admin-jadwal-pagination-info" data-admin-jadwal-info>Menampilkan 0 dari 0 data</p>
                    <div class="admin-jadwal-pagination" data-admin-jadwal-pagination aria-label="Pagination jadwal penerbitan"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
