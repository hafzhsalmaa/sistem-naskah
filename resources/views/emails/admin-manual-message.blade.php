@component('emails.partials.layout', [
    'title' => 'Pesan dari Admin',
    'footerText' => 'Email ini dikirim oleh Admin PubliSync. Silakan abaikan jika informasi ini tidak relevan untuk Anda.',
])
    <p style="margin: 0 0 16px; font-size: 15px;">Yth. Bapak/Ibu {{ $recipientName }},</p>

    <p style="margin: 0 0 22px; color: #334e68; font-size: 15px;">{!! nl2br(e($messageBody)) !!}</p>

    <p style="margin: 0; color: #334e68; font-size: 15px;">Hormat kami,</p>
    <p style="margin: 18px 0 0; color: #102a43; font-size: 15px; font-weight: 700;">PubliSync Team</p>
@endcomponent
