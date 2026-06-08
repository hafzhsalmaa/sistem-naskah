@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('header')
    <div class="admin-dashboard-header">
        <div>
            <p class="admin-dashboard-eyebrow">Admin Dashboard</p>
            <h1 class="admin-dashboard-title">SELAMAT DATANG, ADMIN!</h1>
            <p class="admin-dashboard-subtitle">Pantau seluruh aktivitas publikasi dari dashboard utama.</p>
        </div>
    </div>
@endsection

@section('content')
    <section class="admin-dashboard">
        <div class="admin-dashboard-shell">
            <div class="admin-dashboard-chart-grid">
                <article class="admin-dashboard-card admin-dashboard-chart-card">
                    <div class="admin-dashboard-card-header">
                        <div>
                            <p class="admin-dashboard-card-eyebrow">Dashboard Insight</p>
                            <h3 class="admin-dashboard-card-title">Diagram Prosentase Penyelesaian Review Naskah</h3>
                        </div>
                        <span class="admin-dashboard-card-icon is-blue">
                            <x-icons.chart-line class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="admin-dashboard-chart-layout">
                        <div class="admin-dashboard-chart-side">
                            <div class="admin-donut-chart {{ $reviewChart['total'] === 0 ? 'is-empty' : '' }}" data-values="{{ collect($reviewChart['items'])->pluck('value')->implode(',') }}" data-colors="{{ collect($reviewChart['items'])->pluck('color')->implode(',') }}">
                                <div class="admin-donut-chart-inner">
                                    <strong>{{ $reviewChart['total'] > 0 ? '100%' : '0%' }}</strong>
                                    <span>Review</span>
                                </div>
                            </div>

                            <div class="admin-dashboard-legend-list">
                                @foreach ($reviewChart['items'] as $item)
                                    <div class="admin-dashboard-legend-item">
                                        <span class="admin-dashboard-legend-dot" style="background-color: {{ $item['color'] }}"></span>
                                        <span class="admin-dashboard-legend-label">{{ $item['label'] }}</span>
                                        <span class="admin-dashboard-legend-value">{{ $item['percentage'] }}%</span>
                                    </div>
                                @endforeach
                                @if ($reviewChart['total'] === 0)
                                    <p class="admin-dashboard-empty-state">Belum ada data naskah.</p>
                                @endif
                            </div>
                        </div>

                        <div class="admin-dashboard-metric-stack">
                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-success">
                                    <x-icons.active-columns class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Jumlah Editor Aktif</p>
                                    <p class="admin-dashboard-metric-value">{{ $editorActiveCount }}</p>
                                </div>
                            </div>

                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-danger">
                                    <x-icons.clock class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Jumlah Naskah Review</p>
                                    <p class="admin-dashboard-metric-value">{{ $naskahReviewCount }}</p>
                                </div>
                            </div>

                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-info">
                                    <x-icons.plus class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Naskah Minggu Ini</p>
                                    <p class="admin-dashboard-metric-value">{{ $naskahMingguIniCount }}</p>
                                </div>
                            </div>

                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-warning">
                                    <x-icons.warning class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Jumlah Naskah Revisi</p>
                                    <p class="admin-dashboard-metric-value">{{ $naskahRevisiCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="admin-dashboard-card admin-dashboard-chart-card">
                    <div class="admin-dashboard-card-header">
                        <div>
                            <p class="admin-dashboard-card-eyebrow">Dashboard Insight</p>
                            <h3 class="admin-dashboard-card-title">Diagram Prosentase Penyelesaian Layout Naskah</h3>
                        </div>
                        <span class="admin-dashboard-card-icon is-purple">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h16.5M3.75 9.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </span>
                    </div>

                    <div class="admin-dashboard-chart-layout">
                        <div class="admin-dashboard-chart-side">
                            <div class="admin-donut-chart {{ $layoutChart['total'] === 0 ? 'is-empty' : '' }}" data-values="{{ collect($layoutChart['items'])->pluck('value')->implode(',') }}" data-colors="{{ collect($layoutChart['items'])->pluck('color')->implode(',') }}">
                                <div class="admin-donut-chart-inner">
                                    <strong>{{ $layoutChart['total'] > 0 ? '100%' : '0%' }}</strong>
                                    <span>Layout</span>
                                </div>
                            </div>

                            <div class="admin-dashboard-legend-list">
                                @foreach ($layoutChart['items'] as $item)
                                    <div class="admin-dashboard-legend-item">
                                        <span class="admin-dashboard-legend-dot" style="background-color: {{ $item['color'] }}"></span>
                                        <span class="admin-dashboard-legend-label">{{ $item['label'] }}</span>
                                        <span class="admin-dashboard-legend-value">{{ $item['percentage'] }}%</span>
                                    </div>
                                @endforeach
                                @if ($layoutChart['total'] === 0)
                                    <p class="admin-dashboard-empty-state">Belum ada data naskah.</p>
                                @endif
                            </div>
                        </div>

                        <div class="admin-dashboard-metric-stack">
                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-success">
                                    <x-icons.active-columns class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Jumlah Layouter Aktif</p>
                                    <p class="admin-dashboard-metric-value">{{ $layouterActiveCount }}</p>
                                </div>
                            </div>

                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-danger">
                                    <x-icons.clock class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Jumlah Naskah Layout</p>
                                    <p class="admin-dashboard-metric-value">{{ $naskahLayoutCount }}</p>
                                </div>
                            </div>

                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-info">
                                    <x-icons.plus class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Menunggu Jadwal Penerbitan</p>
                                    <p class="admin-dashboard-metric-value">{{ $naskahMenungguJadwalCount }}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </article>
            </div>

            <div class="admin-dashboard-chart-grid">
                <article class="admin-dashboard-card admin-dashboard-chart-card">
                    <div class="admin-dashboard-card-header">
                        <div>
                            <p class="admin-dashboard-card-eyebrow">Dashboard Insight</p>
                            <h3 class="admin-dashboard-card-title">Diagram Prosentase Penulis Naskah</h3>
                        </div>
                        <span class="admin-dashboard-card-icon is-orange">
                            <x-icons.user-circle class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="admin-dashboard-chart-layout">
                        <div class="admin-dashboard-chart-side">
                            <div class="admin-donut-chart {{ $penulisChart['total'] === 0 ? 'is-empty' : '' }}" data-values="{{ collect($penulisChart['items'])->pluck('value')->implode(',') }}" data-colors="{{ collect($penulisChart['items'])->pluck('color')->implode(',') }}">
                                <div class="admin-donut-chart-inner">
                                    <strong>{{ $penulisChart['total'] > 0 ? '100%' : '0%' }}</strong>
                                    <span>Penulis</span>
                                </div>
                            </div>

                            <div class="admin-dashboard-legend-list">
                                @foreach ($penulisChart['items'] as $item)
                                    <div class="admin-dashboard-legend-item">
                                        <span class="admin-dashboard-legend-dot" style="background-color: {{ $item['color'] }}"></span>
                                        <span class="admin-dashboard-legend-label">{{ $item['label'] }}</span>
                                        <span class="admin-dashboard-legend-value">{{ $item['percentage'] }}%</span>
                                    </div>
                                @endforeach
                                @if ($penulisChart['total'] === 0)
                                    <p class="admin-dashboard-empty-state">Belum ada data naskah.</p>
                                @endif
                            </div>
                        </div>

                        <div class="admin-dashboard-metric-stack">
                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-success">
                                    <x-icons.active-columns class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Jumlah Penulis Terdaftar</p>
                                    <p class="admin-dashboard-metric-value">{{ $penulisTerdaftarCount }}</p>
                                </div>
                            </div>

                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-danger">
                                    <x-icons.clock class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Jumlah Naskah Minggu Ini</p>
                                    <p class="admin-dashboard-metric-value">{{ $naskahMingguIniCount }}</p>
                                </div>
                            </div>

                            <div class="admin-dashboard-metric">
                                <span class="admin-dashboard-metric-icon is-info">
                                    <x-icons.plus class="h-5 w-5" />
                                </span>
                                <div>
                                    <p class="admin-dashboard-metric-label">Naskah Pending Review</p>
                                    <p class="admin-dashboard-metric-value">{{ $naskahPendingReviewCount }}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </article>

                <article class="admin-dashboard-card admin-dashboard-table-card">
                    <div class="admin-dashboard-card-header">
                        <div>
                            <p class="admin-dashboard-card-eyebrow">Quick Access</p>
                            <h3 class="admin-dashboard-card-title">Data Naskah Terbaru</h3>
                        </div>
                        <span class="admin-dashboard-card-icon is-blue">
                            <x-icons.file-text class="h-5 w-5" />
                        </span>
                    </div>

                    <div class="admin-dashboard-table-wrap">
                        <table class="admin-dashboard-table">
                            <thead>
                                <tr>
                                    <th>Kode Naskah</th>
                                    <th>Tanggal Submit</th>
                                    <th>Status Naskah</th>
                                    <th>Preview</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestNaskahRows as $row)
                                    <tr>
                                        <td>{{ $row->kode_naskah ?? '#'.$row->id_naskah }}</td>
                                        <td>{{ optional($row->tanggal_submit)->format('d M Y H:i') ?? '-' }}</td>
                                        <td>
                                            <x-status-naskah-badge :status="$row->status_tampilan ?? $row->status_naskah" />
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.naskah.index') }}" class="admin-dashboard-preview-btn" aria-label="Lihat data naskah">
                                                <x-icons.eye class="h-4 w-4" />
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($latestNaskahRows->isEmpty())
                                    <tr>
                                        <td colspan="4" class="admin-dashboard-table-empty">Belum ada data naskah.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            <div class="admin-dashboard-actions">
                <a href="{{ route('admin.data-penulis.create') }}" class="admin-dashboard-action-card">
                    <span class="admin-dashboard-action-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v9m-4.5-4.5H22.5m-13.5 8.25h-4.875c-1.313 0-2.625-.656-3.281-1.969a4.5 4.5 0 0 1-.094-3.968l2.572-5.144A4.5 4.5 0 0 1 7.353 6.75H12m-3 13.5V4.5a2.25 2.25 0 0 1 2.25-2.25H12m0 0a2.25 2.25 0 0 1 2.25 2.25v15.75M12 2.25h3.75" />
                        </svg>
                    </span>
                    <span class="admin-dashboard-action-text">
                        <strong>Tambah Penulis</strong>
                        <small>Kelola data penulis terdaftar</small>
                    </span>
                </a>

                <a href="{{ route('admin.data-editor.create') }}" class="admin-dashboard-action-card">
                    <span class="admin-dashboard-action-icon">
                        <x-icons.user-circle class="h-5 w-5" />
                    </span>
                    <span class="admin-dashboard-action-text">
                        <strong>Tambah Editor</strong>
                        <small>Atur kebutuhan review naskah</small>
                    </span>
                </a>

                <a href="{{ route('admin.data-layouter.create') }}" class="admin-dashboard-action-card">
                    <span class="admin-dashboard-action-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5h16.5M3.75 9.75h16.5m-16.5 5.25h9.75m-9.75 5.25h16.5" />
                        </svg>
                    </span>
                    <span class="admin-dashboard-action-text">
                        <strong>Tambah Layouter</strong>
                        <small>Distribusikan proses layout akhir</small>
                    </span>
                </a>

                <a href="{{ route('admin.jadwal-penerbitan.index') }}" class="admin-dashboard-action-card">
                    <span class="admin-dashboard-action-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6.75h15A1.5 1.5 0 0 1 21 8.25v11.25A1.5 1.5 0 0 1 19.5 21h-15A1.5 1.5 0 0 1 3 19.5V8.25A1.5 1.5 0 0 1 4.5 6.75Z" />
                        </svg>
                    </span>
                    <span class="admin-dashboard-action-text">
                        <strong>Buat Jadwal</strong>
                        <small>Tentukan rencana penerbitan</small>
                    </span>
                </a>
            </div>
        </div>
    </section>
@endsection
