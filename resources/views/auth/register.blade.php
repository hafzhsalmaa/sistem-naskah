<x-guest-layout>
    <form
        method="POST"
        action="{{ route('register') }}"
        x-data="{
            role: @js(old('role', '')),
            profesi: @js(old('profesi', '')),
            showPassword: false,
            showPasswordConfirmation: false,
        }"
        class="space-y-4"
    >
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" x-model="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="">Pilih role</option>
                <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                <option value="penulis" @selected(old('role') === 'penulis')>Penulis</option>
                <option value="editor" @selected(old('role') === 'editor')>Editor</option>
                <option value="layouter" @selected(old('role') === 'layouter')>Layouter</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <fieldset x-show="role === 'penulis'" x-cloak x-bind:disabled="role !== 'penulis'" class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <div>
                <x-input-label for="alamat" value="Alamat" />
                <x-text-input id="alamat" class="mt-1 block w-full" type="text" name="alamat" :value="old('alamat')" />
                <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="profesi" value="Profesi" />
                <select id="profesi" name="profesi" x-model="profesi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih profesi</option>
                    <option value="Guru" @selected(old('profesi') === 'Guru')>Guru</option>
                    <option value="Dosen" @selected(old('profesi') === 'Dosen')>Dosen</option>
                    <option value="Karyawan" @selected(old('profesi') === 'Karyawan')>Karyawan</option>
                    <option value="Professor" @selected(old('profesi') === 'Professor')>Professor</option>
                    <option value="Lainnya" @selected(old('profesi') === 'Lainnya')>Lainnya</option>
                </select>
                <x-input-error :messages="$errors->get('profesi')" class="mt-2" />
            </div>

            <div x-show="profesi === 'Lainnya'" x-cloak>
                <x-input-label for="profesi_lainnya" value="Profesi Manual" />
                <x-text-input id="profesi_lainnya" class="mt-1 block w-full" type="text" name="profesi_lainnya" :value="old('profesi_lainnya')" />
                <x-input-error :messages="$errors->get('profesi_lainnya')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="jurusan_pendidikan" value="Jurusan Pendidikan" />
                <x-text-input id="jurusan_pendidikan" class="mt-1 block w-full" type="text" name="jurusan_pendidikan" :value="old('jurusan_pendidikan')" placeholder="Contoh: Ilmu Komputer, Teknik Informatika" />
                <x-input-error :messages="$errors->get('jurusan_pendidikan')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="no_hp" value="No HP" />
                <x-text-input id="no_hp" class="mt-1 block w-full" type="text" name="no_hp" :value="old('no_hp')" />
                <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
            </div>

        </fieldset>

        <fieldset x-show="role === 'editor' || role === 'layouter'" x-cloak x-bind:disabled="role !== 'editor' && role !== 'layouter'" class="space-y-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <div>
                <x-input-label for="no_hp_role" value="No HP" />
                <x-text-input id="no_hp_role" class="mt-1 block w-full" type="text" name="no_hp" :value="old('no_hp')" />
                <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="bidang_keahlian_role" value="Bidang Keahlian" />
                <select id="bidang_keahlian_role" name="bidang_keahlian" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih bidang keahlian</option>
                    <option value="SD/MI" @selected(old('bidang_keahlian') === 'SD/MI')>SD/MI</option>
                    <option value="SMP/MTS" @selected(old('bidang_keahlian') === 'SMP/MTS')>SMP/MTS</option>
                    <option value="SMA/MA/SMK" @selected(old('bidang_keahlian') === 'SMA/MA/SMK')>SMA/MA/SMK</option>
                </select>
                <x-input-error :messages="$errors->get('bidang_keahlian')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="kategori_mapel" value="Kategori Mapel" />
                <select id="kategori_mapel" name="kategori_mapel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih kategori mapel</option>
                    <option value="Umum" @selected(old('kategori_mapel') === 'Umum')>Umum</option>
                    <option value="Bahasa" @selected(old('kategori_mapel') === 'Bahasa')>Bahasa</option>
                    <option value="Agama" @selected(old('kategori_mapel') === 'Agama')>Agama</option>
                </select>
                <x-input-error :messages="$errors->get('kategori_mapel')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="mata_pelajaran" value="Mata Pelajaran" />
                <select id="mata_pelajaran" name="mata_pelajaran" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih bidang mapel</option>
                    <option value="IPA" @selected(old('mata_pelajaran') === 'IPA')>IPA</option>
                    <option value="IPS" @selected(old('mata_pelajaran') === 'IPS')>IPS</option>
                    <option value="Matematika" @selected(old('mata_pelajaran') === 'Matematika')>Matematika</option>
                    <option value="Bahasa Indonesia" @selected(old('mata_pelajaran') === 'Bahasa Indonesia')>Bahasa Indonesia</option>
                    <option value="Bahasa Inggris" @selected(old('mata_pelajaran') === 'Bahasa Inggris')>Bahasa Inggris</option>
                    <option value="Sejarah" @selected(old('mata_pelajaran') === 'Sejarah')>Sejarah</option>
                    <option value="Agama" @selected(old('mata_pelajaran') === 'Agama')>Agama</option>
                    <option value="Bahasa Jawa" @selected(old('mata_pelajaran') === 'Bahasa Jawa')>Bahasa Jawa</option>
                </select>
                <x-input-error :messages="$errors->get('mata_pelajaran')" class="mt-2" />
            </div>
        </fieldset>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pe-12"
                                x-bind:type="showPassword ? 'text' : 'password'"
                                name="password"
                                required autocomplete="new-password" />
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                    aria-label="Toggle password visibility"
                >
                    <x-icons.eye x-show="!showPassword" x-cloak />
                    <x-icons.eye-off x-show="showPassword" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative mt-1">
                <x-text-input id="password_confirmation" class="block w-full pe-12"
                                x-bind:type="showPasswordConfirmation ? 'text' : 'password'"
                                name="password_confirmation" required autocomplete="new-password" />
                <button
                    type="button"
                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                    aria-label="Toggle password confirmation visibility"
                >
                    <x-icons.eye x-show="!showPasswordConfirmation" x-cloak />
                    <x-icons.eye-off x-show="showPasswordConfirmation" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
