@extends('layouts.app')

@section('title', 'Edit Data Penulis')

@section('header')
    <div class="admin-create-header">
        <div>
            <h1 class="admin-create-title">Edit Data Penulis</h1>
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
                        <p class="admin-create-card-title">Form Edit Penulis</p>
                        <p class="admin-create-card-subtitle">Perbarui biodata penulis tanpa mengubah password, role, atau kode penulis.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.data-penulis.update', $penulis->id_penulis) }}" class="admin-create-form">
                    @csrf
                    @method('PATCH')

                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="nama_lengkap" class="admin-form-label">Nama Penulis</label>
                            <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap', $penulis->nama_lengkap) }}" class="admin-form-input" required autofocus>
                            @error('nama_lengkap')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $penulis->email) }}" class="admin-form-input" required>
                            @error('email')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="no_hp" class="admin-form-label">Nomor Handphone</label>
                            <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $penulis->no_hp) }}" class="admin-form-input">
                            @error('no_hp')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="profesi" class="admin-form-label">Profesi</label>
                            <input id="profesi" name="profesi" type="text" value="{{ old('profesi', $penulis->profesi) }}" class="admin-form-input" required>
                            @error('profesi')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="jurusan_pendidikan" class="admin-form-label">Jurusan Pendidikan</label>
                            <input id="jurusan_pendidikan" name="jurusan_pendidikan" type="text" value="{{ old('jurusan_pendidikan', $penulis->jurusan_pendidikan) }}" class="admin-form-input" placeholder="Contoh: Ilmu Komputer, Teknik Informatika">
                            @error('jurusan_pendidikan')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label for="alamat" class="admin-form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" class="admin-form-textarea" required>{{ old('alamat', $penulis->alamat) }}</textarea>
                        @error('alamat')
                            <p class="admin-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="admin-form-alert">
                        Perubahan nama lengkap juga akan menyinkronkan username akun penulis. Password, role, dan kode penulis tidak diubah dari halaman ini.
                    </div>

                    <div class="admin-form-actions">
                        <a href="{{ route('admin.data-penulis.index') }}" class="admin-btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="admin-btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
