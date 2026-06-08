@extends('layouts.app')

@section('title', 'Data Editor')

@section('header')
    <div class="admin-editor-page-header">
        <div>
            <h1 class="admin-editor-title">Data Editor</h1>
        </div>
        <a href="{{ route('admin.data-editor.create') }}" class="admin-btn-primary">
            Tambah Editor
        </a>
    </div>
@endsection

@section('content')
    @php
        $mataPelajaranOptions = $editorList->pluck('mata_pelajaran')->filter()->unique()->sort()->values();
        $bidangKeahlianOptions = $editorList->pluck('bidang_keahlian')->filter()->unique()->sort()->values();
        $namaEditorOptions = $editorList->pluck('nama_lengkap')->filter()->unique()->sort()->values();
    @endphp

    <section class="admin-editor-page">
        <div class="admin-editor-shell" x-data="{ activeEmailModal: null }">
            @if (session('status'))
                <div class="admin-editor-alert admin-editor-alert-success" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="admin-editor-alert admin-editor-alert-danger" data-flash-auto-hide>
                    {{ session('error') }}
                </div>
            @endif

            <div class="admin-editor-card" data-admin-editor-table>
                <div class="admin-editor-card-header">
                    <div>
                        <p class="admin-editor-card-title">Daftar Editor</p>
                        <p class="admin-editor-card-subtitle">Kelola data editor tanpa mengubah alur review naskah.</p>
                    </div>

                    <div class="admin-editor-toolbar" aria-label="Toolbar data editor">
                        <label class="admin-editor-search-control">
                            <x-icons.search class="h-[15px] w-[15px]" />
                            <span class="sr-only">Cari data editor</span>
                            <input type="search" placeholder="Cari Data" data-admin-editor-search>
                        </label>

                        <div class="admin-editor-filter-grid">
                            <select data-admin-editor-filter="mataPelajaran" aria-label="Filter bidang mata pelajaran">
                                <option value="">Bidang Mapel</option>
                                @foreach ($mataPelajaranOptions as $mataPelajaran)
                                    <option value="{{ $mataPelajaran }}">{{ $mataPelajaran }}</option>
                                @endforeach
                            </select>

                            <select data-admin-editor-filter="bidang" aria-label="Filter bidang keahlian">
                                <option value="">Bidang Keahlian</option>
                                @foreach ($bidangKeahlianOptions as $bidangKeahlian)
                                    <option value="{{ $bidangKeahlian }}">{{ $bidangKeahlian }}</option>
                                @endforeach
                            </select>

                            <select data-admin-editor-filter="nama" aria-label="Filter nama editor">
                                <option value="">Nama Editor</option>
                                @foreach ($namaEditorOptions as $namaEditor)
                                    <option value="{{ $namaEditor }}">{{ $namaEditor }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="button" class="admin-editor-reset-button" data-admin-editor-reset>
                            Reset
                        </button>

                        <label class="admin-editor-page-size">
                            <span>Tampil</span>
                            <select data-admin-editor-page-size aria-label="Jumlah data per halaman">
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
                    <form method="POST" action="{{ route('admin.data-editor.email.bulk') }}" class="admin-bulk-email-dialog" role="dialog" aria-modal="true" aria-labelledby="bulk-email-editor-title" data-admin-bulk-email-form>
                        @csrf
                        <div data-admin-bulk-email-ids></div>
                        <div class="admin-bulk-email-header">
                            <div>
                                <h2 id="bulk-email-editor-title" class="admin-bulk-email-title">Kirim Email Terpilih</h2>
                                <p class="admin-bulk-email-subtitle">
                                    Email akan dikirim ke <span data-admin-bulk-email-count>0</span> penerima terpilih.
                                </p>
                            </div>
                            <button type="button" class="admin-bulk-email-close" data-admin-bulk-email-close aria-label="Tutup modal">
                                <x-icons.x-close class="h-5 w-5" />
                            </button>
                        </div>
                        <div class="admin-bulk-email-body">
                            <label for="bulk_email_editor_message" class="admin-bulk-email-label">Pesan</label>
                            <textarea id="bulk_email_editor_message" name="pesan" rows="5" maxlength="5000" class="admin-bulk-email-textarea" data-admin-bulk-email-message required></textarea>
                        </div>
                        <div class="admin-bulk-email-actions">
                            <button type="button" class="admin-bulk-email-secondary" data-admin-bulk-email-close>Batal</button>
                            <button type="button" class="admin-bulk-email-primary" data-admin-bulk-email-submit>Kirim Email</button>
                        </div>
                    </form>
                </div>

                <div class="admin-editor-table-wrap">
                    <table class="admin-editor-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="admin-editor-checkbox" data-admin-select-all aria-label="Pilih semua editor">
                                </th>
                                <th>Kode Editor</th>
                                <th>Nama Editor</th>
                                <th>Email</th>
                                <th>Nomor Handphone</th>
                                <th>Bidang Mata Pelajaran</th>
                                <th>Bidang Keahlian</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($editorList as $editor)
                                @php
                                    $searchText = collect([
                                        $editor->kode_editor ?? '#'.$editor->id_editor,
                                        $editor->id_editor,
                                        $editor->nama_lengkap,
                                        $editor->email,
                                        $editor->no_hp,
                                        $editor->mata_pelajaran,
                                        $editor->bidang_keahlian,
                                    ])->filter()->implode(' ');
                                @endphp
                                <tr
                                    data-admin-editor-row
                                    data-search="{{ $searchText }}"
                                    data-filter-mata-pelajaran="{{ $editor->mata_pelajaran }}"
                                    data-filter-bidang="{{ $editor->bidang_keahlian }}"
                                    data-filter-nama="{{ $editor->nama_lengkap }}"
                                >
                                    <td class="text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $editor->id_editor }}" class="admin-editor-checkbox" data-admin-row-checkbox aria-label="Pilih editor {{ $editor->nama_lengkap ?: $editor->id_editor }}">
                                    </td>
                                    <td>{{ $editor->kode_editor ?? '#'.$editor->id_editor }}</td>
                                    <td class="admin-editor-name-cell">{{ $editor->nama_lengkap ?: '-' }}</td>
                                    <td>{{ $editor->email }}</td>
                                    <td>{{ $editor->no_hp ?: '-' }}</td>
                                    <td>{{ $editor->mata_pelajaran ?: '-' }}</td>
                                    <td>{{ $editor->bidang_keahlian ?: '-' }}</td>
                                    <td>
                                        <div class="admin-editor-action-group">
                                            <a
                                                href="{{ route('admin.data-editor.edit', $editor->id_editor) }}"
                                                class="admin-editor-icon-button is-edit"
                                                aria-label="Edit editor {{ $editor->nama_lengkap ?: $editor->id_editor }}"
                                                title="Edit"
                                            >
                                                <x-icons.edit class="h-[17px] w-[17px]" />
                                            </a>

                                            <form method="POST" action="{{ route('admin.data-editor.destroy', $editor->id_editor) }}" class="js-confirm-delete" data-confirm-message="Hapus data editor ini?">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="admin-editor-icon-button is-delete"
                                                    aria-label="Hapus editor {{ $editor->nama_lengkap ?: $editor->id_editor }}"
                                                    title="Hapus"
                                                >
                                                    <x-icons.trash class="h-[17px] w-[17px]" />
                                                </button>
                                            </form>

                                            <button
                                                type="button"
                                                @click="activeEmailModal = 'editor-{{ $editor->id_editor }}'"
                                                class="admin-editor-icon-button is-email"
                                                aria-label="Kirim email ke {{ $editor->nama_lengkap ?: 'editor' }}"
                                                title="Kirim Email"
                                            >
                                                <x-icons.mail class="h-[17px] w-[17px]" />
                                            </button>
                                        </div>

                                        <div
                                            x-show="activeEmailModal === 'editor-{{ $editor->id_editor }}'"
                                            x-cloak
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
                                        >
                                            <div
                                                @click.away="activeEmailModal = null"
                                                class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl"
                                            >
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <h2 class="text-lg font-semibold text-gray-900">Kirim Email ke Editor</h2>
                                                        <p class="mt-1 text-sm text-gray-600">{{ $editor->nama_lengkap ?: 'Editor' }}</p>
                                                    </div>

                                                    <button type="button" @click="activeEmailModal = null" class="text-gray-400 transition hover:text-gray-600">
                                                        <span class="sr-only">Tutup</span>
                                                        <x-icons.x-close class="h-5 w-5" />
                                                    </button>
                                                </div>

                                                <form method="POST" action="{{ route('admin.data-editor.email', $editor->id_editor) }}" class="mt-5 space-y-4">
                                                    @csrf

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Kepada</label>
                                                        <input type="text" value="{{ $editor->email }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-sm shadow-sm" readonly>
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Dari</label>
                                                        <input type="text" value="{{ config('mail.from.address') }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-sm shadow-sm" readonly>
                                                    </div>

                                                    <div>
                                                        <label for="pesan_editor_{{ $editor->id_editor }}" class="block text-sm font-medium text-gray-700">Pesan</label>
                                                        <textarea
                                                            id="pesan_editor_{{ $editor->id_editor }}"
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
                                <tr data-admin-editor-server-empty>
                                    <td colspan="8" class="admin-editor-empty">
                                        Belum ada data editor.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="admin-editor-filter-empty hidden" data-admin-editor-empty>
                                <td colspan="8" class="admin-editor-empty">
                                    Tidak ada data editor yang cocok dengan pencarian atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="admin-editor-pagination-bar">
                    <p class="admin-editor-pagination-info" data-admin-editor-info>Menampilkan 0 dari 0 data</p>
                    <div class="admin-editor-pagination" data-admin-editor-pagination aria-label="Pagination data editor"></div>
                </div>
            </div>
        </div>
    </section>
@endsection
