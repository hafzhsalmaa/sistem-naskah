@extends('layouts.app')

@section('title', 'Notifikasi')

@php
    $renderedUnreadCount = collect($notifications->items())->filter(fn ($notification) => $notification->read_at === null)->count();
@endphp

@section('header')
    <div class="notification-page-header">
        <div>
            <h1>Notifikasi</h1>
        </div>

        @if ($notifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf

                <button type="submit" class="notification-read-all">
                    Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>
@endsection

@section('content')
        <section class="notification-page" data-notification-page>
            <div class="notification-shell">
                @if (session('status'))
                    <div class="notification-alert" data-flash-auto-hide>
                        {{ session('status') }}
                    </div>
                @endif

                <div class="notification-card">
                    <div class="notification-toolbar">
                        <label class="notification-search">
                            <span class="sr-only">Cari notifikasi</span>
                            <input type="search" placeholder="Cari notifikasi" data-notification-search>
                        </label>

                        <div class="notification-filter" role="group" aria-label="Filter status notifikasi">
                            <button type="button" class="is-active" data-notification-filter="all">Semua</button>
                            <button type="button" data-notification-filter="unread">Belum Dibaca</button>
                            <button type="button" data-notification-filter="read">Sudah Dibaca</button>
                        </div>

                        <div class="notification-counts">
                            <span>{{ $notifications->total() }} Notifikasi</span>
                            <span>{{ $renderedUnreadCount }} Belum Dibaca di halaman ini</span>
                        </div>
                    </div>

                    <div class="notification-list">
                        @forelse ($notifications as $notification)
                            @php
                                $notificationTitle = $notification->data['title'] ?? 'Notifikasi';
                                $notificationMessage = $notification->data['message'] ?? '';
                                $notificationSearchText = collect([
                                    $notificationTitle,
                                    $notificationMessage,
                                    $notification->created_at->diffForHumans(),
                                    $notification->read_at ? 'Sudah Dibaca' : 'Belum Dibaca',
                                ])->filter()->implode(' ');
                            @endphp

                            <article
                                class="notification-item {{ $notification->read_at ? 'is-read' : 'is-unread' }}"
                                data-notification-item
                                data-search="{{ $notificationSearchText }}"
                                data-read="{{ $notification->read_at ? '1' : '0' }}"
                                data-status="{{ $notification->read_at ? 'read' : 'unread' }}"
                            >
                                <div class="notification-copy">
                                    <div class="notification-title-row">
                                        <h2>{{ $notificationTitle }}</h2>
                                        <span class="notification-state {{ $notification->read_at ? 'is-read' : 'is-unread' }}">
                                            {{ $notification->read_at ? 'Sudah Dibaca' : 'Belum Dibaca' }}
                                        </span>
                                    </div>

                                    @if (! empty($notificationMessage))
                                        <p class="notification-message">{{ $notificationMessage }}</p>
                                    @endif

                                    <p class="notification-time">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>

                                <div class="notification-actions">
                                    <a href="{{ route('notifications.redirect', $notification->id) }}" class="notification-open-button">
                                        Buka
                                    </a>

                                    @if (! $notification->read_at)
                                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="notification-mark-button">
                                                Tandai Dibaca
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="notification-empty">
                                Belum ada notifikasi.
                            </div>
                        @endforelse

                        <div class="notification-empty is-hidden" data-notification-empty>
                            Tidak ada notifikasi yang cocok dengan pencarian atau filter.
                        </div>
                    </div>
                </div>

                @if ($notifications->hasPages())
                    <div class="notification-pagination">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </section>
@endsection
