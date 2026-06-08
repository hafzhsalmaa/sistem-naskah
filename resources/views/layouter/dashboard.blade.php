@extends('layouts.app')

@section('title', 'Dashboard Layouter')

@section('header')
    @php
        $layouterDisplayName = Auth::user()?->layouter?->nama_lengkap ?: (Auth::user()?->username ?? 'Layouter');
    @endphp

    <div class="layouter-dashboard-header">
        <div>
            <p class="layouter-dashboard-eyebrow">Layouter Dashboard</p>
            <h1 class="layouter-dashboard-title">SELAMAT DATANG LAYOUTER {{ strtoupper($layouterDisplayName) }}!</h1>
            <p class="layouter-dashboard-subtitle">Pantau progres layout, status naskah yang ditugaskan, dan hasil akhir layout Anda dari dashboard utama.</p>
        </div>
    </div>
@endsection

@section('content')
    <section class="layouter-dashboard">
        <div class="layouter-dashboard-shell">
            <div class="layouter-dashboard-top-grid">
                <article class="layouter-dashboard-card layouter-dashboard-chart-card">
                    <div class="layouter-dashboard-card-header">
                        <div>
                            <p class="layouter-dashboard-card-eyebrow">Insight</p>
                            <h3 class="layouter-dashboard-card-title">Diagram Prosentase Penyelesaian Layout Naskah</h3>
                        </div>
                        <span class="layouter-dashboard-card-icon">
                            <x-icons.lock class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="layouter-dashboard-chart-layout">
                        <div class="layouter-dashboard-chart-side">
                            <div class="layouter-donut-chart {{ $chartTotal === 0 ? 'is-empty' : '' }}" data-values="{{ $chartStatuses->pluck('value')->implode(',') }}" data-colors="{{ $chartStatuses->pluck('color')->implode(',') }}">
                                <div class="layouter-donut-chart-inner">
                                    <strong>{{ $chartTotal }}</strong>
                                    <span>Total Layout</span>
                                </div>
                            </div>

                            <div class="layouter-dashboard-legend-list">
                                @forelse ($chartStatuses as $item)
                                    <div class="layouter-dashboard-legend-item">
                                        <span class="layouter-dashboard-legend-dot" style="background-color: {{ $item['color'] }}"></span>
                                        <span class="layouter-dashboard-legend-label">{{ $item['label'] }}</span>
                                        <span class="layouter-dashboard-legend-value">{{ $item['percentage'] }}%</span>
                                    </div>
                                @empty
                                    <p class="layouter-dashboard-empty-state">Belum ada naskah layout.</p>
                                @endforelse

                                @if ($chartTotal === 0)
                                    <p class="layouter-dashboard-empty-state">Belum ada naskah layout.</p>
                                @endif
                            </div>
                        </div>

                        <div class="layouter-dashboard-metric-stack">
                            <div class="layouter-dashboard-metric">
                                <span class="layouter-dashboard-metric-icon is-info">
                                    <x-icons.user-circle class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="layouter-dashboard-metric-label">Jumlah Layouter Aktif</p>
                                    <p class="layouter-dashboard-metric-value">{{ $metrics['layouterActiveCount'] }}</p>
                                </div>
                            </div>

                            <div class="layouter-dashboard-metric">
                                <span class="layouter-dashboard-metric-icon is-blue">
                                    <x-icons.file-text class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="layouter-dashboard-metric-label">Jumlah Naskah Layout Saya</p>
                                    <p class="layouter-dashboard-metric-value">{{ $metrics['naskahLayoutSayaCount'] }}</p>
                                </div>
                            </div>

                            <div class="layouter-dashboard-metric">
                                <span class="layouter-dashboard-metric-icon is-warning">
                                    <x-icons.clock class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="layouter-dashboard-metric-label">Rata-rata Waktu Layout</p>
                                    <p class="layouter-dashboard-metric-value">{{ $metrics['averageLayoutDuration'] }}</p>
                                </div>
                            </div>

                            <div class="layouter-dashboard-metric">
                                <span class="layouter-dashboard-metric-icon is-danger">
                                    <x-icons.revision class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="layouter-dashboard-metric-label">Jumlah Revisi Layout</p>
                                    <p class="layouter-dashboard-metric-value">{{ $metrics['revisiLayoutCount'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="layouter-dashboard-card">
                    <div class="layouter-dashboard-card-header">
                        <div>
                            <p class="layouter-dashboard-card-eyebrow">Info</p>
                            <h3 class="layouter-dashboard-card-title">Info dan Announcement</h3>
                        </div>
                        <span class="layouter-dashboard-card-icon">
                            <x-icons.megaphone class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="layouter-dashboard-announcement-list">
                        @foreach ($announcementItems as $item)
                            <div class="layouter-dashboard-announcement-item">
                                <span class="layouter-dashboard-announcement-mark">></span>
                                <p>{{ $item['title'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>

            <article class="layouter-dashboard-card layouter-dashboard-table-card">
                <div class="layouter-dashboard-card-header">
                    <div>
                        <p class="layouter-dashboard-card-eyebrow">Quick Access</p>
                        <h3 class="layouter-dashboard-card-title">Data Naskah Terbaru</h3>
                    </div>
                    <span class="layouter-dashboard-card-icon">
                        <x-icons.file-text class="h-5 w-5" />
                    </span>
                </div>

                <div class="layouter-dashboard-table-wrap">
                    <table class="layouter-dashboard-table">
                        <thead>
                            <tr>
                                <th>Kode Naskah</th>
                                <th>Tanggal Submit</th>
                                <th>Status Naskah</th>
                                <th>Preview</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestNaskahRows as $naskah)
                                <tr>
                                    <td>{{ $naskah->kode_naskah ?? '#'.$naskah->id_naskah }}</td>
                                    <td>{{ optional($naskah->tanggal_submit)->format('d M Y H:i') ?? '-' }}</td>
                                    <td>
                                        <x-status-naskah-badge :status="$naskah->status_tampilan ?? $naskah->status_naskah" />
                                    </td>
                                    <td>
                                        <a href="{{ route('layouter.naskah.show', $naskah->id_naskah) }}" class="layouter-dashboard-preview-btn" aria-label="Lihat detail naskah">
                                            <x-icons.eye class="h-4 w-4" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="layouter-dashboard-table-empty">Belum ada naskah layout.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>
    </section>
@endsection
