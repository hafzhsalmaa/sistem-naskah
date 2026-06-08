@component('emails.partials.layout', ['title' => $subject])
    <p style="margin: 0 0 16px; font-size: 15px;">{{ $greeting }}</p>
    <p style="margin: 0 0 22px; color: #334e68; font-size: 15px;">{{ $opening }}</p>

    @include('emails.partials.detail-table', ['details' => $details])

    <p style="margin: 0 0 20px; color: #334e68; font-size: 15px;">{{ $bodyMessage }}</p>

    @include('emails.partials.button', [
        'url' => $actionUrl,
        'label' => $actionText,
    ])

    <p style="margin: 0; color: #334e68; font-size: 15px;">{{ $closing }}</p>
    <p style="margin: 18px 0 0; color: #102a43; font-size: 15px; font-weight: 700;">PubliSync</p>
@endcomponent
