<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PubliSync - Sistem Manajemen Naskah Pendidikan</title>
        @include('layouts.partials.favicon')

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="landing-body">
        <main class="landing-page">
            <div class="landing-grid-background"></div>
            <div class="landing-glow landing-glow-primary"></div>
            <div class="landing-glow landing-glow-secondary"></div>

            <nav class="landing-navbar" aria-label="Navigasi utama">
                <a href="{{ url('/') }}" class="landing-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo PubliSync" class="landing-brand-logo">
                    <span>PubliSync</span>
                </a>

                @if (Route::has('login'))
                    <div class="landing-nav-actions">
                        <a href="{{ route('login') }}" class="landing-nav-button">Login</a>
                        {{-- @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="landing-nav-button">Register</a>
                        @endif --}}
                    </div>
                @endif
            </nav>

            <section class="landing-hero">
                <div class="landing-hero-content">
                    <p class="landing-eyebrow">Sistem Penerbitan Buku Pendidikan</p>
                    <h1>Smarter Collaboration for Educational Publishing</h1>
                    <p class="landing-hero-copy">
                        PubliSync membantu proses pengiriman, seleksi, editing, layout, hingga penjadwalan penerbitan naskah dalam satu sistem terpadu.
                    </p>

                    <div class="landing-hero-actions">
                        <a href="{{ route('login') }}" class="landing-cta-button">Masuk Sistem</a>
                        <a href="#fitur" class="landing-secondary-button">Lihat Fitur</a>
                    </div>

                    <div class="landing-hero-stats" aria-label="Ringkasan alur PubliSync">
                        <div>
                            <strong>5</strong>
                            <span>Tahap Workflow</span>
                        </div>
                        <div>
                            <strong>4</strong>
                            <span>Role Pengguna</span>
                        </div>
                        <div>
                            <strong>1</strong>
                            <span>Sistem Terpadu</span>
                        </div>
                    </div>
                </div>

                <div class="landing-hero-visual">
                    <div class="landing-hero-card">
                        <img src="{{ asset('images/ilustrasi1.png') }}" alt="Ilustrasi pengelolaan naskah pendidikan PubliSync" class="landing-hero-image">
                        <div class="landing-floating-card landing-floating-card-top">
                            <x-icons.file-text class="h-5 w-5" />
                            <span>Naskah Masuk</span>
                        </div>
                        <div class="landing-floating-card landing-floating-card-bottom">
                            <x-icons.check class="h-5 w-5" />
                            <span>Siap Terbit</span>
                        </div>
                    </div>
                </div>
            </section>

            <section id="fitur" class="landing-section">
                <div class="landing-section-heading">
                    <p class="landing-eyebrow">Fitur Utama</p>
                    <h2>Semua proses naskah dalam satu alur kerja</h2>
                    <p>Dirancang untuk membantu admin, penulis, editor, dan layouter memantau progres naskah dengan jelas.</p>
                </div>

                <div class="landing-feature-grid">
                    <article class="landing-feature-card">
                        <span class="landing-feature-icon"><x-icons.file-text class="h-5 w-5" /></span>
                        <h3>Pengiriman Naskah</h3>
                        <p>Penulis mengirim naskah dan melacak status perbaikan dari halaman kerja mereka.</p>
                    </article>
                    <article class="landing-feature-card">
                        <span class="landing-feature-icon"><x-icons.eye class="h-5 w-5" /></span>
                        <h3>Review Editor</h3>
                        <p>Editor melakukan seleksi, memberi catatan, dan memastikan kualitas naskah.</p>
                    </article>
                    <article class="landing-feature-card">
                        <span class="landing-feature-icon"><x-icons.layout-panel class="h-5 w-5" /></span>
                        <h3>Proses Layout</h3>
                        <p>Layouter mengelola file final agar naskah siap masuk tahap penerbitan.</p>
                    </article>
                    <article class="landing-feature-card">
                        <span class="landing-feature-icon"><x-icons.calendar class="h-5 w-5" /></span>
                        <h3>Jadwal Penerbitan</h3>
                        <p>Admin menentukan jadwal penerbitan untuk naskah yang sudah selesai layout.</p>
                    </article>
                    <article class="landing-feature-card">
                        <span class="landing-feature-icon"><x-icons.clock class="h-5 w-5" /></span>
                        <h3>Riwayat Monitoring</h3>
                        <p>Seluruh progres naskah dapat dilacak melalui riwayat dan monitoring terstruktur.</p>
                    </article>
                </div>
            </section>

            <section class="landing-section landing-workflow-section">
                <div class="landing-section-heading">
                    <p class="landing-eyebrow">Workflow</p>
                    <h2>Alur penerbitan yang mudah dipantau</h2>
                </div>

                <div class="landing-workflow">
                    <div class="landing-workflow-step">Upload</div>
                    <div class="landing-workflow-step">Review</div>
                    <div class="landing-workflow-step">Editing</div>
                    <div class="landing-workflow-step">Layout</div>
                    <div class="landing-workflow-step">Terbit</div>
                </div>
            </section>

            <section class="landing-section">
                <div class="landing-section-heading">
                    <p class="landing-eyebrow">Role Sistem</p>
                    <h2>Dibuat untuk kolaborasi tim penerbitan</h2>
                </div>

                <div class="landing-role-grid">
                    <article class="landing-role-card">
                        <x-icons.briefcase class="h-5 w-5" />
                        <h3>Admin</h3>
                        <p>Mengelola data, assignment, jadwal, dan monitoring sistem.</p>
                    </article>
                    <article class="landing-role-card">
                        <x-icons.user-circle class="h-5 w-5" />
                        <h3>Penulis</h3>
                        <p>Mengirim naskah dan menindaklanjuti revisi dari editor.</p>
                    </article>
                    <article class="landing-role-card">
                        <x-icons.eye class="h-5 w-5" />
                        <h3>Editor</h3>
                        <p>Meninjau kualitas naskah dan memberi arahan perbaikan.</p>
                    </article>
                    <article class="landing-role-card">
                        <x-icons.layout-panel class="h-5 w-5" />
                        <h3>Layouter</h3>
                        <p>Menyiapkan tampilan akhir naskah sebelum dijadwalkan terbit.</p>
                    </article>
                </div>
            </section>

            <footer class="landing-footer">
                <div class="landing-footer-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo PubliSync">
                    <span>PubliSync</span>
                </div>
                <p>&copy; {{ date('Y') }} PubliSync. Sistem manajemen naskah pendidikan.</p>
            </footer>
        </main>
    </body>
</html>
