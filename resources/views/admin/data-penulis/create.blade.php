@extends('layouts.app')

@section('title', 'Tambah Data Penulis')

@section('header')
    <div class="admin-create-header">
        <div>
            <h1 class="admin-create-title">Tambah Data Penulis</h1>
        </div>
        <a href="{{ route('admin.data-penulis.index') }}" class="admin-btn-secondary">
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
                        <p class="admin-create-card-title">Form Tambah Penulis</p>
                        <p class="admin-create-card-subtitle">Buat akun login penulis beserta biodata utama yang diperlukan sistem.</p>
                    </div>
                </div>

                <form
                    method="POST"
                    action="{{ route('admin.data-penulis.store') }}"
                    class="admin-create-form"
                    x-data="{ profesi: @js(old('profesi', '')) }"
                >
                    @csrf

                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="nama_lengkap" class="admin-form-label">Nama Lengkap</label>
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
                            <label for="profesi" class="admin-form-label">Profesi</label>
                            <select id="profesi" name="profesi" x-model="profesi" class="admin-form-select" required>
                                <option value="">Pilih profesi</option>
                                <option value="Guru" @selected(old('profesi') === 'Guru')>Guru</option>
                                <option value="Dosen" @selected(old('profesi') === 'Dosen')>Dosen</option>
                                <option value="Karyawan" @selected(old('profesi') === 'Karyawan')>Karyawan</option>
                                <option value="Professor" @selected(old('profesi') === 'Professor')>Professor</option>
                                <option value="Lainnya" @selected(old('profesi') === 'Lainnya')>Lainnya</option>
                            </select>
                            @error('profesi')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group" x-show="profesi === 'Lainnya'" x-cloak>
                            <label for="profesi_lainnya" class="admin-form-label">Profesi Manual</label>
                            <input id="profesi_lainnya" name="profesi_lainnya" type="text" value="{{ old('profesi_lainnya') }}" class="admin-form-input">
                            @error('profesi_lainnya')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="jurusan_pendidikan" class="admin-form-label">Jurusan Pendidikan</label>
                            <input id="jurusan_pendidikan" name="jurusan_pendidikan" type="text" value="{{ old('jurusan_pendidikan') }}" placeholder="Contoh: Ilmu Komputer, Teknik Informatika" class="admin-form-input" required>
                            @error('jurusan_pendidikan')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="admin-form-group">
                        <label for="alamat" class="admin-form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" class="admin-form-textarea" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="admin-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-form-alert">
                        Akun akan dibuat dengan role <strong>Penulis</strong>. Nama lengkap juga dipakai sebagai username login agar konsisten dengan data biodata penulis.
                    </div>

                    <div class="admin-form-actions">
                        <a href="{{ route('admin.data-penulis.index') }}" class="admin-btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="admin-btn-primary">
                            Simpan Penulis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
