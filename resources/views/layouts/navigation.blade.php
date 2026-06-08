@php
    $notificationUser = auth()->user();
    $recentNotifications = $notificationUser?->notifications()->latest()->limit(5)->get() ?? collect();
    $unreadNotificationsCount = $notificationUser?->unreadNotifications()->count() ?? 0;
    $navigationUser = Auth::user();
    $displayName = match ($navigationUser?->role) {
        'penulis' => $navigationUser->penulis?->nama_lengkap ?: ($navigationUser->username ?? 'User'),
        'editor' => $navigationUser->editor?->nama_lengkap ?: ($navigationUser->username ?? 'User'),
        'layouter' => $navigationUser->layouter?->nama_lengkap ?: ($navigationUser->username ?? 'User'),
        default => $navigationUser?->username ?? 'User',
    };
    $avatarInitials = \Illuminate\Support\Str::of($displayName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
        ->implode('');
    $isAdminNavigation = auth()->check() && auth()->user()->role === 'admin';
@endphp

@if ($isAdminNavigation)
    @include('layouts.sidebars.admin')
@elseif (auth()->check() && auth()->user()->role === 'penulis')
    @include('layouts.sidebars.penulis')
@elseif (auth()->check() && auth()->user()->role === 'editor')
    @include('layouts.sidebars.editor')
@elseif (auth()->check() && auth()->user()->role === 'layouter')
    @include('layouts.sidebars.layouter')
@else
<nav
    x-data="{
        open: false,
        adminManageOpen: {{ request()->routeIs('admin.data-*') || request()->routeIs('admin.naskah.*') ? 'true' : 'false' }},
        notificationOpen: false,
        toggleAdminManage() {
            this.adminManageOpen = !this.adminManageOpen;
        },
        closeAdminManage() {
            this.adminManageOpen = false;
        },
        toggleNotification() {
            this.notificationOpen = !this.notificationOpen;
        },
        closeNotification() {
            this.notificationOpen = false;
        }
    }"
    class="app-nav"
>
    <div class="app-nav__container">
        <div class="app-nav__bar">
            <div class="app-nav__brand-area">
                <div class="app-nav__logo">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="app-nav__logo-image" />
                    </a>
                </div>

                <div class="app-nav__desktop-links">
                    <a
                        href="{{ route('dashboard') }}"
                        class="app-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                    >
                        {{ __('Dashboard') }}
                    </a>

                    @if (auth()->user()?->role === 'admin')
                        <div class="app-nav-menu" @click.outside="closeAdminManage()">
                            <button
                                type="button"
                                @click.stop="toggleAdminManage()"
                                class="app-nav-link app-nav-link--button {{ request()->routeIs('admin.data-*') || request()->routeIs('admin.naskah.*') ? 'is-active' : '' }}"
                                :aria-expanded="adminManageOpen.toString()"
                            >
                                <span>{{ __('Manajemen Data') }}</span>
                                <x-icons.chevron-down class="role-icon role-icon--sm transition" ::class="{ 'rotate-180': adminManageOpen }" />
                            </button>

                            <div
                                x-show="adminManageOpen"
                                x-cloak
                                @click.stop
                                class="app-nav-menu__panel"
                            >
                                <a href="{{ route('admin.data-penulis.index') }}" @click="closeAdminManage()" class="app-nav-menu__item {{ request()->routeIs('admin.data-penulis.*') ? 'is-active' : '' }}">
                                    Data Penulis
                                </a>
                                <a href="{{ route('admin.data-editor.index') }}" @click="closeAdminManage()" class="app-nav-menu__item {{ request()->routeIs('admin.data-editor.*') ? 'is-active' : '' }}">
                                    Data Editor
                                </a>
                                <a href="{{ route('admin.data-layouter.index') }}" @click="closeAdminManage()" class="app-nav-menu__item {{ request()->routeIs('admin.data-layouter.*') ? 'is-active' : '' }}">
                                    Data Layouter
                                </a>
                                <a href="{{ route('admin.naskah.index') }}" @click="closeAdminManage()" class="app-nav-menu__item {{ request()->routeIs('admin.naskah.*') ? 'is-active' : '' }}">
                                    Data Naskah
                                </a>
                            </div>
                        </div>

                        <a
                            href="{{ route('admin.jadwal-penerbitan.index') }}"
                            class="app-nav-link {{ request()->routeIs('admin.jadwal-penerbitan.*') ? 'is-active' : '' }}"
                        >
                            {{ __('Jadwal Penerbitan') }}
                        </a>
                        <a
                            href="{{ route('admin.riwayat-naskah.index') }}"
                            class="app-nav-link {{ request()->routeIs('admin.riwayat-naskah.*') ? 'is-active' : '' }}"
                        >
                            {{ __('Riwayat Naskah') }}
                        </a>
                    @endif

                    @if (auth()->user()?->role === 'editor')
                        <a
                            href="{{ route('editor.naskah.index') }}"
                            class="app-nav-link {{ request()->routeIs('editor.naskah.*') ? 'is-active' : '' }}"
                        >
                            {{ __('Data Naskah') }}
                        </a>
                        <a
                            href="{{ route('editor.riwayat-naskah.index') }}"
                            class="app-nav-link {{ request()->routeIs('editor.riwayat-naskah.*') ? 'is-active' : '' }}"
                        >
                            {{ __('Riwayat Naskah') }}
                        </a>
                    @endif

                    @if (auth()->user()?->role === 'penulis')
                        <a
                            href="{{ route('penulis.naskah.index') }}"
                            class="app-nav-link {{ request()->routeIs('penulis.naskah.*') ? 'is-active' : '' }}"
                        >
                            {{ __('Data Naskah') }}
                        </a>
                        <a
                            href="{{ route('penulis.riwayat-naskah.index') }}"
                            class="app-nav-link {{ request()->routeIs('penulis.riwayat-naskah.*') ? 'is-active' : '' }}"
                        >
                            {{ __('Riwayat Naskah') }}
                        </a>
                    @endif

                    @if (auth()->user()?->role === 'layouter')
                        <a
                            href="{{ route('layouter.naskah.index') }}"
                            class="app-nav-link {{ request()->routeIs('layouter.naskah.*') ? 'is-active' : '' }}"
                        >
                            {{ __('Data Naskah') }}
                        </a>
                        <a
                            href="{{ route('layouter.riwayat-naskah.index') }}"
                            class="app-nav-link {{ request()->routeIs('layouter.riwayat-naskah.*') ? 'is-active' : '' }}"
                        >
                            {{ __('Riwayat Naskah') }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="app-nav__account-area">
                <div class="app-nav-notification" @click.outside="closeNotification()">
                    <button
                        type="button"
                        @click.stop="toggleNotification()"
                        class="app-nav-notification__button"
                        aria-label="Buka notifikasi"
                    >
                        <x-icons.bell class="role-icon" />

                        @if ($unreadNotificationsCount > 0)
                            <span class="app-nav-notification__badge">
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

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="app-nav-user-trigger">
                            <div class="app-nav-user-trigger__avatar">
                                {{ $avatarInitials }}
                            </div>
                            <div class="app-nav-user-trigger__copy">
                                <div class="app-nav-user-trigger__name">{{ $displayName }}</div>
                                <div class="app-nav-user-trigger__role">{{ ucfirst(Auth::user()->role) }}</div>
                            </div>
                            <x-icons.chevron-down class="role-icon role-icon--sm app-nav-user-trigger__chevron" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="app-nav-dropdown-head">
                            <p class="app-nav-dropdown-name">{{ $displayName }}</p>
                            <p class="app-nav-dropdown-email">{{ Auth::user()->email }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}" class="js-confirm-logout" data-confirm-message="Apakah Anda yakin ingin keluar dari sistem?">
                            @csrf

                            <button
                                type="submit"
                                class="app-nav-dropdown-button"
                            >
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="app-nav__mobile-toggle">
                <button @click="open = ! open" class="app-nav-mobile-button">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="app-nav-mobile-panel hidden">
        <div class="app-nav-mobile-links">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                {{ __('Notifikasi') }}
                @if ($unreadNotificationsCount > 0)
                    <span class="ms-2 inline-flex rounded-full bg-red-500 px-2 py-0.5 text-[10px] font-semibold text-white">
                        {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                    </span>
                @endif
            </x-responsive-nav-link>

            @if (auth()->user()?->role === 'admin')
                <div class="app-nav-mobile-group">
                    <button
                        type="button"
                        @click.stop="toggleAdminManage()"
                        class="app-nav-mobile-group-toggle"
                    >
                        <span class="app-nav-mobile-group-label">Manajemen Data</span>
                        <x-icons.chevron-down class="role-icon role-icon--sm app-nav-user-trigger__chevron transition" ::class="{ 'rotate-180': adminManageOpen }" />
                    </button>

                    <div x-show="adminManageOpen" x-cloak class="app-nav-mobile-subgroup">
                        <x-responsive-nav-link :href="route('admin.data-penulis.index')" :active="request()->routeIs('admin.data-penulis.*')" x-on:click="closeAdminManage()">
                            {{ __('Data Penulis') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.data-editor.index')" :active="request()->routeIs('admin.data-editor.*')" x-on:click="closeAdminManage()">
                            {{ __('Data Editor') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.data-layouter.index')" :active="request()->routeIs('admin.data-layouter.*')" x-on:click="closeAdminManage()">
                            {{ __('Data Layouter') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.naskah.index')" :active="request()->routeIs('admin.naskah.*')" x-on:click="closeAdminManage()">
                            {{ __('Data Naskah') }}
                        </x-responsive-nav-link>
                    </div>
                </div>
                <x-responsive-nav-link :href="route('admin.jadwal-penerbitan.index')" :active="request()->routeIs('admin.jadwal-penerbitan.*')">
                    {{ __('Jadwal Penerbitan') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.riwayat-naskah.index')" :active="request()->routeIs('admin.riwayat-naskah.*')">
                    {{ __('Riwayat Naskah') }}
                </x-responsive-nav-link>
            @endif

            @if (auth()->user()?->role === 'editor')
                <x-responsive-nav-link :href="route('editor.naskah.index')" :active="request()->routeIs('editor.naskah.*')">
                    {{ __('Data Naskah') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('editor.riwayat-naskah.index')" :active="request()->routeIs('editor.riwayat-naskah.*')">
                    {{ __('Riwayat Naskah') }}
                </x-responsive-nav-link>
            @endif

            @if (auth()->user()?->role === 'penulis')
                <x-responsive-nav-link :href="route('penulis.naskah.index')" :active="request()->routeIs('penulis.naskah.*')">
                    {{ __('Data Naskah') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('penulis.riwayat-naskah.index')" :active="request()->routeIs('penulis.riwayat-naskah.*')">
                    {{ __('Riwayat Naskah') }}
                </x-responsive-nav-link>
            @endif

            @if (auth()->user()?->role === 'layouter')
                <x-responsive-nav-link :href="route('layouter.naskah.index')" :active="request()->routeIs('layouter.naskah.*')">
                    {{ __('Data Naskah') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('layouter.riwayat-naskah.index')" :active="request()->routeIs('layouter.riwayat-naskah.*')">
                    {{ __('Riwayat Naskah') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="app-nav-mobile-account">
            <div class="app-nav-mobile-account__identity">
                <div class="app-nav-mobile-account__name">{{ $displayName }}</div>
                <div class="app-nav-mobile-account__email">{{ Auth::user()->email }}</div>
            </div>

            <div class="app-nav-mobile-account__links">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}" class="js-confirm-logout" data-confirm-message="Apakah Anda yakin ingin keluar dari sistem?">
                    @csrf

                    <button
                        type="submit"
                        class="app-nav-mobile-logout"
                    >
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
@endif
