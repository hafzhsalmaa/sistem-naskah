<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Ubah Password
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Pastikan akun Anda memakai password yang kuat dan tidak mudah ditebak.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <x-password-toggle-field
            id="update_password_current_password"
            name="current_password"
            label="Password Lama"
            autocomplete="current-password"
            input-class="mt-1 block w-full"
            :messages="$errors->updatePassword->get('current_password')"
        />

        <x-password-toggle-field
            id="update_password_password"
            name="password"
            label="Password Baru"
            autocomplete="new-password"
            input-class="mt-1 block w-full"
            :messages="$errors->updatePassword->get('password')"
        />

        <x-password-toggle-field
            id="update_password_password_confirmation"
            name="password_confirmation"
            label="Konfirmasi Password Baru"
            autocomplete="new-password"
            input-class="mt-1 block w-full"
            :messages="$errors->updatePassword->get('password_confirmation')"
        />

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan Password</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>
