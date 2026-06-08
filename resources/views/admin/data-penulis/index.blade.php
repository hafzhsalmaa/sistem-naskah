@extends('layouts.app')

@section('title', 'Data Penulis')

@section('header')
    <div class="admin-penulis-page-header">
        <div>
            <h1 class="admin-penulis-title">Data Penulis</h1>
        </div>
        <a href="{{ route('admin.data-penulis.create') }}" class="admin-penulis-create-button">
            Tambah Penulis
        </a>
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

        $penulisFilters = [
            'jurusan_pendidikan' => $uniqueFilterValues($penulisList, 'jurusan_pendidikan'),
            'profesi' => $uniqueFilterValues($penulisList, 'profesi'),
            'domisili' => $uniqueFilterValues($penulisList, 'alamat'),
        ];
    @endphp

    <section class="admin-penulis-page">
        <div class="admin-penulis-shell" x-data="{ activeEmailModal: null }">
            @if (session('status'))
                <div class="admin-penulis-alert admin-penulis-alert-success" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="admin-penulis-alert admin-penulis-alert-danger" data-flash-auto-hide>
                    {{ session('error') }}
                </div>
            @endif

            <div class="admin-penulis-card" data-admin-penulis-table>
                <div class="admin-penulis-card-header">
                    <div>
                        <p class="admin-penulis-card-title">Daftar Penulis</p>
                        <p class="admin-penulis-card-subtitle">Kelola data penulis yang terdaftar dalam sistem.</p>
                    </div>

                    <div class="admin-penulis-toolbar">
                        <label class="admin-penulis-search-control">
                            <x-icons.search class="h-4 w-4" />
                            <span class="sr-only">Cari data penulis</span>
                            <input type="search" data-admin-penulis-search placeholder="Cari data">
                        </label>

                        <div class="admin-penulis-filter-grid">
                            <select data-admin-penulis-filter="jurusan" aria-label="Filter jurusan pendidikan">
                                <option value="">Jurusan</option>
                                @foreach ($penulisFilters['jurusan_pendidikan'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-penulis-filter="profesi" aria-label="Filter profesi">
                                <option value="">Profesi</option>
                                @foreach ($penulisFilters['profesi'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select data-admin-penulis-filter="domisili" aria-label="Filter domisili">
                                <option value="">Domisili</option>
                                @foreach ($penulisFilters['domisili'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="admin-penulis-reset-button" data-admin-penulis-reset>Reset</button>

                        <label class="admin-penulis-page-size">
                            <span>Tampil</span>
                            <select data-admin-penulis-page-size>
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
                    <form method="POST" action="{{ route('admin.data-penulis.email.bulk') }}" class="admin-bulk-email-dialog" role="dialog" aria-modal="true" aria-labelledby="bulk-email-penulis-title" data-admin-bulk-email-form>
                        @csrf
                        <div data-admin-bulk-email-ids></div>
                        <div class="admin-bulk-email-header">
                            <div>
                                <h2 id="bulk-email-penulis-title" class="admin-bulk-email-title">Kirim Email Terpilih</h2>
                                <p class="admin-bulk-email-subtitle">
                                    Email akan dikirim ke <span data-admin-bulk-email-count>0</span> penerima terpilih.
                                </p>
                            </div>
                            <button type="button" class="admin-bulk-email-close" data-admin-bulk-email-close aria-label="Tutup modal">
                                <x-icons.x-close class="h-5 w-5" />
                            </button>
                        </div>
                        <div class="admin-bulk-email-body">
                            <label for="bulk_email_penulis_message" class="admin-bulk-email-label">Pesan</label>
                            <textarea id="bulk_email_penulis_message" name="pesan" rows="5" maxlength="5000" class="admin-bulk-email-textarea" data-admin-bulk-email-message required></textarea>
                        </div>
                        <div class="admin-bulk-email-actions">
                            <button type="button" class="admin-bulk-email-secondary" data-admin-bulk-email-close>Batal</button>
                            <button type="button" class="admin-bulk-email-primary" data-admin-bulk-email-submit>Kirim Email</button>
                        </div>
                    </form>
                </div>

                <div class="admin-penulis-table-wrap">
                    <table class="admin-penulis-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="admin-penulis-checkbox" data-admin-select-all aria-label="Pilih semua penulis">
                                </th>
                                <th>Kode Penulis</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Nomor Handphone</th>
                                <th>Domisili</th>
                                <th>Profesi</th>
                                <th>Jurusan Pendidikan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($penulisList as $penulis)
                                @php
                                    $searchText = implode(' ', [
                                        $penulis->kode_penulis ?? '#'.$penulis->id_penulis,
                                        $penulis->nama_lengkap,
                                        $penulis->email,
                                        $penulis->no_hp,
                                        $penulis->alamat,
                                        $penulis->profesi,
                                        $penulis->jurusan_pendidikan,
                                    ]);
                                @endphp

                                <tr
                                    data-admin-penulis-row
                                    data-search="{{ $searchText }}"
                                    data-filter-jurusan="{{ $penulis->jurusan_pendidikan }}"
                                    data-filter-profesi="{{ $penulis->profesi }}"
                                    data-filter-domisili="{{ $penulis->alamat }}"
                                >
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $penulis->id_penulis }}" class="admin-penulis-checkbox" data-admin-row-checkbox aria-label="Pilih penulis {{ $penulis->nama_lengkap ?: $penulis->id_penulis }}">
                                    </td>
                                    <td class="whitespace-nowrap">{{ $penulis->kode_penulis ?? '#'.$penulis->id_penulis }}</td>
                                    <td class="admin-penulis-name-cell">{{ $penulis->nama_lengkap ?: '-' }}</td>
                                    <td>{{ $penulis->email }}</td>
                                    <td>{{ $penulis->no_hp ?: '-' }}</td>
                                    <td>{{ $penulis->alamat ?: '-' }}</td>
                                    <td>{{ $penulis->profesi ?: '-' }}</td>
                                    <td>{{ $penulis->jurusan_pendidikan ?: '-' }}</td>
                                    <td class="whitespace-nowrap">
                                        <div class="admin-penulis-action-group">
                                            <a
                                                href="{{ route('admin.data-penulis.edit', $penulis->id_penulis) }}"
                                                class="admin-penulis-icon-button is-edit"
                                                aria-label="Edit penulis"
                                            >
                                                <x-icons.edit-compact class="h-4 w-4" />
                                            </a>

                                            <form method="POST" action="{{ route('admin.data-penulis.destroy', $penulis->id_penulis) }}" class="js-confirm-delete" data-confirm-message="Hapus data penulis ini?">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="admin-penulis-icon-button is-delete"
                                                    aria-label="Hapus penulis"
                                                >
                                                    <x-icons.trash-compact class="h-4 w-4" />
                                                </button>
                                            </form>

                                            <button
                                                type="button"
                                                @click="activeEmailModal = 'penulis-{{ $penulis->id_penulis }}'"
                                                class="admin-penulis-icon-button is-email"
                                                aria-label="Kirim email ke penulis"
                                            >
                                                <x-icons.mail class="h-4 w-4" />
                                            </button>
                                        </div>

                                        <div
                                            x-show="activeEmailModal === 'penulis-{{ $penulis->id_penulis }}'"
                                            x-cloak
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
                                        >
                                            <div
                                                @click.away="activeEmailModal = null"
                                                class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl"
                                            >
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <h2 class="text-lg font-semibold text-gray-900">Kirim Email ke Penulis</h2>
                                                        <p class="mt-1 text-sm text-gray-600">{{ $penulis->nama_lengkap ?: 'Penulis' }}</p>
                                                    </div>

                                                    <button type="button" @click="activeEmailModal = null" class="text-gray-400 transition hover:text-gray-600">
                                                        <span class="sr-only">Tutup</span>
                                                        <x-icons.x-close class="h-5 w-5" />
                                                    </button>
                                                </div>

                                                <form method="POST" action="{{ route('admin.data-penulis.email', $penulis->id_penulis) }}" class="mt-5 space-y-4">
                                                    @csrf

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Kepada</label>
                                                        <input type="text" value="{{ $penulis->email }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-sm shadow-sm" readonly>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Dari</label>
                                                        <input type="text" value="{{ config('mail.from.address') }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-sm shadow-sm" readonly>
                                                    </div>

                                                    <div>
                                                        <label for="pesan_penulis_{{ $penulis->id_penulis }}" class="block text-sm font-medium text-gray-700">Pesan</label>
                                                        <textarea
                                                            id="pesan_penulis_{{ $penulis->id_penulis }}"
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
                                <tr>
                                    <td colspan="9" class="admin-penulis-empty">
                                        Belum ada data penulis.
                                    </td>
                                </tr>
                            @endforelse
                            <tr class="admin-penulis-filter-empty hidden" data-admin-penulis-empty>
                                <td colspan="9" class="admin-penulis-empty">
                                    Tidak ada data penulis yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="admin-penulis-pagination-bar">
                    <p class="admin-penulis-pagination-info" data-admin-penulis-info>Menampilkan 0 data</p>
                    <div class="admin-penulis-pagination" data-admin-penulis-pagination></div>
                </div>
            </div>
        </div>
    </section>
@endsection
