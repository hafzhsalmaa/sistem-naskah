@props([
    'id',
    'name',
    'label',
    'autocomplete' => null,
    'messages' => [],
    'inputClass' => '',
])

<div class="password-toggle-field">
    <x-input-label :for="$id" :value="$label" />

    <div class="password-toggle-field__control">
        <x-text-input
            :id="$id"
            :name="$name"
            type="password"
            class="password-toggle-field__input {{ $inputClass }}"
            :autocomplete="$autocomplete"
            data-password-toggle-input
        />

        <button
            type="button"
            class="password-toggle-field__button"
            data-password-toggle
            aria-label="Tampilkan password"
            aria-pressed="false"
        >
            <span data-password-toggle-hidden-icon>
                <x-icons.eye-off class="h-5 w-5" />
            </span>
            <span data-password-toggle-visible-icon hidden>
                <x-icons.eye class="h-5 w-5" />
            </span>
        </button>
    </div>

    <x-input-error class="mt-2" :messages="$messages" />
</div>
