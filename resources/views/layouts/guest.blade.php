<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $appName = config('app.name', 'PubliSync');
            $pageTitle = match (true) {
                request()->routeIs('login') => 'Login',
                request()->routeIs('register') => 'Registrasi',
                request()->routeIs('password.request') => 'Lupa Password',
                request()->routeIs('password.reset') => 'Reset Password',
                request()->routeIs('password.confirm') => 'Konfirmasi Password',
                request()->routeIs('verification.notice') => 'Verifikasi Email',
                default => '',
            };
        @endphp

        <title>{{ $pageTitle !== '' ? $pageTitle.' - '.$appName : $appName }}</title>
        @include('layouts.partials.favicon')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @if (request()->routeIs('login'))
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="guest-body">
        @php
            $isLoginPage = request()->routeIs('login');
        @endphp

        <div
            class="{{ $isLoginPage ? 'guest-shell guest-shell--login' : 'guest-shell guest-shell--default' }}"
            @if ($isLoginPage)
                style="background-image: url('{{ asset('images/auth/bg-login.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"
            @endif
        >
            @if (! $isLoginPage)
                <div>
                    <a href="/">
                        <x-application-logo class="guest-logo" />
                    </a>
                </div>
            @endif

            <div class="{{ $isLoginPage ? 'guest-card guest-card--login' : (request()->routeIs('register') ? 'guest-card guest-card--register' : 'guest-card guest-card--default') }}">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
