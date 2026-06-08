@php
    $title = $title ?? 'PubliSync';
    $eyebrow = $eyebrow ?? 'PubliSync';
    $heading = $heading ?? $title;
    $footerText = $footerText ?? 'Email ini dikirim otomatis oleh sistem PubliSync. Abaikan jika informasi ini tidak relevan untuk Anda.';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body style="margin: 0; background: #f4f7fb; color: #172b4d; font-family: Arial, Helvetica, sans-serif; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background: #f4f7fb; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; overflow: hidden; border: 1px solid #dbe5f0; border-radius: 16px; background: #ffffff;">
                    <tr>
                        <td style="background: #094067; padding: 22px 28px;">
                            <p style="margin: 0; color: #d6e8f7; font-size: 12px; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">{{ $eyebrow }}</p>
                            <h1 style="margin: 8px 0 0; color: #ffffff; font-size: 22px; line-height: 1.35;">{{ $heading }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 28px;">
                            {{ $slot }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top: 1px solid #e4edf6; background: #fbfdff; padding: 16px 28px;">
                            <p style="margin: 0; color: #7b8ea4; font-size: 12px;">{{ $footerText }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
