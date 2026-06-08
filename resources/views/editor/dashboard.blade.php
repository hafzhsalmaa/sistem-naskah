@extends('layouts.app')

@section('title', 'Dashboard Editor')

@section('header')
    @php
        $editorDisplayName = Auth::user()?->editor?->nama_lengkap ?: (Auth::user()?->username ?? 'Editor');
    @endphp

    <div class="editor-dashboard-header">
        <div>
            <p class="editor-dashboard-eyebrow">Editor Dashboard</p>
            <h1 class="editor-dashboard-title">SELAMAT DATANG EDITOR {{ strtoupper($editorDisplayName) }}!</h1>
            <p class="editor-dashboard-subtitle">Pantau alur review naskah, status revisi, dan progres naskah yang ditugaskan kepada Anda dari dashboard utama.</p>
        </div>
    </div>
@endsection

@section('content')
    <section class="editor-dashboard">
        <div class="editor-dashboard-shell">
            <div class="editor-dashboard-top-grid">
                <article class="editor-dashboard-card editor-dashboard-chart-card">
                    <div class="editor-dashboard-card-header">
                        <div>
                            <p class="editor-dashboard-card-eyebrow">Insight</p>
                            <h3 class="editor-dashboard-card-title">Diagram Prosentase Penyelesaian Review Naskah</h3>
                        </div>
                        <span class="editor-dashboard-card-icon">
                            <x-icons.lock class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="editor-dashboard-chart-layout">
                        <div class="editor-dashboard-chart-side">
                            <div class="editor-donut-chart {{ $chartTotal === 0 ? 'is-empty' : '' }}" data-values="{{ $chartStatuses->pluck('value')->implode(',') }}" data-colors="{{ $chartStatuses->pluck('color')->implode(',') }}">
                                <div class="editor-donut-chart-inner">
                                    <strong>{{ $chartTotal }}</strong>
                                    <span>Total Review</span>
                                </div>
                            </div>

                            <div class="editor-dashboard-legend-list">
                                @forelse ($chartStatuses as $item)
                                    <div class="editor-dashboard-legend-item">
                                        <span class="editor-dashboard-legend-dot" style="background-color: {{ $item['color'] }}"></span>
                                        <span class="editor-dashboard-legend-label">{{ $item['label'] }}</span>
                                        <span class="editor-dashboard-legend-value">{{ $item['percentage'] }}%</span>
                                    </div>
                                @empty
                                    <p class="editor-dashboard-empty-state">Belum ada naskah review.</p>
                                @endforelse

                                @if ($chartTotal === 0)
                                    <p class="editor-dashboard-empty-state">Belum ada naskah review.</p>
                                @endif
                            </div>
                        </div>

                        <div class="editor-dashboard-metric-stack">
                            <div class="editor-dashboard-metric">
                                <span class="editor-dashboard-metric-icon is-info">
                                    <x-icons.user-circle class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="editor-dashboard-metric-label">Jumlah Editor Aktif</p>
                                    <p class="editor-dashboard-metric-value">{{ $metrics['editorActiveCount'] }}</p>
                                </div>
                            </div>

                            <div class="editor-dashboard-metric">
                                <span class="editor-dashboard-metric-icon is-blue">
                                    <x-icons.file-text class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="editor-dashboard-metric-label">Jumlah Naskah Review Saya</p>
                                    <p class="editor-dashboard-metric-value">{{ $metrics['naskahReviewSayaCount'] }}</p>
                                </div>
                            </div>

                            <div class="editor-dashboard-metric">
                                <span class="editor-dashboard-metric-icon is-warning">
                                    <x-icons.clock class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="editor-dashboard-metric-label">Rata-rata Waktu Review</p>
                                    <p class="editor-dashboard-metric-value">{{ $metrics['averageReviewDuration'] }}</p>
                                </div>
                            </div>

                            <div class="editor-dashboard-metric">
                                <span class="editor-dashboard-metric-icon is-danger">
                                    <x-icons.revision class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="editor-dashboard-metric-label">Jumlah Naskah Revisi</p>
                                    <p class="editor-dashboard-metric-value">{{ $metrics['naskahRevisiCount'] }}</p>
                                </div>
                            </div>

                            <div class="editor-dashboard-metric">
                                <span class="editor-dashboard-metric-icon is-success">
                                    <x-icons.check class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="editor-dashboard-metric-label">Review Selesai</p>
                                    <p class="editor-dashboard-metric-value">{{ $metrics['naskahSelesaiCount'] }}</p>
                                </div>
                            </div>

                            <div class="editor-dashboard-metric">
                                <span class="editor-dashboard-metric-icon is-peach">
                                    <x-icons.plus class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="editor-dashboard-metric-label">Siap / Masuk Layout</p>
                                    <p class="editor-dashboard-metric-value">{{ $metrics['naskahSiapLayoutCount'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="editor-dashboard-card">
                    <div class="editor-dashboard-card-header">
                        <div>
                            <p class="editor-dashboard-card-eyebrow">Info</p>
                            <h3 class="editor-dashboard-card-title">Info dan Announcement</h3>
                        </div>
                        <span class="editor-dashboard-card-icon">
                            <x-icons.megaphone class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="editor-dashboard-announcement-list">
                        @foreach ($announcementItems as $item)
                            <div class="editor-dashboard-announcement-item">
                                <span class="editor-dashboard-announcement-mark">></span>
                                <p>{{ $item['title'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>

            <article class="editor-dashboard-card editor-dashboard-table-card">
                <div class="editor-dashboard-card-header">
                    <div>
                        <p class="editor-dashboard-card-eyebrow">Quick Access</p>
                        <h3 class="editor-dashboard-card-title">Data Naskah Terbaru</h3>
                    </div>
                    <span class="editor-dashboard-card-icon">
                        <x-icons.file-text class="h-5 w-5" />
                    </span>
                </div>

                <div class="editor-dashboard-table-wrap">
                    <table class="editor-dashboard-table">
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
                                        <a href="{{ route('editor.naskah.show', $naskah->id_naskah) }}" class="editor-dashboard-preview-btn" aria-label="Lihat detail naskah">
                                            <x-icons.eye class="h-4 w-4" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="editor-dashboard-table-empty">Belum ada naskah review.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </div>
    </section>
@endsection
