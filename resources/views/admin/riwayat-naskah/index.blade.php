@extends('layouts.app')

@section('title', 'Riwayat Naskah Admin')

@section('header')
    <div class="admin-riwayat-page-header">
        <div>
            <h1 class="admin-riwayat-title">Riwayat Monitoring</h1>
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
            'jenjang' => $uniqueFilterValues($riwayatList, 'jenjang'),
            'kelas' => $uniqueFilterValues($riwayatList, 'kelas'),
            'tanggal' => $bulanTerbitOptions,
            'status' => $bulanTerbitOptions->map(fn ($value) => 'Terbit '.$value)->values(),
        ];
    @endphp

    <section class="admin-riwayat-page">
        <div class="admin-riwayat-shell" x-data="{ activeScheduleModal: @js(old('schedule_modal')) }">
            @if (session('status'))
                <div class="admin-jadwal-alert admin-jadwal-alert-success" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="admin-jadwal-alert admin-jadwal-alert-danger" data-flash-auto-hide>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="admin-riwayat-card" data-admin-riwayat-table>
                <div class="admin-riwayat-card-header">
                    <div>
                        <p class="admin-riwayat-card-title">Riwayat Naskah Terjadwal</p>
                        <p class="admin-riwayat-card-subtitle">Daftar naskah yang sudah memiliki jadwal penerbitan.</p>
                    </div>

                    <div class="admin-riwayat-toolbar">
                        <label class="admin-riwayat-search-control">
                            <x-icons.search class="h-[15px] w-[15px]" />
                            <span class="sr-only">Cari riwayat naskah</span>
                            <input type="search" placeholder="Cari Data" data-admin-riwayat-search>
                        </label>

                        <div class="admin-riwayat-filter-grid">
                            <select data-admin-riwayat-filter="jenjang" aria-label="Filter jenjang">
                                <option value="">Jenjang</option>
                                @foreach ($riwayatFilters['jenjang'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-riwayat-filter="kelas" aria-label="Filter kelas">
                                <option value="">Kelas</option>
                                @foreach ($riwayatFilters['kelas'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-riwayat-filter="tanggal" aria-label="Filter tanggal terbit">
                                <option value="">Tanggal Terbit</option>
                                @foreach ($riwayatFilters['tanggal'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-riwayat-filter="status" aria-label="Filter status">
                                <option value="">Status</option>
                                @foreach ($riwayatFilters['status'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="admin-riwayat-reset-button" data-admin-riwayat-reset>
                            Reset
                        </button>

                        <label class="admin-riwayat-page-size">
                            <span>Tampil</span>
                            <select data-admin-riwayat-page-size aria-label="Jumlah data per halaman">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="admin-riwayat-table-wrap">
                    <table class="admin-riwayat-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Naskah</th>
                                <th>Judul Naskah</th>
                                <th>Penulis</th>
                                <th>Editor</th>
                                <th>Layouter</th>
                                <th>Jenjang</th>
                                <th>Kelas</th>
                                <th>Tanggal Terbit</th>
                                <th>Download File</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatList as $naskah)
                                @php
                                    $kodeNaskah = $naskah->kode_naskah ?? '#'.$naskah->id_naskah;
                                    $tanggalJadwal = \Illuminate\Support\Carbon::parse($naskah->tanggal_cetak);
                                    $tanggalTerbit = $tanggalJadwal->translatedFormat('F Y');
                                    $statusLabel = 'Terbit '.$tanggalTerbit;
                                    $isPdfLayout = $naskah->file_layout && strtolower(pathinfo($naskah->file_layout, PATHINFO_EXTENSION)) === 'pdf';
                                    $scheduleModalId = 'edit-jadwal-'.$naskah->id_naskah;
                                    $searchText = collect([
                                        $kodeNaskah,
                                        $naskah->judul,
                                        $naskah->nama_penulis,
                                        $naskah->nama_editor,
                                        $naskah->nama_layouter,
                                        $naskah->jenjang,
                                        $naskah->kelas,
                                        $tanggalTerbit,
                                        $statusLabel,
                                    ])->filter()->implode(' ');
                                @endphp

                                <tr
                                    data-admin-riwayat-row
                                    data-search="{{ $searchText }}"
                                    data-filter-jenjang="{{ $naskah->jenjang }}"
                                    data-filter-kelas="{{ $naskah->kelas }}"
                                    data-filter-tanggal="{{ $tanggalTerbit }}"
                                    data-filter-status="{{ $statusLabel }}"
                                >
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="whitespace-nowrap">{{ $kodeNaskah }}</td>
                                    <td class="admin-riwayat-title-cell">{{ $naskah->judul }}</td>
                                    <td>{{ $naskah->nama_penulis }}</td>
                                    <td>{{ $naskah->nama_editor ?? '-' }}</td>
                                    <td>{{ $naskah->nama_layouter ?? '-' }}</td>
                                    <td>{{ $naskah->jenjang }}</td>
                                    <td>{{ $naskah->kelas }}</td>
                                    <td class="text-center">
                                        <span class="admin-riwayat-date-badge">{{ $tanggalTerbit }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="file-action-group">
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
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <x-status-naskah-badge :status="$statusLabel" />
                                    </td>
                                    <td class="text-center">
                                        <button
                                            type="button"
                                            class="admin-jadwal-action-button is-primary"
                                            @click="activeScheduleModal = '{{ $scheduleModalId }}'"
                                        >
                                            Edit Jadwal
                                        </button>

                                        <div
                                            x-show="activeScheduleModal === '{{ $scheduleModalId }}'"
                                            x-cloak
                                            class="editor-naskah-modal-overlay"
                                            role="dialog"
                                            aria-modal="true"
                                            aria-labelledby="edit-jadwal-title-{{ $naskah->id_naskah }}"
                                        >
                                            <div class="editor-naskah-modal-card" @click.away="activeScheduleModal = null">
                                                <div class="editor-naskah-modal-header">
                                                    <div>
                                                        <h2 id="edit-jadwal-title-{{ $naskah->id_naskah }}" class="editor-naskah-modal-title">
                                                            Edit Jadwal Penerbitan
                                                        </h2>
                                                        <p class="editor-naskah-modal-subtitle">{{ $naskah->judul }}</p>
                                                        <p class="editor-naskah-modal-meta">{{ $kodeNaskah }}</p>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        class="editor-naskah-modal-close"
                                                        @click="activeScheduleModal = null"
                                                        aria-label="Tutup modal"
                                                    >
                                                        &times;
                                                    </button>
                                                </div>

                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.jadwal-penerbitan.update', $naskah->id_naskah) }}"
                                                    class="editor-naskah-modal-form"
                                                >
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="schedule_modal" value="{{ $scheduleModalId }}">

                                                    <div class="editor-naskah-modal-info">
                                                        Perubahan jadwal akan memperbarui data terbit naskah dan mengirim notifikasi kepada penulis.
                                                    </div>

                                                    <div class="editor-naskah-modal-field">
                                                        <label for="bulan-jadwal-{{ $naskah->id_naskah }}">Bulan Terbit</label>
                                                        <select id="bulan-jadwal-{{ $naskah->id_naskah }}" name="bulan" required>
                                                            @foreach (range(1, 12) as $bulan)
                                                                <option
                                                                    value="{{ $bulan }}"
                                                                    @selected((int) old('bulan', $tanggalJadwal->month) === $bulan)
                                                                >
                                                                    {{ \Illuminate\Support\Carbon::createFromDate(2024, $bulan, 1)->translatedFormat('F') }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="editor-naskah-modal-field">
                                                        <label for="tahun-jadwal-{{ $naskah->id_naskah }}">Tahun Terbit</label>
                                                        <input
                                                            id="tahun-jadwal-{{ $naskah->id_naskah }}"
                                                            type="number"
                                                            name="tahun"
                                                            min="2024"
                                                            max="2100"
                                                            value="{{ old('tahun', $tanggalJadwal->year) }}"
                                                            class="admin-form-input"
                                                            required
                                                        >
                                                    </div>

                                                    <div class="editor-naskah-modal-field">
                                                        <label for="catatan-jadwal-{{ $naskah->id_naskah }}">Catatan Admin</label>
                                                        <textarea
                                                            id="catatan-jadwal-{{ $naskah->id_naskah }}"
                                                            name="catatan_admin"
                                                            rows="3"
                                                            maxlength="255"
                                                            class="admin-form-textarea"
                                                        >{{ old('catatan_admin', $naskah->catatan_admin) }}</textarea>
                                                    </div>

                                                    <div class="editor-naskah-modal-actions">
                                                        <button
                                                            type="button"
                                                            class="editor-naskah-modal-secondary"
                                                            @click="activeScheduleModal = null"
                                                        >
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="editor-naskah-modal-primary">
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr data-admin-riwayat-server-empty>
                                    <td colspan="12" class="admin-riwayat-empty">
                                        Belum ada naskah yang dijadwalkan terbit.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="admin-riwayat-filter-empty hidden" data-admin-riwayat-empty>
                                <td colspan="12" class="admin-riwayat-empty">
                                    Tidak ada riwayat naskah yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="admin-riwayat-pagination-bar">
                    <p class="admin-riwayat-pagination-info" data-admin-riwayat-info>Menampilkan 0 dari 0 data</p>
                    <div class="admin-riwayat-pagination" data-admin-riwayat-pagination aria-label="Pagination riwayat monitoring"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
