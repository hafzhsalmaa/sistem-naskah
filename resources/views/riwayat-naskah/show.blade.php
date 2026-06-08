@extends('layouts.app')

@section('title', 'Detail Riwayat Naskah')

@section('header')
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-2xl font-semibold text-gray-900">Detail Riwayat Naskah</h1>
        <a href="{{ url()->previous() }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <section class="py-12">
        <div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 px-6 py-4">
                    <p class="text-lg font-medium text-gray-900">{{ $naskah->judul }}</p>
                    <p class="mt-1 text-sm text-gray-600">
                        Penulis: {{ $naskah->nama_penulis }} | Editor: {{ $naskah->nama_editor ?? '-' }} | Layouter: {{ $naskah->nama_layouter ?? '-' }}
                    </p>
                </div>

                <div class="grid gap-4 px-6 py-6 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Kelas</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $naskah->kelas }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Kurikulum</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $naskah->kurikulum }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Kategori Mapel</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $naskah->kategori_mapel }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Mata Pelajaran</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $naskah->mata_pelajaran }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Deskripsi</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $naskah->deskripsi ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <x-status-naskah-badge :status="$naskah->status_naskah" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 px-6 py-4">
                    <p class="text-lg font-medium text-gray-900">Seluruh Versi Naskah</p>
                    <p class="mt-1 text-sm text-gray-600">Mencakup upload awal, revisi penulis, dan hasil layout jika tersedia.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Versi</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Uploader</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Upload</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama File</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($historyItems as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->label_versi }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $item->uploader ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                        {{ $item->tanggal_upload ? \Illuminate\Support\Carbon::parse($item->tanggal_upload)->format('d M Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $item->nama_file_asli ?? basename($item->file_path) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <a
                                            href="{{ route($downloadRoute, ['id' => $naskah->id_naskah, 'source' => $item->source, 'ref' => $item->ref_id]) }}"
                                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50"
                                        >
                                            Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Belum ada data versi naskah.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
