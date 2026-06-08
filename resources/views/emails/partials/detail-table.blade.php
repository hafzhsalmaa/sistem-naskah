@php
    $details = $details ?? [];
@endphp

@if (! empty($details))
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin: 0 0 22px; border-collapse: separate; border-spacing: 0; overflow: hidden; border: 1px solid #dbe5f0; border-radius: 12px; background: #fbfdff;">
        @foreach ($details as $label => $value)
            <tr>
                <td style="width: 38%; border-bottom: {{ $loop->last ? '0' : '1px solid #e4edf6' }}; padding: 12px 16px; color: #52667e; font-size: 13px; font-weight: 700;">
                    {{ $label }}
                </td>
                <td style="border-bottom: {{ $loop->last ? '0' : '1px solid #e4edf6' }}; padding: 12px 16px; color: #102a43; font-size: 14px; font-weight: 600;">
                    {{ filled($value) ? $value : '-' }}
                </td>
            </tr>
        @endforeach
    </table>
@endif
