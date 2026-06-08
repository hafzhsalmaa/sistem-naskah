@extends('layouts.app')

@section('title', 'Data Naskah Editor')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900">Data Naskah Editor</h1>
@endsection

@section('content')
    <section class="py-12">
        <div x-data="{ activeLayouterModal: null }" class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('id_layouter'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first('id_layouter') }}
                </div>
            @endif

            <div class="space-y-8">
                <div id="naskah-masuk" class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <p class="text-lg font-medium text-gray-900">Naskah Masuk</p>
                        <p class="mt-1 text-sm text-gray-600">Naskah baru yang siap dicek dan direview editor.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Penulis</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">File</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($naskahMasuk as $naskah)
                                    <tr>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $naskah->judul }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $naskah->nama_penulis }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
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
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a
                                                    href="{{ route('editor.naskah.show', $naskah->id_naskah) }}"
                                                    class="editor-naskah-detail-button"
                                                >
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Belum ada naskah masuk editor.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="naskah-revisi" class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <p class="text-lg font-medium text-gray-900">Naskah Revisi</p>
                        <p class="mt-1 text-sm text-gray-600">Naskah yang sedang berjalan dalam proses revisi antara editor dan penulis.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Penulis</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Revisi Terakhir</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($naskahRevisi as $naskah)
                                    <tr>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $naskah->judul }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $naskah->nama_penulis }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ $naskah->tanggal_revisi_terakhir ? \Illuminate\Support\Carbon::parse($naskah->tanggal_revisi_terakhir)->format('d M Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <a
                                                href="{{ route('editor.naskah.show', $naskah->id_naskah) }}"
                                                class="editor-naskah-detail-button"
                                            >
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Belum ada naskah revisi editor.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="selesai-review" class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <p class="text-lg font-medium text-gray-900">Selesai Review</p>
                        <p class="mt-1 text-sm text-gray-600">Naskah yang sudah selesai ditinjau editor dan menunggu keputusan akhir atau pengiriman ke layouter.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Penulis</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($naskahSelesaiReview as $naskah)
                                    @php
                                        $pemeriksaanAwalSelesai = (bool) $naskah->cek_kurikulum
                                            && (bool) $naskah->cek_silabus
                                            && (bool) $naskah->cek_rpp
                                            && (bool) $naskah->bebas_sara;
                                        $hasFileFinalEditor = filled($naskah->file_final_editor_path ?? null);
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $naskah->judul }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $naskah->nama_penulis }}</td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a
                                                    href="{{ route('editor.naskah.show', $naskah->id_naskah) }}"
                                                    class="editor-naskah-detail-button"
                                                >
                                                    Lihat Detail
                                                </a>

                                                @if ($naskah->status_naskah === 'Diterima' && $pemeriksaanAwalSelesai && $hasFileFinalEditor)
                                                    <button
                                                        type="button"
                                                        @click="activeLayouterModal = 'layouter-{{ $naskah->id_naskah }}'"
                                                        class="inline-flex items-center rounded-md border border-green-300 bg-green-50 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-green-700 transition hover:bg-green-100"
                                                    >
                                                        Kirim ke Layouter
                                                    </button>
                                                @elseif ($naskah->status_naskah === 'Diterima' && $pemeriksaanAwalSelesai)
                                                    <button
                                                        type="button"
                                                        class="editor-naskah-secondary-button is-disabled"
                                                        disabled
                                                        title="File Final Editor belum diupload. Silakan upload file final editor melalui halaman detail naskah sebelum mengirim ke Layouter."
                                                    >
                                                        Kirim ke Layouter
                                                    </button>
                                                    <a
                                                        href="{{ route('editor.naskah.show', $naskah->id_naskah) }}#file-final-editor"
                                                        class="editor-naskah-warning-pill"
                                                        title="Upload File Final Editor di halaman detail naskah"
                                                    >
                                                        Upload File Final Editor
                                                    </a>
                                                @elseif ($naskah->status_naskah === 'Diterima')
                                                    <span class="inline-flex items-center rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-amber-800">
                                                        Lengkapi Pemeriksaan Awal
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($naskah->status_naskah === 'Diterima' && $pemeriksaanAwalSelesai && $hasFileFinalEditor)
                                                <div
                                                    x-show="activeLayouterModal === 'layouter-{{ $naskah->id_naskah }}'"
                                                    x-cloak
                                                    class="editor-naskah-modal-overlay"
                                                >
                                                    <div
                                                        @click.away="activeLayouterModal = null"
                                                        class="editor-naskah-modal-card"
                                                    >
                                                        <div class="editor-naskah-modal-header">
                                                            <div>
                                                                <h2 class="editor-naskah-modal-title">Kirim Naskah ke Layouter</h2>
                                                                <p class="editor-naskah-modal-subtitle">{{ $naskah->judul }}</p>
                                                                <p class="editor-naskah-modal-meta">Penulis: {{ $naskah->nama_penulis }}</p>
                                                            </div>

                                                            <button
                                                                type="button"
                                                                @click="activeLayouterModal = null"
                                                                class="editor-naskah-modal-close"
                                                            >
                                                                <span class="sr-only">Tutup</span>
                                                                <x-icons.x-close class="h-5 w-5" />
                                                            </button>
                                                        </div>

                                                        <form
                                                            method="POST"
                                                            action="{{ route('editor.naskah.kirim-layouter', $naskah->id_naskah) }}"
                                                            class="editor-naskah-modal-form"
                                                        >
                                                            @csrf

                                                            <div class="editor-naskah-modal-info is-success">
                                                                File Final Editor sudah tersedia dan akan menjadi dokumen utama untuk Layouter.
                                                            </div>

                                                            <div class="editor-naskah-modal-field">
                                                                <label for="id_layouter_{{ $naskah->id_naskah }}">
                                                                    Pilih Layouter
                                                                </label>
                                                                <select
                                                                    id="id_layouter_{{ $naskah->id_naskah }}"
                                                                    name="id_layouter"
                                                                    required
                                                                >
                                                                    <option value="">Pilih layouter</option>
                                                                    @foreach (($layouterMap[$naskah->id_naskah] ?? collect()) as $layouter)
                                                                        <option value="{{ $layouter->id_layouter }}">
                                                                            {{ $layouter->username }} &bull; {{ $layouter->naskah_aktif_count ?? 0 }} Naskah Aktif
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="editor-naskah-modal-actions">
                                                                <button
                                                                    type="button"
                                                                    @click="activeLayouterModal = null"
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
                                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Belum ada naskah selesai review.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
