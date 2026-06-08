@php
    $penulisProfile = $user->penulis;
    $profileName = $penulisProfile?->nama_lengkap ?: $user->username;
    $profileInitials = \Illuminate\Support\Str::of($profileName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('') ?: 'P';
    $profileDetails = [
        'Nama Lengkap' => $penulisProfile?->nama_lengkap ?: '-',
        'Email' => $user->email ?: '-',
        'Nomor Handphone' => $penulisProfile?->no_hp ?: '-',
        'Alamat' => $penulisProfile?->alamat ?: '-',
        'Profesi' => $penulisProfile?->profesi ?: '-',
        'Jurusan Pendidikan' => $penulisProfile?->jurusan_pendidikan ?: '-',
        'Kode Penulis' => $penulisProfile?->kode_penulis ?: '#'.($penulisProfile?->id_penulis ?? '-'),
    ];
@endphp

<x-app-layout>
    <x-slot name="title">
        {{ $user->role === 'penulis' ? 'Biodata Penulis' : 'Profile' }}
    </x-slot>

    <x-slot name="header">
        @if ($user->role === 'penulis')
            <div class="penulis-profile-heading">
                <h1>Biodata Penulis</h1>
            </div>
        @else
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>
        @endif
    </x-slot>

    @if ($user->role === 'penulis')
        <section class="penulis-profile-page">
            <div class="penulis-profile-shell">
                <div class="penulis-profile-overview">
                    <article class="penulis-profile-card penulis-profile-identity">
                    <div class="penulis-profile-avatar-wrap">
                        <div class="penulis-profile-avatar-fallback">{{ $profileInitials }}</div>
                    </div>

                    <div class="penulis-profile-identity-copy">
                        <span class="penulis-profile-chip">Penulis</span>
                        <h2>{{ $profileName }}</h2>
                        <p>{{ $penulisProfile?->kode_penulis ?: '#'.($penulisProfile?->id_penulis ?? '-') }}</p>
                    </div>

                    <dl class="penulis-profile-summary">
                        <div>
                            <dt>Profesi</dt>
                            <dd>{{ $penulisProfile?->profesi ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt>Jurusan</dt>
                            <dd>{{ $penulisProfile?->jurusan_pendidikan ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt>Status Akun</dt>
                            <dd><span class="penulis-profile-status">Aktif</span></dd>
                        </div>
                    </dl>
                    </article>

                    <article class="penulis-profile-card penulis-profile-detail-card">
                    <div class="penulis-profile-card-head">
                        <div>
                            <p class="penulis-profile-card-label">Informasi Biodata</p>
                            <h2>Detail Penulis</h2>
                        </div>
                    </div>

                    <dl class="penulis-profile-detail-list">
                        @foreach ($profileDetails as $label => $value)
                            <div>
                                <dt>{{ $label }}</dt>
                                <dd>{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    </article>

                    <aside class="penulis-profile-card penulis-profile-insight">
                    <div class="penulis-profile-card-head">
                        <div>
                            <p class="penulis-profile-card-label">Informasi Kerja</p>
                            <h2>Workflow Penulisan</h2>
                        </div>
                    </div>

                    <div class="penulis-profile-workflow" aria-label="Tahapan workflow penulisan">
                        <span>Upload</span>
                        <span>Review</span>
                        <span>Editing</span>
                        <span>Layout</span>
                        <span>Printing</span>
                    </div>

                    <div class="penulis-profile-note-list">
                        <div>
                            <strong>Informasi Naskah</strong>
                            <p>Unggah dan pantau naskah dari menu penulis yang sudah tersedia.</p>
                        </div>
                        <div>
                            <strong>Status Aktivitas</strong>
                            <p>Status proses tetap mengikuti workflow sistem yang berjalan.</p>
                        </div>
                        <div>
                            <strong>Notifikasi</strong>
                            <p>Pemberitahuan penting tetap dikirim melalui pusat notifikasi aplikasi.</p>
                        </div>
                    </div>
                    </aside>
                </div>

                <div class="penulis-profile-settings-grid">
                    <article class="penulis-profile-form-card">
                        @include('profile.partials.update-profile-information-form')
                    </article>

                    <article class="penulis-profile-form-card">
                        @include('profile.partials.update-password-form')
                    </article>

                    <article class="penulis-profile-form-card penulis-profile-danger-card">
                        @include('profile.partials.delete-user-form')
                    </article>
                </div>
            </div>
        </section>
    @else
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
