@props([
    'displayName',
    'initials',
    'roleLabel',
    'openState',
    'closeState' => null,
    'settingsRoute' => null,
])

@php
    $closeExpression = $closeState ? "; {$closeState} = false" : '';
@endphp

<div class="role-account-dropdown" @click.outside="{{ $openState }} = false{{ $closeExpression }}">
    <button
        type="button"
        class="role-topbar-user role-account-trigger"
        @click.stop="{{ $openState }} = !{{ $openState }}{{ $closeExpression }}"
        :aria-expanded="{{ $openState }}.toString()"
        aria-haspopup="menu"
    >
        <span class="role-topbar-avatar">
            {{ $initials }}
        </span>
        <span class="role-topbar-user-text account-user-info">
            <span class="role-topbar-user-name account-user-name">{{ $displayName }}</span>
            <span class="role-topbar-user-role account-user-role">{{ $roleLabel }}</span>
        </span>
    </button>

    <div
        x-show="{{ $openState }}"
        x-cloak
        @click.stop
        class="role-account-panel"
        role="menu"
    >
        <div class="role-account-head">
            <p class="role-account-name">{{ $displayName }}</p>
            <p class="role-account-role">{{ $roleLabel }}</p>
        </div>

        <div class="role-account-list">
            @if ($settingsRoute)
                <a href="{{ $settingsRoute }}" class="role-account-item" role="menuitem">
                    <x-icons.settings class="role-icon role-icon--sm" />
                    <span>Pengaturan</span>
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="js-confirm-logout" data-confirm-message="Apakah Anda yakin ingin keluar dari sistem?">
                @csrf
                <button type="submit" class="role-account-item role-account-item--button" role="menuitem">
                    <x-icons.logout class="role-icon role-icon--sm" />
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</div>
