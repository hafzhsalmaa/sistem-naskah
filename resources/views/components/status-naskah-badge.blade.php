@props(['status' => null])

@php
    $statusText = trim((string) $status);
    $statusKey = strtolower($statusText);

    $variant = match ($statusText) {
        'Pending Review' => 'pending',
        'Perbaikan Dikirim' => 'perbaikan',
        'Revisi' => 'revisi',
        'Ditolak' => 'ditolak',
        'Diterima' => 'diterima',
        'Menunggu Layout' => 'menunggu-layout',
        'Proses Layout' => 'proses-layout',
        'Revisi Layout' => 'revisi-layout',
        'Selesai Layout' => 'selesai-layout',
        'Menunggu Jadwal Penerbitan' => 'menunggu-jadwal',
        'Terjadwal Terbit' => 'terjadwal-terbit',
        'Siap Dijadwalkan' => 'siap-dijadwalkan',
        default => str_starts_with($statusKey, 'terbit') ? 'terbit' : 'default',
    };
@endphp

<span {{ $attributes->merge(['class' => 'status-naskah-badge status-naskah-badge--'.$variant]) }}>
    {{ $statusText !== '' ? $statusText : '-' }}
</span>
