@extends('layouts.app')

@section('title', 'Pengaturan Layouter')

@section('header')
    <div class="layouter-dashboard-header">
        <div>
            <p class="layouter-dashboard-eyebrow">Pengaturan Akun</p>
            <h1 class="layouter-dashboard-title">PENGATURAN LAYOUTER</h1>
            <p class="layouter-dashboard-subtitle">Kelola nama pengguna, email, dan password akun layouter Anda.</p>
        </div>
    </div>
@endsection

@section('content')
    <section class="layouter-settings">
        <div class="layouter-settings-shell">
            <div class="layouter-settings-grid">
                <article class="layouter-settings-card">
                    <div class="layouter-settings-card-header">
                        <div>
                            <p class="layouter-dashboard-card-eyebrow">Profil</p>
                            <h3 class="layouter-dashboard-card-title">Data Akun Layouter</h3>
                        </div>
                        <span class="layouter-settings-card-icon">
                            <x-icons.user-circle class="h-5 w-5" />
                        </span>
                    </div>

                    <form method="POST" action="{{ route('layouter.pengaturan.profil') }}" class="layouter-settings-form">
                        @csrf
                        @method('PATCH')

                        <div class="layouter-settings-field">
                            <x-input-label for="nama_pengguna" value="Nama Pengguna" />
                            <x-text-input id="nama_pengguna" name="nama_pengguna" type="text" class="layouter-settings-input" :value="old('nama_pengguna', $layouter->nama_lengkap ?: $user->username)" required autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('nama_pengguna')" />
                        </div>

                        <div class="layouter-settings-field">
                            <x-input-label for="email" value="Email" />
                            <x-text-input id="email" name="email" type="email" class="layouter-settings-input" :value="old('email', $user->email)" required autocomplete="email" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div class="layouter-settings-actions">
                            <x-primary-button>Simpan Profil</x-primary-button>
                        </div>
                    </form>
                </article>

                <article class="layouter-settings-card">
                    <div class="layouter-settings-card-header">
                        <div>
                            <p class="layouter-dashboard-card-eyebrow">Informasi</p>
                            <h3 class="layouter-dashboard-card-title">Informasi Diri</h3>
                        </div>
                        <span class="layouter-settings-card-icon">
                            <x-icons.file-text class="h-5 w-5" />
                        </span>
                    </div>

                    <dl class="layouter-settings-info-list">
                        <div class="layouter-settings-info-row">
                            <dt class="layouter-settings-label">Kode Layouter</dt>
                            <dd class="layouter-settings-value">{{ $layouter->kode_layouter ?: '-' }}</dd>
                        </div>
                        <div class="layouter-settings-info-row">
                            <dt class="layouter-settings-label">Bidang Keahlian</dt>
                            <dd class="layouter-settings-value">{{ $layouter->bidang_keahlian ?: '-' }}</dd>
                        </div>
                        <div class="layouter-settings-info-row">
                            <dt class="layouter-settings-label">Kategori Mapel</dt>
                            <dd class="layouter-settings-value">{{ $layouter->kategori_mapel ?: '-' }}</dd>
                        </div>
                        <div class="layouter-settings-info-row">
                            <dt class="layouter-settings-label">Mata Pelajaran</dt>
                            <dd class="layouter-settings-value">{{ $layouter->mata_pelajaran ?: '-' }}</dd>
                        </div>
                        <div class="layouter-settings-info-row">
                            <dt class="layouter-settings-label">Nomor HP</dt>
                            <dd class="layouter-settings-value">{{ $layouter->no_hp ?: '-' }}</dd>
                        </div>
                    </dl>
                </article>
            </div>

            <article class="layouter-settings-card">
                <div class="layouter-settings-card-header">
                    <div>
                        <p class="layouter-dashboard-card-eyebrow">Keamanan</p>
                        <h3 class="layouter-dashboard-card-title">Ubah Password</h3>
                    </div>
                    <span class="layouter-settings-card-icon">
                        <x-icons.lock class="h-5 w-5" />
                    </span>
                </div>

                <form method="POST" action="{{ route('layouter.pengaturan.password') }}" class="layouter-settings-form layouter-settings-password-form">
                    @csrf
                    @method('PATCH')

                    <x-password-toggle-field
                        id="current_password"
                        name="current_password"
                        label="Password Lama"
                        autocomplete="current-password"
                        input-class="layouter-settings-input"
                        :messages="$errors->updatePassword->get('current_password')"
                    />

                    <x-password-toggle-field
                        id="password"
                        name="password"
                        label="Password Baru"
                        autocomplete="new-password"
                        input-class="layouter-settings-input"
                        :messages="$errors->updatePassword->get('password')"
                    />

                    <x-password-toggle-field
                        id="password_confirmation"
                        name="password_confirmation"
                        label="Konfirmasi Password Baru"
                        autocomplete="new-password"
                        input-class="layouter-settings-input"
                        :messages="$errors->updatePassword->get('password_confirmation')"
                    />

                    <div class="layouter-settings-actions layouter-settings-password-actions">
                        <x-primary-button>Simpan Password</x-primary-button>
                    </div>
                </form>
            </article>
        </div>
    </section>
@endsection
