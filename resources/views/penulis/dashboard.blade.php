@extends('layouts.app')

@section('title', 'Dashboard Penulis')

@section('header')
    <div class="penulis-dashboard-header">
        <div>
            <p class="penulis-dashboard-eyebrow">Writer Dashboard</p>
            <h1 class="penulis-dashboard-title">SELAMAT DATANG PENULIS {{ strtoupper(Auth::user()->username) }}!</h1>
            <p class="penulis-dashboard-subtitle">Pantau progres naskah, revisi, dan perkembangan publikasi Anda dari dashboard utama.</p>
        </div>
    </div>
@endsection

@section('content')
    <section class="penulis-dashboard">
        <div class="penulis-dashboard-shell">
            <div class="penulis-dashboard-top-grid">
                <article class="penulis-dashboard-card penulis-dashboard-chart-card">
                    <div class="penulis-dashboard-card-header">
                        <div>
                            <p class="penulis-dashboard-card-eyebrow">Insight</p>
                            <h3 class="penulis-dashboard-card-title">Diagram Prosentase Ringkasan Data Naskah</h3>
                        </div>
                        <span class="penulis-dashboard-card-icon">
                            <x-icons.file-text class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="penulis-dashboard-chart-layout">
                        <div class="penulis-dashboard-chart-side">
                            <div class="penulis-donut-chart {{ $chartTotal === 0 ? 'is-empty' : '' }}" data-values="{{ $chartStatuses->pluck('value')->implode(',') }}" data-colors="{{ $chartStatuses->pluck('color')->implode(',') }}">
                                <div class="penulis-donut-chart-inner">
                                    <strong>{{ $chartTotal }}</strong>
                                    <span>Total Naskah</span>
                                </div>
                            </div>

                            <div class="penulis-dashboard-legend-list">
                                @forelse ($chartStatuses as $item)
                                    <div class="penulis-dashboard-legend-item">
                                        <span class="penulis-dashboard-legend-dot" style="background-color: {{ $item['color'] }}"></span>
                                        <span class="penulis-dashboard-legend-label">{{ $item['label'] }}</span>
                                        <span class="penulis-dashboard-legend-value">{{ $item['percentage'] }}%</span>
                                    </div>
                                @empty
                                    <p class="penulis-dashboard-empty-state">Belum ada data naskah.</p>
                                @endforelse

                                @if ($chartTotal === 0)
                                    <p class="penulis-dashboard-empty-state">Belum ada data naskah.</p>
                                @endif
                            </div>
                        </div>

                        <div class="penulis-dashboard-metric-stack">
                            <div class="penulis-dashboard-metric">
                                <span class="penulis-dashboard-metric-icon is-info">
                                    <x-icons.file-text class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="penulis-dashboard-metric-label">Total Naskah</p>
                                    <p class="penulis-dashboard-metric-value">{{ $metrics['totalNaskah'] }}</p>
                                </div>
                            </div>

                            <div class="penulis-dashboard-metric">
                                <span class="penulis-dashboard-metric-icon is-warning">
                                    <x-icons.clock class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="penulis-dashboard-metric-label">Pending Review</p>
                                    <p class="penulis-dashboard-metric-value">{{ $metrics['pendingReviewCount'] }}</p>
                                </div>
                            </div>

                            <div class="penulis-dashboard-metric">
                                <span class="penulis-dashboard-metric-icon is-danger">
                                    <x-icons.revision class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="penulis-dashboard-metric-label">Butuh Revisi</p>
                                    <p class="penulis-dashboard-metric-value">{{ $metrics['revisiCount'] }}</p>
                                </div>
                            </div>

                            <div class="penulis-dashboard-metric">
                                <span class="penulis-dashboard-metric-icon is-success">
                                    <x-icons.check class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="penulis-dashboard-metric-label">Menunggu Layout</p>
                                    <p class="penulis-dashboard-metric-value">{{ $metrics['menungguLayoutCount'] }}</p>
                                </div>
                            </div>

                            <div class="penulis-dashboard-metric">
                                <span class="penulis-dashboard-metric-icon is-emerald">
                                    <x-icons.check class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="penulis-dashboard-metric-label">Menunggu Jadwal</p>
                                    <p class="penulis-dashboard-metric-value">{{ $metrics['menungguJadwalCount'] }}</p>
                                </div>
                            </div>

                            <div class="penulis-dashboard-metric">
                                <span class="penulis-dashboard-metric-icon is-blue">
                                    <x-icons.plus class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="penulis-dashboard-metric-label">Naskah Minggu Ini</p>
                                    <p class="penulis-dashboard-metric-value">{{ $metrics['naskahMingguIniCount'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <div class="penulis-dashboard-side-stack">
                    <article class="penulis-dashboard-card">
                        <div class="penulis-dashboard-card-header">
                            <div>
                                <p class="penulis-dashboard-card-eyebrow">Reminder</p>
                                <h3 class="penulis-dashboard-card-title">Deadline / Reminder</h3>
                            </div>
                            <span class="penulis-dashboard-card-icon">
                                <x-icons.clock class="h-5 w-5" />
                            </span>
                        </div>

                        @if ($deadlineRevisiItems->isEmpty())
                            <p class="penulis-dashboard-empty-state">Belum ada deadline revisi.</p>
                        @else
                            <div class="penulis-dashboard-reminder-list">
                                @foreach ($deadlineRevisiItems as $item)
                                    <div class="penulis-dashboard-reminder-item">
                                        <p class="penulis-dashboard-reminder-title">{{ $item['title'] ?? 'Deadline Naskah Revisi' }}</p>
                                        <p class="penulis-dashboard-reminder-date">{{ $item['date'] ?? '-' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </article>

                    <article class="penulis-dashboard-card">
                        <div class="penulis-dashboard-card-header">
                            <div>
                                <p class="penulis-dashboard-card-eyebrow">Info</p>
                                <h3 class="penulis-dashboard-card-title">Info dan Announcement</h3>
                            </div>
                            <span class="penulis-dashboard-card-icon">
                                <x-icons.megaphone class="h-5 w-5" />
                            </span>
                        </div>

                        <div class="penulis-dashboard-announcement-list">
                            @foreach ($announcementItems as $item)
                                <div class="penulis-dashboard-announcement-item">
                                    <span class="penulis-dashboard-announcement-mark">></span>
                                    <p>{{ $item['title'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>
            </div>

            <article class="penulis-dashboard-card penulis-dashboard-table-card">
                <div class="penulis-dashboard-card-header">
                    <div>
                        <p class="penulis-dashboard-card-eyebrow">Quick Access</p>
                        <h3 class="penulis-dashboard-card-title">Data Naskah Terbaru</h3>
                    </div>
                    <span class="penulis-dashboard-card-icon">
                        <x-icons.file-text class="h-5 w-5" />
                    </span>
                </div>

                <div class="penulis-dashboard-table-wrap">
                    <table class="penulis-dashboard-table">
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
                                        <a href="{{ route('penulis.naskah.show', $naskah->id_naskah) }}" class="penulis-dashboard-preview-btn" aria-label="Lihat detail naskah">
                                            <x-icons.eye class="h-4 w-4" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="penulis-dashboard-table-empty">Belum ada data naskah.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <div class="penulis-dashboard-actions">
                <a href="{{ route('penulis.naskah.index') }}" class="penulis-dashboard-action-card">
                    <span class="penulis-dashboard-action-icon">
                        <x-icons.file-text class="h-5 w-5" />
                    </span>
                    <span class="penulis-dashboard-action-text">
                        <strong>Daftar Naskah Saya</strong>
                        <small>Pantau seluruh naskah yang sudah Anda kirim.</small>
                    </span>
                </a>

                <a href="{{ route('penulis.naskah.create') }}" class="penulis-dashboard-action-card">
                    <span class="penulis-dashboard-action-icon">
                        <x-icons.plus class="h-5 w-5" />
                    </span>
                    <span class="penulis-dashboard-action-text">
                        <strong>Kirim Naskah</strong>
                        <small>Upload naskah baru untuk masuk ke alur review.</small>
                    </span>
                </a>

                <a href="{{ route('penulis.riwayat-naskah.index') }}" class="penulis-dashboard-action-card">
                    <span class="penulis-dashboard-action-icon">
                        <x-icons.clock class="h-5 w-5" />
                    </span>
                    <span class="penulis-dashboard-action-text">
                        <strong>Riwayat Naskah</strong>
                        <small>Lihat arsip naskah yang sudah masuk tahap lanjutan.</small>
                    </span>
                </a>
            </div>
        </div>
    </section>
@endsection
