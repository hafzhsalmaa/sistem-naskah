@extends('layouts.app')

@section('title', 'Tambah Data Editor')

@section('header')
    <div class="admin-create-header">
        <div>
            <h1 class="admin-create-title">Tambah Data Editor</h1>
        </div>
        <a href="{{ route('admin.data-editor.index') }}" class="admin-btn-secondary">
            Kembali
        </a>
    </div>
@endsection

@section('content')
    <section class="admin-create-page">
        <div class="admin-create-shell">
            <div class="admin-create-card">
                <div class="admin-create-card-header">
                    <div>
                        <p class="admin-create-card-title">Form Tambah Editor</p>
                        <p class="admin-create-card-subtitle">Buat akun login editor beserta bidang review naskah yang akan ditangani.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.data-editor.store') }}" class="admin-create-form">
                    @csrf

                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="nama_lengkap" class="admin-form-label">Nama Editor</label>
                            <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap') }}" class="admin-form-input" required autofocus>
                            @error('nama_lengkap')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="admin-form-input" required>
                            @error('email')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="password" class="admin-form-label">Password</label>
                            <div class="admin-password-field" data-password-toggle>
                                <input id="password" name="password" type="password" class="admin-form-input admin-password-input" required autocomplete="new-password" data-password-input>
                                <button type="button" class="admin-password-toggle" aria-label="Tampilkan password" aria-pressed="false">
                                    <x-icons.eye data-password-icon-show class="admin-password-icon is-hidden" />
                                    <x-icons.eye-off data-password-icon-hide class="admin-password-icon" />
                                </button>
                            </div>
                            @error('password')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="password_confirmation" class="admin-form-label">Konfirmasi Password</label>
                            <div class="admin-password-field" data-password-toggle>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="admin-form-input admin-password-input" required autocomplete="new-password" data-password-input>
                                <button type="button" class="admin-password-toggle" aria-label="Tampilkan konfirmasi password" aria-pressed="false">
                                    <x-icons.eye data-password-icon-show class="admin-password-icon is-hidden" />
                                    <x-icons.eye-off data-password-icon-hide class="admin-password-icon" />
                                </button>
                            </div>
                        </div>

                        <div class="admin-form-group">
                            <label for="no_hp" class="admin-form-label">Nomor Handphone</label>
                            <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp') }}" class="admin-form-input" required>
                            @error('no_hp')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="bidang_keahlian" class="admin-form-label">Bidang Keahlian</label>
                            <select id="bidang_keahlian" name="bidang_keahlian" class="admin-form-select" required>
                                <option value="">Pilih bidang keahlian</option>
                                <option value="SD/MI" @selected(old('bidang_keahlian') === 'SD/MI')>SD/MI</option>
                                <option value="SMP/MTS" @selected(old('bidang_keahlian') === 'SMP/MTS')>SMP/MTS</option>
                                <option value="SMA/MA/SMK" @selected(old('bidang_keahlian') === 'SMA/MA/SMK')>SMA/MA/SMK</option>
                            </select>
                            @error('bidang_keahlian')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="kategori_mapel" class="admin-form-label">Kategori Mapel</label>
                            <select id="kategori_mapel" name="kategori_mapel" class="admin-form-select" data-mapel-category-select required>
                                <option value="">Pilih kategori mapel</option>
                                <option value="Umum" @selected(old('kategori_mapel') === 'Umum')>Umum</option>
                                <option value="Bahasa" @selected(old('kategori_mapel') === 'Bahasa')>Bahasa</option>
                                <option value="Agama" @selected(old('kategori_mapel') === 'Agama')>Agama</option>
                            </select>
                            @error('kategori_mapel')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="mata_pelajaran" class="admin-form-label">Mata Pelajaran</label>
                            <select id="mata_pelajaran" name="mata_pelajaran" class="admin-form-select" data-mapel-select data-selected-mapel="{{ old('mata_pelajaran') }}" required>
                                <option value="">Pilih kategori mapel terlebih dahulu</option>
                            </select>
                            @error('mata_pelajaran')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="admin-form-alert">
                        Akun akan dibuat dengan role <strong>Editor</strong>. Nama editor juga dipakai sebagai username login agar konsisten dengan data akun.
                    </div>

                    <div class="admin-form-actions">
                        <a href="{{ route('admin.data-editor.index') }}" class="admin-btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="admin-btn-primary">
                            Simpan Editor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
