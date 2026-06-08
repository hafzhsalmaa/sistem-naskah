@extends('layouts.app')

@section('title', 'Tambah Naskah')

@section('header')
    <div class="penulis-create-heading">
        <div>
            <p class="penulis-create-kicker">Kirim Naskah</p>
            <h1>Tambah Naskah</h1>
        </div>
        <a href="{{ route('penulis.dashboard') }}" class="penulis-create-back-link">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <section class="penulis-create-page">
        <div class="penulis-create-shell">
            <div class="penulis-create-grid">
                <article class="penulis-create-card">
                    @if (session('status'))
                        <div class="penulis-create-status" data-flash-auto-hide>
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('penulis.naskah.store') }}" enctype="multipart/form-data" class="penulis-create-form">
                        @csrf

                        <div class="penulis-create-field">
                            <label for="judul">Judul</label>
                            <input
                                id="judul"
                                name="judul"
                                type="text"
                                value="{{ old('judul') }}"
                                class="penulis-create-control"
                                required
                            >
                            <x-input-error :messages="$errors->get('judul')" class="mt-2" />
                        </div>

                        <div class="penulis-create-field">
                            <label for="kelas">Kelas</label>
                            <input
                                id="kelas"
                                name="kelas"
                                type="text"
                                value="{{ old('kelas') }}"
                                class="penulis-create-control"
                                required
                            >
                            <x-input-error :messages="$errors->get('kelas')" class="mt-2" />
                            <p class="penulis-create-help">
                                Kelas harus sesuai dengan jenjang: SD/MI 1-6, SMP/MTS 7-9, SMA/MA/SMK 10-12.
                            </p>
                        </div>

                        <div class="penulis-create-field">
                            <label for="bidang_keahlian">Jenjang Naskah</label>
                            <select
                                id="bidang_keahlian"
                                name="bidang_keahlian"
                                class="penulis-create-control"
                                required
                            >
                                <option value="">Pilih jenjang naskah</option>
                                <option value="SD" @selected(old('bidang_keahlian') === 'SD')>SD</option>
                                <option value="SMP" @selected(old('bidang_keahlian') === 'SMP')>SMP</option>
                                <option value="SMA" @selected(old('bidang_keahlian') === 'SMA')>SMA</option>
                                <option value="MI" @selected(old('bidang_keahlian') === 'MI')>MI</option>
                                <option value="MTS" @selected(old('bidang_keahlian') === 'MTS')>MTS</option>
                                <option value="MA" @selected(old('bidang_keahlian') === 'MA')>MA</option>
                                <option value="SMK" @selected(old('bidang_keahlian') === 'SMK')>SMK</option>
                            </select>
                            <x-input-error :messages="$errors->get('bidang_keahlian')" class="mt-2" />
                        </div>

                        <div class="penulis-create-inline-grid">
                            <div class="penulis-create-field">
                                <label for="kurikulum">Kurikulum</label>
                                <select
                                    id="kurikulum"
                                    name="kurikulum"
                                    class="penulis-create-control"
                                    required
                                >
                                    <option value="">Pilih kurikulum</option>
                                    <option value="Merdeka" @selected(old('kurikulum') === 'Merdeka')>Merdeka</option>
                                    <option value="K13" @selected(old('kurikulum') === 'K13')>K13</option>
                                </select>
                                <x-input-error :messages="$errors->get('kurikulum')" class="mt-2" />
                            </div>

                            <div class="penulis-create-field">
                                <label for="kategori_mapel">Kategori Mapel</label>
                                <select
                                    id="kategori_mapel"
                                    name="kategori_mapel"
                                    class="penulis-create-control"
                                    required
                                >
                                    <option value="">Pilih kategori mapel</option>
                                    <option value="Umum" @selected(old('kategori_mapel') === 'Umum')>Umum</option>
                                    <option value="Bahasa" @selected(old('kategori_mapel') === 'Bahasa')>Bahasa</option>
                                    <option value="Agama" @selected(old('kategori_mapel') === 'Agama')>Agama</option>
                                </select>
                                <x-input-error :messages="$errors->get('kategori_mapel')" class="mt-2" />
                            </div>
                        </div>

                        <div class="penulis-create-field">
                            <label for="mata_pelajaran">Mata Pelajaran</label>
                            <select
                                id="mata_pelajaran"
                                name="mata_pelajaran"
                                class="penulis-create-control"
                            >
                                <option value="">Pilih mata pelajaran</option>
                                <option value="IPA" @selected(old('mata_pelajaran') === 'IPA')>IPA</option>
                                <option value="IPS" @selected(old('mata_pelajaran') === 'IPS')>IPS</option>
                                <option value="Matematika" @selected(old('mata_pelajaran') === 'Matematika')>Matematika</option>
                                <option value="Bahasa Indonesia" @selected(old('mata_pelajaran') === 'Bahasa Indonesia')>Bahasa Indonesia</option>
                                <option value="Bahasa Inggris" @selected(old('mata_pelajaran') === 'Bahasa Inggris')>Bahasa Inggris</option>
                                <option value="Sejarah" @selected(old('mata_pelajaran') === 'Sejarah')>Sejarah</option>
                                <option value="Agama" @selected(old('mata_pelajaran') === 'Agama')>Agama</option>
                                <option value="Bahasa Jawa" @selected(old('mata_pelajaran') === 'Bahasa Jawa')>Bahasa Jawa</option>
                            </select>
                            <x-input-error :messages="$errors->get('mata_pelajaran')" class="mt-2" />
                        </div>

                        <div class="penulis-create-field">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                rows="4"
                                class="penulis-create-control penulis-create-textarea"
                            >{{ old('deskripsi') }}</textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        <div class="penulis-create-field">
                            <label for="file_naskah">File Naskah</label>
                            <div class="penulis-create-upload">
                                <div class="penulis-create-upload-copy">
                                    <strong>Pilih dokumen naskah</strong>
                                    <p>Format file: DOCX. Ukuran maksimal 50 MB.</p>
                                </div>
                                <input
                                    id="file_naskah"
                                    name="file_naskah"
                                    type="file"
                                    accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                    class="penulis-create-file-input"
                                    required
                                >
                            </div>
                            <x-input-error :messages="$errors->get('file_naskah')" class="mt-2" />
                        </div>

                        <div class="penulis-create-actions">
                            <button
                                type="submit"
                                class="penulis-create-submit"
                            >
                                Simpan Naskah
                            </button>
                        </div>
                    </form>
                </article>

                <aside class="penulis-create-info-card">
                    <div class="penulis-create-info-head">
                        <p class="penulis-create-kicker">Panduan Pengiriman</p>
                        <h2>Periksa sebelum submit</h2>
                        <p>Gunakan data yang tepat agar proses review berikutnya berjalan lebih lancar.</p>
                    </div>

                    <div class="penulis-create-guidelines">
                        <div>
                            <span>01</span>
                            <p>Pastikan naskah sesuai kurikulum yang dipilih.</p>
                        </div>
                        <div>
                            <span>02</span>
                            <p>File wajib berformat DOCX dengan ukuran maksimal 50 MB.</p>
                        </div>
                        <div>
                            <span>03</span>
                            <p>Naskah tidak mengandung konten sensitif atau SARA.</p>
                        </div>
                        <div>
                            <span>04</span>
                            <p>Naskah akan melewati review, editing, layout, dan penerbitan.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
