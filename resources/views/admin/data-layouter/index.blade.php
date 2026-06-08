@extends('layouts.app')

@section('title', 'Data Layouter')

@section('header')
    <div class="admin-layouter-page-header">
        <div>
            <h1 class="admin-layouter-title">Data Layouter</h1>
        </div>
        <a href="{{ route('admin.data-layouter.create') }}" class="admin-btn-primary">
            Tambah Layouter
        </a>
    </div>
@endsection

@section('content')
    @php
        $mataPelajaranOptions = $layouterList->pluck('mata_pelajaran')->filter()->unique()->sort()->values();
        $bidangKeahlianOptions = $layouterList->pluck('bidang_keahlian')->filter()->unique()->sort()->values();
        $namaLayouterOptions = $layouterList->pluck('nama_lengkap')->filter()->unique()->sort()->values();
    @endphp

    <section class="admin-layouter-page">
        <div class="admin-layouter-shell" x-data="{ activeEmailModal: null }">
            @if (session('status'))
                <div class="admin-layouter-alert admin-layouter-alert-success" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="admin-layouter-alert admin-layouter-alert-danger" data-flash-auto-hide>
                    {{ session('error') }}
                </div>
            @endif

            <div class="admin-layouter-card" data-admin-layouter-table>
                <div class="admin-layouter-card-header">
                    <div>
                        <p class="admin-layouter-card-title">Daftar Layouter</p>
                        <p class="admin-layouter-card-subtitle">Kelola data layouter tanpa mengganggu proses layout yang berjalan.</p>
                    </div>

                    <div class="admin-layouter-toolbar" aria-label="Toolbar data layouter">
                        <label class="admin-layouter-search-control">
                            <x-icons.search class="h-[15px] w-[15px]" />
                            <span class="sr-only">Cari data layouter</span>
                            <input type="search" placeholder="Cari Data" data-admin-layouter-search>
                        </label>

                        <div class="admin-layouter-filter-grid">
                            <select data-admin-layouter-filter="mataPelajaran" aria-label="Filter bidang mata pelajaran">
                                <option value="">Bidang Mapel</option>
                                @foreach ($mataPelajaranOptions as $mataPelajaran)
                                    <option value="{{ $mataPelajaran }}">{{ $mataPelajaran }}</option>
                                @endforeach
                            </select>

                            <select data-admin-layouter-filter="bidang" aria-label="Filter bidang keahlian">
                                <option value="">Bidang Keahlian</option>
                                @foreach ($bidangKeahlianOptions as $bidangKeahlian)
                                    <option value="{{ $bidangKeahlian }}">{{ $bidangKeahlian }}</option>
                                @endforeach
                            </select>

                            <select data-admin-layouter-filter="nama" aria-label="Filter nama layouter">
                                <option value="">Nama Layouter</option>
                                @foreach ($namaLayouterOptions as $namaLayouter)
                                    <option value="{{ $namaLayouter }}">{{ $namaLayouter }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="admin-layouter-reset-button" data-admin-layouter-reset>
                            Reset
                        </button>

                        <label class="admin-layouter-page-size">
                            <span>Tampil</span>
                            <select data-admin-layouter-page-size aria-label="Jumlah data per halaman">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </label>
                    </div>
                </div>

                <div class="admin-bulk-action-bar" data-admin-bulk-bar hidden>
                    <p class="admin-bulk-action-text">
                        <span data-admin-selected-count>0</span> data terpilih
                    </p>
                    <button type="button" class="admin-bulk-action-button" data-admin-bulk-email-button data-admin-bulk-email-open>
                        Kirim Email Terpilih
                    </button>
                </div>

                <div class="admin-bulk-email-modal" data-admin-bulk-email-modal hidden>
                    <div class="admin-bulk-email-overlay" data-admin-bulk-email-close></div>
                    <form method="POST" action="{{ route('admin.data-layouter.email.bulk') }}" class="admin-bulk-email-dialog" role="dialog" aria-modal="true" aria-labelledby="bulk-email-layouter-title" data-admin-bulk-email-form>
                        @csrf
                        <div data-admin-bulk-email-ids></div>
                        <div class="admin-bulk-email-header">
                            <div>
                                <h2 id="bulk-email-layouter-title" class="admin-bulk-email-title">Kirim Email Terpilih</h2>
                                <p class="admin-bulk-email-subtitle">
                                    Email akan dikirim ke <span data-admin-bulk-email-count>0</span> penerima terpilih.
                                </p>
                            </div>
                            <button type="button" class="admin-bulk-email-close" data-admin-bulk-email-close aria-label="Tutup modal">
                                <x-icons.x-close class="h-5 w-5" />
                            </button>
                        </div>
                        <div class="admin-bulk-email-body">
                            <label for="bulk_email_layouter_message" class="admin-bulk-email-label">Pesan</label>
                            <textarea id="bulk_email_layouter_message" name="pesan" rows="5" maxlength="5000" class="admin-bulk-email-textarea" data-admin-bulk-email-message required></textarea>
                        </div>
                        <div class="admin-bulk-email-actions">
                            <button type="button" class="admin-bulk-email-secondary" data-admin-bulk-email-close>Batal</button>
                            <button type="button" class="admin-bulk-email-primary" data-admin-bulk-email-submit>Kirim Email</button>
                        </div>
                    </form>
                </div>

                <div class="admin-layouter-table-wrap">
                    <table class="admin-layouter-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="admin-layouter-checkbox" data-admin-select-all aria-label="Pilih semua layouter">
                                </th>
                                <th>Kode Layouter</th>
                                <th>Nama Layouter</th>
                                <th>Email</th>
                                <th>Nomor Handphone</th>
                                <th>Bidang Mata Pelajaran</th>
                                <th>Bidang Keahlian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($layouterList as $layouter)
                                @php
                                    $searchText = collect([
                                        $layouter->kode_layouter ?? '#'.$layouter->id_layouter,
                                        $layouter->id_layouter,
                                        $layouter->nama_lengkap,
                                        $layouter->email,
                                        $layouter->no_hp,
                                        $layouter->mata_pelajaran,
                                        $layouter->bidang_keahlian,
                                    ])->filter()->implode(' ');
                                @endphp
                                <tr
                                    data-admin-layouter-row
                                    data-search="{{ $searchText }}"
                                    data-filter-mata-pelajaran="{{ $layouter->mata_pelajaran }}"
                                    data-filter-bidang="{{ $layouter->bidang_keahlian }}"
                                    data-filter-nama="{{ $layouter->nama_lengkap }}"
                                >
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $layouter->id_layouter }}" class="admin-layouter-checkbox" data-admin-row-checkbox aria-label="Pilih layouter {{ $layouter->nama_lengkap ?: $layouter->id_layouter }}">
                                    </td>
                                    <td>{{ $layouter->kode_layouter ?? '#'.$layouter->id_layouter }}</td>
                                    <td class="admin-layouter-name-cell">{{ $layouter->nama_lengkap ?: '-' }}</td>
                                    <td>{{ $layouter->email }}</td>
                                    <td>{{ $layouter->no_hp ?: '-' }}</td>
                                    <td>{{ $layouter->mata_pelajaran ?: '-' }}</td>
                                    <td>{{ $layouter->bidang_keahlian ?: '-' }}</td>
                                    <td>
                                        <div class="admin-layouter-action-group">
                                            <a
                                                href="{{ route('admin.data-layouter.edit', $layouter->id_layouter) }}"
                                                class="admin-layouter-icon-button is-edit"
                                                aria-label="Edit layouter {{ $layouter->nama_lengkap ?: $layouter->id_layouter }}"
                                                title="Edit"
                                            >
                                                <x-icons.edit class="h-[17px] w-[17px]" />
                                            </a>

                                            <form method="POST" action="{{ route('admin.data-layouter.destroy', $layouter->id_layouter) }}" class="js-confirm-delete" data-confirm-message="Hapus data layouter ini?">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="admin-layouter-icon-button is-delete"
                                                    aria-label="Hapus layouter {{ $layouter->nama_lengkap ?: $layouter->id_layouter }}"
                                                    title="Hapus"
                                                >
                                                    <x-icons.trash class="h-[17px] w-[17px]" />
                                                </button>
                                            </form>

                                            <button
                                                type="button"
                                                @click="activeEmailModal = 'layouter-{{ $layouter->id_layouter }}'"
                                                class="admin-layouter-icon-button is-email"
                                                aria-label="Kirim email ke {{ $layouter->nama_lengkap ?: 'layouter' }}"
                                                title="Kirim Email"
                                            >
                                                <x-icons.mail class="h-[17px] w-[17px]" />
                                            </button>
                                        </div>

                                        <div
                                            x-show="activeEmailModal === 'layouter-{{ $layouter->id_layouter }}'"
                                            x-cloak
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
                                        >
                                            <div
                                                @click.away="activeEmailModal = null"
                                                class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl"
                                            >
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <h2 class="text-lg font-semibold text-gray-900">Kirim Email ke Layouter</h2>
                                                        <p class="mt-1 text-sm text-gray-600">{{ $layouter->nama_lengkap ?: 'Layouter' }}</p>
                                                    </div>

                                                    <button type="button" @click="activeEmailModal = null" class="text-gray-400 transition hover:text-gray-600">
                                                        <span class="sr-only">Tutup</span>
                                                        <x-icons.x-close class="h-5 w-5" />
                                                    </button>
                                                </div>

                                                <form method="POST" action="{{ route('admin.data-layouter.email', $layouter->id_layouter) }}" class="mt-5 space-y-4">
                                                    @csrf

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Kepada</label>
                                                        <input type="text" value="{{ $layouter->email }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-sm shadow-sm" readonly>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Dari</label>
                                                        <input type="text" value="{{ config('mail.from.address') }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-sm shadow-sm" readonly>
                                                    </div>

                                                    <div>
                                                        <label for="pesan_layouter_{{ $layouter->id_layouter }}" class="block text-sm font-medium text-gray-700">Pesan</label>
                                                        <textarea
                                                            id="pesan_layouter_{{ $layouter->id_layouter }}"
                                                            name="pesan"
                                                            rows="5"
                                                            class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            required
                                                        >{{ old('pesan') }}</textarea>
                                                        @error('pesan')
                                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <div class="flex justify-end gap-3">
                                                        <button
                                                            type="button"
                                                            @click="activeEmailModal = null"
                                                            class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50"
                                                        >
                                                            Batal
                                                        </button>
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center rounded-md bg-sky-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-sky-500"
                                                        >
                                                            Kirim
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr data-admin-layouter-server-empty>
                                    <td colspan="8" class="admin-layouter-empty">
                                        Belum ada data layouter.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="admin-layouter-filter-empty hidden" data-admin-layouter-empty>
                                <td colspan="8" class="admin-layouter-empty">
                                    Tidak ada data layouter yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="admin-layouter-pagination-bar">
                    <p class="admin-layouter-pagination-info" data-admin-layouter-info>Menampilkan 0 dari 0 data</p>
                    <div class="admin-layouter-pagination" data-admin-layouter-pagination aria-label="Pagination data layouter"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
