@php
    $penulisUser = Auth::user();
    $penulisDisplayName = $penulisUser?->penulis?->nama_lengkap ?: ($penulisUser?->username ?? 'Penulis');
    $sidebarInitials = \Illuminate\Support\Str::of($penulisDisplayName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
@endphp

<div
    x-data="{ penulisSidebarOpen: false, notificationOpen: false, accountOpen: false }"
    @keydown.escape.window="notificationOpen = false; accountOpen = false">
    <div x-show="penulisSidebarOpen" x-cloak class="penulis-sidebar-backdrop role-sidebar-backdrop"
        @click="penulisSidebarOpen = false"></div>

    <aside class="penulis-sidebar role-sidebar-panel"
        :class="{ 'translate-x-0': penulisSidebarOpen }">
        <div class="penulis-sidebar-header">
            <a href="{{ route('penulis.dashboard') }}" class="penulis-sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PubliSync" class="penulis-sidebar-logo">
                <span>PubliSync</span>
            </a>

            <button type="button" @click="penulisSidebarOpen = !penulisSidebarOpen" class="penulis-sidebar-toggle">
                <x-icons.menu class="h-5 w-5" />
            </button>
        </div>

        <div class="penulis-sidebar-profile">
            <div class="penulis-sidebar-avatar">
                {{ $sidebarInitials }}
            </div>
            <div class="penulis-sidebar-name">{{ $penulisDisplayName }}</div>
        </div>

        <div class="penulis-sidebar-divider"></div>

        <div class="penulis-sidebar-body">
            <div class="penulis-sidebar-scroll role-sidebar-scroll">
                <nav class="role-sidebar-nav">
                    <a href="{{ route('penulis.dashboard') }}"
                        class="penulis-sidebar-link {{ request()->routeIs('penulis.dashboard') ? 'is-active' : '' }}">
                        <span class="penulis-sidebar-icon">
                            <x-icons.home class="h-5 w-5" />
                        </span>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                        class="penulis-sidebar-link {{ request()->routeIs('profile.*') ? 'is-active' : '' }}">
                        <span class="penulis-sidebar-icon">
                            <x-icons.user-circle class="h-5 w-5" />
                        </span>
                        <span>Biodata Penulis</span>
                    </a>

                    <a href="{{ route('penulis.naskah.index') }}"
                        class="penulis-sidebar-link {{ request()->routeIs('penulis.naskah.index') || request()->routeIs('penulis.naskah.show') || request()->routeIs('penulis.naskah.revisi.store') || request()->routeIs('penulis.naskah.versi.download') ? 'is-active' : '' }}">
                        <span class="penulis-sidebar-icon">
                            <x-icons.file-text class="h-5 w-5" />
                        </span>
                        <span>Data Naskah Saya</span>
                    </a>

                    <a href="{{ route('penulis.naskah.create') }}"
                        class="penulis-sidebar-link {{ request()->routeIs('penulis.naskah.create') ? 'is-active' : '' }}">
                        <span class="penulis-sidebar-icon">
                            <x-icons.plus class="h-5 w-5" />
                        </span>
                        <span>Kirim Naskah</span>
                    </a>

                    {{-- <a href="#"
                        class="penulis-sidebar-link">
                        <span class="penulis-sidebar-icon">
                            <x-icons.calendar class="h-5 w-5" />
                        </span>
                        <span>Jadwal Penerbitan</span>
                    </a> --}}

                    <a href="{{ route('penulis.riwayat-naskah.index') }}"
                        class="penulis-sidebar-link {{ request()->routeIs('penulis.riwayat-naskah.*') ? 'is-active' : '' }}">
                        <span class="penulis-sidebar-icon">
                            <x-icons.clock class="h-5 w-5" />
                        </span>
                        <span>Riwayat Naskah</span>
                    </a>

                    <a href="{{ route('notifications.index') }}"
                        class="penulis-sidebar-link {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}">
                        <span class="penulis-sidebar-icon">
                            <x-icons.bell class="h-5 w-5" />
                        </span>
                        <span>Notifikasi</span>
                    </a>
                </nav>
            </div>

            <div class="role-sidebar-footer">
                <div class="role-sidebar-nav">
                    {{-- Menu Pengaturan disembunyikan sementara untuk demo.
                    <a href="#" class="penulis-sidebar-link">
                        <span class="penulis-sidebar-icon">
                            <x-icons.settings class="h-5 w-5" />
                        </span>
                        <span>Pengaturan</span>
                    </a>
                    --}}

                    <form method="POST" action="{{ route('logout') }}" class="js-confirm-logout" data-confirm-message="Apakah Anda yakin ingin keluar dari sistem?">
                        @csrf
                        <button type="submit" class="penulis-sidebar-link role-sidebar-full-button">
                            <span class="penulis-sidebar-icon">
                                <x-icons.logout class="h-5 w-5" />
                            </span>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <header class="penulis-topbar role-topbar">
        <div class="role-topbar-inner">
            <div class="role-topbar-left">
                <button type="button" @click="penulisSidebarOpen = !penulisSidebarOpen"
                    class="penulis-topbar-menu role-topbar-menu">
                    <x-icons.menu class="role-icon role-icon--lg" />
                </button>
            </div>

            <div class="role-topbar-actions">
                <div class="role-notification" @click.outside="notificationOpen = false; accountOpen = false">
                    <button
                        type="button"
                        @click.stop="notificationOpen = !notificationOpen; accountOpen = false"
                        class="role-notification-button"
                        aria-label="Buka notifikasi"
                    >
                        <x-icons.bell class="role-icon role-icon--notification" />

                        @if ($unreadNotificationsCount > 0)
                            <span class="role-notification-badge">
                                {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>

                    <div
                        x-show="notificationOpen"
                        x-cloak
                        @click.stop
                        class="role-notification-panel"
                    >
                        <div class="role-notification-head">
                            <div>
                                <p class="role-notification-title">Notifikasi</p>
                                <p class="role-notification-subtitle">{{ $unreadNotificationsCount }} belum dibaca</p>
                            </div>

                            @if ($unreadNotificationsCount > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf

                                    <button type="submit" class="role-notification-link">
                                        Tandai semua
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="role-notification-list">
                            @forelse ($recentNotifications as $notification)
                                <a
                                    href="{{ route('notifications.redirect', $notification->id) }}"
                                    class="role-notification-item {{ $notification->read_at ? '' : 'is-unread' }}"
                                >
                                    <div class="role-notification-row">
                                        <div class="role-notification-copy">
                                            <p class="role-notification-item-title">
                                                {{ $notification->data['title'] ?? 'Notifikasi' }}
                                            </p>
                                            @if (! empty($notification->data['message']))
                                                <p class="role-notification-message">
                                                    {{ \Illuminate\Support\Str::limit($notification->data['message'], 90) }}
                                                </p>
                                            @endif
                                            <p class="role-notification-time">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>

                                        @if (! $notification->read_at)
                                            <span class="role-notification-dot"></span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="role-notification-empty">
                                    Belum ada notifikasi.
                                </div>
                            @endforelse
                        </div>

                        <div class="role-notification-foot">
                            <a
                                href="{{ route('notifications.index') }}"
                                class="role-notification-all"
                            >
                                Lihat semua notifikasi
                            </a>
                        </div>
                    </div>
                </div>

                <x-role-account-dropdown
                    :display-name="$penulisDisplayName"
                    :initials="$sidebarInitials"
                    role-label="Penulis"
                    open-state="accountOpen"
                    close-state="notificationOpen"
                />
            </div>
        </div>
    </header>
</div>
