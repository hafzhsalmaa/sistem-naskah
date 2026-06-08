<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $appName = config('app.name', 'PubliSync');
        $sectionTitle = trim($__env->yieldContent('title'));
        $slotTitle = isset($title) ? trim((string) $title) : '';
        $pageTitle = $sectionTitle !== '' ? $sectionTitle : $slotTitle;
    @endphp

    <title>{{ $pageTitle !== '' && $pageTitle !== $appName ? $pageTitle.' - '.$appName : $appName }}</title>
    @include('layouts.partials.favicon')

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @php
        $roleAssets = match (auth()->user()?->role) {
            'admin' => ['resources/css/admin.css', 'resources/js/admin.js'],
            'penulis' => ['resources/css/penulis.css', 'resources/js/penulis.js'],
            'editor' => ['resources/css/editor.css', 'resources/js/editor.js'],
            'layouter' => ['resources/css/layouter.css', 'resources/js/layouter.js'],
            default => [],
        };
    @endphp

    @vite(array_merge(['resources/css/app.css', 'resources/js/app.js'], $roleAssets))
</head>
<body class="app-body">
    @php
        $hasSidebarLayout = auth()->check() && in_array(auth()->user()->role, ['admin', 'penulis', 'editor', 'layouter'], true);
    @endphp

    <div class="{{ $hasSidebarLayout ? 'app-shell app-shell--with-sidebar' : 'app-shell app-shell--default' }}">
        @include('layouts.navigation')

        <div class="{{ $hasSidebarLayout ? 'app-content app-content--with-sidebar' : 'app-content' }}">
            @hasSection('header')
                <header class="{{ $hasSidebarLayout ? 'app-page-header app-page-header--sidebar' : 'app-page-header app-page-header--default' }}">
                    <div class="{{ $hasSidebarLayout ? 'app-page-header__inner app-page-header__inner--sidebar' : 'app-page-header__inner app-page-header__inner--default' }}">
                        @yield('header')
                    </div>
                </header>
            @elseif (isset($header))
                <header class="{{ $hasSidebarLayout ? 'app-page-header app-page-header--sidebar' : 'app-page-header app-page-header--default' }}">
                    <div class="{{ $hasSidebarLayout ? 'app-page-header__inner app-page-header__inner--sidebar' : 'app-page-header__inner app-page-header__inner--default' }}">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                @hasSection('content')
                    @yield('content')
                @elseif (isset($slot))
                    {{ $slot }}
                @endif
            </main>
        </div>
    </div>

    <x-confirm-modal />
</body>
</html>
