@php
    $layouterUser = Auth::user();
    $layouterDisplayName = $layouterUser?->layouter?->nama_lengkap ?: ($layouterUser?->username ?? 'Layouter');
    $sidebarInitials = \Illuminate\Support\Str::of($layouterDisplayName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
@endphp

<div
    x-data="{ layouterSidebarOpen: false, notificationOpen: false, accountOpen: false }"
    @keydown.escape.window="notificationOpen = false; accountOpen = false">
    <div x-show="layouterSidebarOpen" x-cloak class="layouter-sidebar-backdrop role-sidebar-backdrop"
        @click="layouterSidebarOpen = false"></div>

    <aside class="layouter-sidebar role-sidebar-panel"
        :class="{ 'translate-x-0': layouterSidebarOpen }">
        <div class="layouter-sidebar-header">
            <a href="{{ route('layouter.dashboard') }}" class="layouter-sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PubliSync" class="layouter-sidebar-logo">
                <span>PubliSync</span>
            </a>

            <button type="button" @click="layouterSidebarOpen = !layouterSidebarOpen" class="layouter-sidebar-toggle">
                <x-icons.menu class="h-5 w-5" />
            </button>
        </div>

        <div class="layouter-sidebar-profile">
            <div class="layouter-sidebar-avatar">
                {{ $sidebarInitials }}
            </div>
            <div class="layouter-sidebar-name">{{ $layouterDisplayName }}</div>
        </div>

        <div class="layouter-sidebar-divider"></div>

        <div class="layouter-sidebar-body">
            <div class="layouter-sidebar-scroll role-sidebar-scroll">
                <nav class="role-sidebar-nav">
                    <a href="{{ route('layouter.dashboard') }}"
                        class="layouter-sidebar-link {{ request()->routeIs('layouter.dashboard') ? 'is-active' : '' }}">
                        <span class="layouter-sidebar-icon">
                            <x-icons.home class="h-5 w-5" />
                        </span>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('layouter.naskah.masuk') }}"
                        class="layouter-sidebar-link {{ request()->routeIs('layouter.naskah.masuk') ? 'is-active' : '' }}">
                        <span class="layouter-sidebar-icon">
                            <x-icons.file-text class="h-5 w-5" />
                        </span>
                        <span>Daftar Naskah Masuk</span>
                    </a>

                    <a href="{{ route('layouter.naskah.layout-selesai') }}"
                        class="layouter-sidebar-link {{ request()->routeIs('layouter.naskah.layout-selesai') ? 'is-active' : '' }}">
                        <span class="layouter-sidebar-icon">
                            <x-icons.layout-panel class="h-5 w-5" />
                        </span>
                        <span>Manajemen Layout</span>
                    </a>

                    <a href="{{ route('layouter.riwayat-naskah.index') }}"
                        class="layouter-sidebar-link {{ request()->routeIs('layouter.riwayat-naskah.*') ? 'is-active' : '' }}">
                        <span class="layouter-sidebar-icon">
                            <x-icons.clock class="h-5 w-5" />
                        </span>
                        <span>Riwayat Naskah</span>
                    </a>

                    <a href="{{ route('notifications.index') }}"
                        class="layouter-sidebar-link {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}">
                        <span class="layouter-sidebar-icon">
                            <x-icons.bell class="h-5 w-5" />
                        </span>
                        <span>Notifikasi</span>
                    </a>
                </nav>
            </div>

            <div class="role-sidebar-footer">
                <div class="role-sidebar-nav">
                    <a href="{{ route('layouter.pengaturan.index') }}"
                        class="layouter-sidebar-link {{ request()->routeIs('layouter.pengaturan.*') ? 'is-active' : '' }}">
                        <span class="layouter-sidebar-icon">
                            <x-icons.settings class="h-5 w-5" />
                        </span>
                        <span>Pengaturan</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="js-confirm-logout" data-confirm-message="Apakah Anda yakin ingin keluar dari sistem?">
                        @csrf
                        <button type="submit" class="layouter-sidebar-link role-sidebar-full-button">
                            <span class="layouter-sidebar-icon">
                                <x-icons.logout class="h-5 w-5" />
                            </span>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <header class="layouter-topbar role-topbar">
        <div class="role-topbar-inner">
            <div class="role-topbar-left">
                <button type="button" @click="layouterSidebarOpen = !layouterSidebarOpen"
                    class="layouter-topbar-menu role-topbar-menu">
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
                    :display-name="$layouterDisplayName"
                    :initials="$sidebarInitials"
                    role-label="Layouter"
                    open-state="accountOpen"
                    close-state="notificationOpen"
                    :settings-route="route('layouter.pengaturan.index')"
                />
            </div>
        </div>
    </header>
</div>
