@php
    $editorUser = Auth::user();
    $editorDisplayName = $editorUser?->editor?->nama_lengkap ?: ($editorUser?->username ?? 'Editor');
    $sidebarInitials = \Illuminate\Support\Str::of($editorDisplayName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
@endphp

<div
    x-data="{ editorSidebarOpen: false, notificationOpen: false, accountOpen: false }"
    @keydown.escape.window="notificationOpen = false; accountOpen = false">
    <div x-show="editorSidebarOpen" x-cloak class="editor-sidebar-backdrop role-sidebar-backdrop"
        @click="editorSidebarOpen = false"></div>

    <aside class="editor-sidebar role-sidebar-panel"
        :class="{ 'translate-x-0': editorSidebarOpen }">
        <div class="editor-sidebar-header">
            <a href="{{ route('editor.dashboard') }}" class="editor-sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo PubliSync" class="editor-sidebar-logo">
                <span>PubliSync</span>
            </a>

            <button type="button" @click="editorSidebarOpen = !editorSidebarOpen" class="editor-sidebar-toggle">
                <x-icons.menu class="h-5 w-5" />
            </button>
        </div>

        <div class="editor-sidebar-profile">
            <div class="editor-sidebar-avatar">
                {{ $sidebarInitials }}
            </div>
            <div class="editor-sidebar-name">{{ $editorDisplayName }}</div>
        </div>

        <div class="editor-sidebar-divider"></div>

        <div class="editor-sidebar-body">
            <div class="editor-sidebar-scroll role-sidebar-scroll">
                <nav class="role-sidebar-nav">
                    <a href="{{ route('editor.dashboard') }}"
                        class="editor-sidebar-link {{ request()->routeIs('editor.dashboard') ? 'is-active' : '' }}">
                        <span class="editor-sidebar-icon">
                            <x-icons.home class="h-5 w-5" />
                        </span>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('editor.naskah.masuk') }}"
                        class="editor-sidebar-link {{ request()->routeIs('editor.naskah.masuk') ? 'is-active' : '' }}">
                        <span class="editor-sidebar-icon">
                            <x-icons.file-text class="h-5 w-5" />
                        </span>
                        <span>Daftar Naskah Masuk</span>
                    </a>

                    <a href="{{ route('editor.naskah.revisi') }}"
                        class="editor-sidebar-link {{ request()->routeIs('editor.naskah.revisi') ? 'is-active' : '' }}">
                        <span class="editor-sidebar-icon">
                            <x-icons.revision class="h-5 w-5" />
                        </span>
                        <span>Manajemen Revisi</span>
                    </a>

                    <a href="{{ route('editor.riwayat-naskah.index') }}"
                        class="editor-sidebar-link {{ request()->routeIs('editor.riwayat-naskah.*') ? 'is-active' : '' }}">
                        <span class="editor-sidebar-icon">
                            <x-icons.clock class="h-5 w-5" />
                        </span>
                        <span>Riwayat Naskah</span>
                    </a>

                    <a href="{{ route('notifications.index') }}"
                        class="editor-sidebar-link {{ request()->routeIs('notifications.*') ? 'is-active' : '' }}">
                        <span class="editor-sidebar-icon">
                            <x-icons.bell class="h-5 w-5" />
                        </span>
                        <span>Notifikasi</span>
                    </a>
                </nav>
            </div>

            <div class="role-sidebar-footer">
                <div class="role-sidebar-nav">
                    <a href="{{ route('editor.pengaturan.index') }}"
                        class="editor-sidebar-link {{ request()->routeIs('editor.pengaturan.*') ? 'is-active' : '' }}">
                        <span class="editor-sidebar-icon">
                            <x-icons.settings class="h-5 w-5" />
                        </span>
                        <span>Pengaturan</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="js-confirm-logout" data-confirm-message="Apakah Anda yakin ingin keluar dari sistem?">
                        @csrf
                        <button type="submit" class="editor-sidebar-link role-sidebar-full-button">
                            <span class="editor-sidebar-icon">
                                <x-icons.logout class="h-5 w-5" />
                            </span>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <header class="editor-topbar role-topbar">
        <div class="role-topbar-inner">
            <div class="role-topbar-left">
                <button type="button" @click="editorSidebarOpen = !editorSidebarOpen"
                    class="editor-topbar-menu role-topbar-menu">
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
                    :display-name="$editorDisplayName"
                    :initials="$sidebarInitials"
                    role-label="Editor"
                    open-state="accountOpen"
                    close-state="notificationOpen"
                    :settings-route="route('editor.pengaturan.index')"
                />
            </div>
        </div>
    </header>
</div>
