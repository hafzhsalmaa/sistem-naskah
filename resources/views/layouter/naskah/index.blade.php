@extends('layouts.app')

@section('title', 'Data Naskah Layouter')

@section('header')
    <h1 class="text-2xl font-semibold text-gray-900">Data Naskah Layouter</h1>
@endsection

@section('content')
    <section class="py-12">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" data-flash-auto-hide>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-8">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <p class="text-lg font-medium text-gray-900">Naskah Masuk / Sedang Dikerjakan</p>
                        <p class="mt-1 text-sm text-gray-600">Naskah yang sudah dikirim ke layouter dan masih dalam proses pengerjaan.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Penulis</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Editor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Mata Pelajaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Diterima</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($naskahAktif as $naskah)
                                    <tr>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $naskah->judul }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $naskah->nama_penulis }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $naskah->nama_editor ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $naskah->mata_pelajaran }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ $naskah->tanggal_upload_terbaru ? \Illuminate\Support\Carbon::parse($naskah->tanggal_upload_terbaru)->format('d M Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a
                                                    href="{{ route('layouter.naskah.show', $naskah->id_naskah) }}"
                                                    class="layouter-naskah-detail-button"
                                                >
                                                    Lihat Detail
                                                </a>
                                                {{-- <a
                                                    href="{{ route('layouter.naskah.download', $naskah->id_naskah) }}"
                                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50"
                                                >
                                                    Download File
                                                </a> --}}
                                                {{-- <a
                                                    href="{{ route('layouter.naskah.show', $naskah->id_naskah) }}#upload-layout"
                                                    class="inline-flex items-center rounded-md border border-blue-300 bg-blue-50 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-blue-700 transition hover:bg-blue-100"
                                                >
                                                    Upload Hasil Layout
                                                </a>
                                                <form method="POST" action="{{ route('layouter.naskah.selesai', $naskah->id_naskah) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center rounded-md border border-emerald-300 bg-emerald-50 px-3 py-2 text-xs font-semibold uppercase tracking-widest text-emerald-800 transition hover:bg-emerald-100"
                                                    >
                                                        Selesaikan
                                                    </button>
                                                </form> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Belum ada naskah layout yang aktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <p class="text-lg font-medium text-gray-900">Layout Selesai</p>
                        <p class="mt-1 text-sm text-gray-600">Naskah yang sudah selesai dikerjakan oleh layouter.</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Penulis</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama Editor</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Selesai</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($naskahSelesai as $naskah)
                                    <tr>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $naskah->judul }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $naskah->nama_penulis }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $naskah->nama_editor ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                            {{ $naskah->tanggal_layout ? \Illuminate\Support\Carbon::parse($naskah->tanggal_layout)->format('d M Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                                            <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            <a
                                                href="{{ route('layouter.naskah.show', $naskah->id_naskah) }}"
                                                class="layouter-naskah-detail-button"
                                            >
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                            Belum ada layout yang selesai.
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
