@php
    $adminUser = Auth::user();
    $sidebarInitials = \Illuminate\Support\Str::of($adminUser?->username ?? 'A')
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
@endphp

<div
    x-data="{ adminSidebarOpen: false, adminManageOpen: {{ request()->routeIs('admin.data-*') || request()->routeIs('admin.naskah.*') ? 'true' : 'false' }}, adminNotificationOpen: false, adminAccountOpen: false }">
    <div x-show="adminSidebarOpen" x-cloak class="admin-sidebar-backdrop role-sidebar-backdrop"
        @click="adminSidebarOpen = false"></div>

    <aside class="admin-sidebar role-sidebar-panel"
        :class="{ 'translate-x-0': adminSidebarOpen }">
        <div class="admin-sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PubliSync" class="admin-sidebar-logo">
                <span>PubliSync</span>
            </a>

            <button type="button" @click="adminSidebarOpen = !adminSidebarOpen" class="admin-sidebar-toggle">
                <x-icons.menu class="h-5 w-5" />
            </button>
        </div>

        <div class="admin-sidebar-profile">
            <div class="admin-sidebar-avatar">
                {{ $sidebarInitials }}
            </div>
            <div class="admin-sidebar-name">{{ $adminUser->username }}</div>
        </div>

        <div class="admin-sidebar-divider"></div>

        <div class="admin-sidebar-body">
            <div class="admin-sidebar-scroll role-sidebar-scroll">
                <nav class="role-sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}"
                        class="admin-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <span class="admin-sidebar-icon">
                            <x-icons.home class="h-5 w-5" />
                        </span>
                        <span>Dashboard</span>
                    </a>

                    <div class="role-sidebar-nav">
                        <button type="button" @click="adminManageOpen = !adminManageOpen"
                            class="admin-sidebar-link role-sidebar-full-button">
                            <span class="admin-sidebar-icon">
                                <x-icons.list class="h-5 w-5" />
                            </span>
                            <span class="role-sidebar-link-fill">Manajemen Data</span>
                            <x-icons.chevron-down class="role-icon role-icon--sm transition" ::class="{ 'rotate-180': adminManageOpen }" />
                        </button>

                        <div x-show="adminManageOpen" x-cloak class="role-sidebar-subnav">
                            <a href="{{ route('admin.naskah.index') }}"
                                class="admin-sidebar-link admin-sidebar-sublink {{ request()->routeIs('admin.naskah.*') ? 'is-active' : '' }}">
                                <span class="admin-sidebar-icon">
                                    <x-icons.file-text class="h-4 w-4" />
                                </span>
                                <span>Manajemen Naskah</span>
                            </a>
                            <a href="{{ route('admin.data-penulis.index') }}"
                                class="admin-sidebar-link admin-sidebar-sublink {{ request()->routeIs('admin.data-penulis.*') ? 'is-active' : '' }}">
                                <span class="admin-sidebar-icon">
                                    <x-icons.user-circle class="h-4 w-4" />
                                </span>
                                <span>Manajemen Penulis</span>
                            </a>
                            <a href="{{ route('admin.data-editor.index') }}"
                                class="admin-sidebar-link admin-sidebar-sublink {{ request()->routeIs('admin.data-editor.*') ? 'is-active' : '' }}">
                                <span class="admin-sidebar-icon">
                                    <x-icons.briefcase class="h-4 w-4" />
                                </span>
                                <span>Manajemen Editor</span>
                            </a>
                            <a href="{{ route('admin.data-layouter.index') }}"
                                class="admin-sidebar-link admin-sidebar-sublink {{ request()->routeIs('admin.data-layouter.*') ? 'is-active' : '' }}">
                                <span class="admin-sidebar-icon">
                                    <x-icons.layout-panel class="h-4 w-4" />
                                </span>
                                <span>Manajemen Layouter</span>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.jadwal-penerbitan.index') }}"
                        class="admin-sidebar-link {{ request()->routeIs('admin.jadwal-penerbitan.*') ? 'is-active' : '' }}">
                        <span class="admin-sidebar-icon">
                            <x-icons.calendar class="h-5 w-5" />
                        </span>
                        <span>Jadwal Penerbitan</span>
                    </a>

                    <a href="{{ route('admin.riwayat-naskah.index') }}"
                        class="admin-sidebar-link {{ request()->routeIs('admin.riwayat-naskah.*') ? 'is-active' : '' }}">
                        <span class="admin-sidebar-icon">
                            <x-icons.clock class="h-5 w-5" />
                        </span>
                        <span>Riwayat Monitoring</span>
                    </a>
                </nav>
            </div>

            <div class="role-sidebar-footer">
                <div class="role-sidebar-nav">
                    {{-- Menu Pengaturan disembunyikan sementara untuk demo.
                    <a href="#" class="admin-sidebar-link">
                        <span class="admin-sidebar-icon">
                            <x-icons.settings class="h-5 w-5" />
                        </span>
                        <span>Pengaturan</span>
                    </a>
                    --}}

                    <form method="POST" action="{{ route('logout') }}" class="js-confirm-logout" data-confirm-message="Apakah Anda yakin ingin keluar dari sistem?">
                        @csrf
                        <button type="submit" class="admin-sidebar-link role-sidebar-full-button">
                            <span class="admin-sidebar-icon">
                                <x-icons.logout class="h-5 w-5" />
                            </span>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <header class="admin-topbar role-topbar">
        <div class="role-topbar-inner">
            <div class="role-topbar-left">
                <button type="button" @click="adminSidebarOpen = !adminSidebarOpen"
                    class="admin-topbar-menu role-topbar-menu">
                    <x-icons.menu class="role-icon role-icon--lg" />
                </button>
            </div>

            <div class="role-topbar-actions">
                <div class="role-notification" @click.outside="adminNotificationOpen = false">
                    <button
                        type="button"
                        @click.stop="adminNotificationOpen = !adminNotificationOpen"
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
                        x-show="adminNotificationOpen"
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
                    :display-name="$adminUser->username"
                    :initials="$sidebarInitials"
                    role-label="Admin"
                    open-state="adminAccountOpen"
                />
            </div>
        </div>
    </header>
</div>
