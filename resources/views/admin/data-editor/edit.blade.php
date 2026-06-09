@extends('layouts.app')

@section('title', 'Edit Data Editor')

@section('header')
    <div class="admin-create-header">
        <div>
            <h1 class="admin-create-title">Edit Data Editor</h1>
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
                        <p class="admin-create-card-title">Form Edit Editor</p>
                        <p class="admin-create-card-subtitle">Perbarui data editor tanpa mengubah password, role, kode editor, atau workflow review.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.data-editor.update', $editor->id_editor) }}" class="admin-create-form">
                    @csrf
                    @method('PATCH')

                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label for="nama_lengkap" class="admin-form-label">Nama Editor</label>
                            <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap', $editor->nama_lengkap) }}" class="admin-form-input" required autofocus>
                            @error('nama_lengkap')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="email" class="admin-form-label">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $editor->email) }}" class="admin-form-input" required>
                            @error('email')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="no_hp" class="admin-form-label">Nomor Handphone</label>
                            <input id="no_hp" name="no_hp" type="text" value="{{ old('no_hp', $editor->no_hp) }}" class="admin-form-input">
                            @error('no_hp')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="bidang_keahlian" class="admin-form-label">Bidang Keahlian</label>
                            <select id="bidang_keahlian" name="bidang_keahlian" class="admin-form-select" required>
                                <option value="">Pilih bidang keahlian</option>
                                <option value="SD/MI" @selected(old('bidang_keahlian', $editor->bidang_keahlian) === 'SD/MI')>SD/MI</option>
                                <option value="SMP/MTS" @selected(old('bidang_keahlian', $editor->bidang_keahlian) === 'SMP/MTS')>SMP/MTS</option>
                                <option value="SMA/MA/SMK" @selected(old('bidang_keahlian', $editor->bidang_keahlian) === 'SMA/MA/SMK')>SMA/MA/SMK</option>
                            </select>
                            @error('bidang_keahlian')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="kategori_mapel" class="admin-form-label">Kategori Mapel</label>
                            <select id="kategori_mapel" name="kategori_mapel" class="admin-form-select" data-mapel-category-select required>
                                <option value="">Pilih kategori mapel</option>
                                <option value="Umum" @selected(old('kategori_mapel', $editor->kategori_mapel) === 'Umum')>Umum</option>
                                <option value="Bahasa" @selected(old('kategori_mapel', $editor->kategori_mapel) === 'Bahasa')>Bahasa</option>
                                <option value="Agama" @selected(old('kategori_mapel', $editor->kategori_mapel) === 'Agama')>Agama</option>
                            </select>
                            @error('kategori_mapel')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-form-group">
                            <label for="mata_pelajaran" class="admin-form-label">Mata Pelajaran</label>
                            <select id="mata_pelajaran" name="mata_pelajaran" class="admin-form-select" data-mapel-select data-selected-mapel="{{ old('mata_pelajaran', $editor->mata_pelajaran) }}" required>
                                <option value="">Pilih kategori mapel terlebih dahulu</option>
                            </select>
                            @error('mata_pelajaran')
                                <p class="admin-form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="admin-form-alert">
                        Perubahan ini hanya memperbarui biodata editor. Assignment dan workflow review naskah tetap mengikuti data yang sudah berjalan.
                    </div>

                    <div class="admin-form-actions">
                        <a href="{{ route('admin.data-editor.index') }}" class="admin-btn-secondary">
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
