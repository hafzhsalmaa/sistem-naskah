@extends('layouts.app')

@section('title', 'Pengaturan Editor')

@section('header')
    <div class="editor-dashboard-header">
        <div>
            <p class="editor-dashboard-eyebrow">Pengaturan Akun</p>
            <h1 class="editor-dashboard-title">PENGATURAN EDITOR</h1>
            <p class="editor-dashboard-subtitle">Kelola nama pengguna, email, dan password akun editor Anda.</p>
        </div>
    </div>
@endsection

@section('content')
    <section class="editor-settings">
        <div class="editor-settings-shell">
            <div class="editor-settings-grid">
                <article class="editor-settings-card">
                    <div class="editor-settings-card-header">
                        <div>
                            <p class="editor-dashboard-card-eyebrow">Profil</p>
                            <h3 class="editor-dashboard-card-title">Data Akun Editor</h3>
                        </div>
                        <span class="editor-settings-card-icon">
                            <x-icons.user-circle class="h-5 w-5" />
                        </span>
                    </div>

                    <form method="POST" action="{{ route('editor.pengaturan.profil') }}" class="editor-settings-form">
                        @csrf
                        @method('PATCH')

                        <div class="editor-settings-field">
                            <x-input-label for="nama_pengguna" value="Nama Pengguna" />
                            <x-text-input id="nama_pengguna" name="nama_pengguna" type="text" class="editor-settings-input" :value="old('nama_pengguna', $editor->nama_lengkap ?: $user->username)" required autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('nama_pengguna')" />
                        </div>

                        <div class="editor-settings-field">
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="editor-settings-input" :value="old('email', $user->email)" required autocomplete="email" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div class="editor-settings-actions">
                            <x-primary-button>Simpan Profil</x-primary-button>
                        </div>
                    </form>
                </article>

                <article class="editor-settings-card">
                    <div class="editor-settings-card-header">
                        <div>
                            <p class="editor-dashboard-card-eyebrow">Informasi</p>
                            <h3 class="editor-dashboard-card-title">Informasi Diri</h3>
                        </div>
                        <span class="editor-settings-card-icon">
                            <x-icons.file-text class="h-5 w-5" />
                        </span>
                    </div>

                    <dl class="editor-settings-info-list">
                        <div class="editor-settings-info-row">
                            <dt class="editor-settings-label">Kode Editor</dt>
                            <dd class="editor-settings-value">{{ $editor->kode_editor ?: '-' }}</dd>
                        </div>
                        <div class="editor-settings-info-row">
                            <dt class="editor-settings-label">Bidang Keahlian</dt>
                            <dd class="editor-settings-value">{{ $editor->bidang_keahlian ?: '-' }}</dd>
                        </div>
                        <div class="editor-settings-info-row">
                            <dt class="editor-settings-label">Kategori Mapel</dt>
                            <dd class="editor-settings-value">{{ $editor->kategori_mapel ?: '-' }}</dd>
                        </div>
                        <div class="editor-settings-info-row">
                            <dt class="editor-settings-label">Mata Pelajaran</dt>
                            <dd class="editor-settings-value">{{ $editor->mata_pelajaran ?: '-' }}</dd>
                        </div>
                        <div class="editor-settings-info-row">
                            <dt class="editor-settings-label">Nomor HP</dt>
                            <dd class="editor-settings-value">{{ $editor->no_hp ?: '-' }}</dd>
                        </div>
                    </dl>
                </article>
            </div>

            <article class="editor-settings-card">
                <div class="editor-settings-card-header">
                    <div>
                        <p class="editor-dashboard-card-eyebrow">Keamanan</p>
                        <h3 class="editor-dashboard-card-title">Ubah Password</h3>
                    </div>
                    <span class="editor-settings-card-icon">
                        <x-icons.lock class="h-5 w-5" />
                    </span>
                </div>

                <form method="POST" action="{{ route('editor.pengaturan.password') }}" class="editor-settings-form editor-settings-password-form">
                    @csrf
                    @method('PATCH')

                    <x-password-toggle-field
                        id="current_password"
                        name="current_password"
                        label="Password Lama"
                        autocomplete="current-password"
                        input-class="editor-settings-input"
                        :messages="$errors->updatePassword->get('current_password')"
                    />

                    <x-password-toggle-field
                        id="password"
                        name="password"
                        label="Password Baru"
                        autocomplete="new-password"
                        input-class="editor-settings-input"
                        :messages="$errors->updatePassword->get('password')"
                    />

                    <x-password-toggle-field
                        id="password_confirmation"
                        name="password_confirmation"
                        label="Konfirmasi Password Baru"
                        autocomplete="new-password"
                        input-class="editor-settings-input"
                        :messages="$errors->updatePassword->get('password_confirmation')"
                    />

                    <div class="editor-settings-actions editor-settings-password-actions">
                        <x-primary-button>Simpan Password</x-primary-button>
                    </div>
                </form>
            </article>
        </div>
    </section>
@endsection
