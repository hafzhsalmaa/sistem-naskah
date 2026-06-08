@php
    $url = $url ?? null;
    $label = $label ?? null;
@endphp

@if ($url && $label)
    <p style="margin: 0 0 24px;">
        <a href="{{ $url }}" style="display: inline-block; border-radius: 10px; background: #094067; color: #ffffff; font-size: 14px; font-weight: 700; padding: 11px 18px; text-decoration: none;">
            {{ $label }}
        </a>
    </p>
@endif
