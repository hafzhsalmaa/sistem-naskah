<x-guest-layout>
    <div x-data="{ showPassword: false }" class="login-card">
        <div class="login-header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo PubliSync" class="login-logo">
            <h1 class="login-title">Login</h1>
            <p class="login-subtitle">Masuk ke dalam Akun Kolaborasi Anda</p>
            <p class="login-helper">Pastikan Anda Sudah Mendapatkan Akun dari Admin</p>
        </div>

        <x-auth-session-status class="login-status" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf

            <div class="login-field">
                <label for="email" class="login-label">Email</label>
                <div class="login-input-wrapper">
                    <span class="login-input-icon">
                        <x-icons.envelope class="h-5 w-5" />
                    </span>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Masukkan email Anda"
                        class="login-input"
                    >
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="login-field">
                <div class="login-password-head">
                    <label for="password" class="login-label">Password</label>
                    @if (Route::has('password.request'))
                        <a class="login-forgot-link" href="{{ route('password.request') }}">
                            Lupa password?
                        </a>
                    @endif
                </div>

                <div class="login-input-wrapper">
                    <span class="login-input-icon">
                        <x-icons.lock class="h-5 w-5" />
                    </span>

                    <input
                        id="password"
                        x-bind:type="showPassword ? 'text' : 'password'"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan password Anda"
                        class="login-input login-input--password"
                    >

                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="login-password-toggle"
                        :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                        :aria-pressed="showPassword.toString()"
                    >
                        <x-icons.eye-off x-show="!showPassword" x-cloak />
                        <x-icons.eye x-show="showPassword" x-cloak />
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <button type="submit" class="login-submit">
                Login
            </button>
        </form>

        <div class="login-footer">
            <p class="login-footer-title">Create. Connect. Collaborate</p>
            <p class="login-footer-copy">
                Platform kolaborasi naskah untuk penulis, editor, layouter, dan admin dalam satu alur kerja yang terstruktur.
            </p>
        </div>
    </div>
</x-guest-layout>
