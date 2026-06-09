@extends('admin.layouts.app')

@section('title', 'Detail Stock Opname')
@section('page-title', $stockOpname->name)

@section('header-actions')
    <div class="d-flex gap-2">
        @if($stockOpname->status == 'draft')
            <form action="{{ route('admin.stock-opname.start', $stockOpname) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-play-fill me-1"></i> Mulai Stock Opname
                </button>
            </form>
        @endif
        
        @if($stockOpname->status == 'in_progress')
            <a href="{{ route('admin.stock-opname.scan', $stockOpname) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-upc-scan me-1"></i> Lanjutkan Scan
            </a>
        @endif
        
        @if($stockOpname->status == 'completed')
            <a href="{{ route('admin.stock-opname.report', $stockOpname) }}" class="btn btn-info btn-sm">
                <i class="bi bi-file-text me-1"></i> Lihat Laporan
            </a>
        @endif
        
        <a href="{{ route('admin.stock-opname.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<style>
    .info-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #eef2f6;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .info-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 15px 20px;
        color: white;
    }
    .info-card-header h6 {
        margin: 0;
        font-weight: 600;
    }
    .info-card-body {
        padding: 20px;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border: 1px solid #eef2f6;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .stat-number {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stat-label {
        color: #6b7280;
        font-size: 13px;
    }
    .progress-custom {
        height: 10px;
        border-radius: 10px;
        background: #eef2f6;
    }
    .progress-bar-custom {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        border-radius: 10px;
    }
    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #eef2f6;
    }
    .info-label {
        width: 120px;
        font-weight: 600;
        color: #6b7280;
    }
    .info-value {
        flex: 1;
        color: #1f2937;
    }
    .badge-status {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-draft {
        background: #f3f4f6;
        color: #6b7280;
    }
    .badge-progress {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-completed {
        background: #d1fae5;
        color: #065f46;
    }
    .table-custom {
        border-radius: 12px;
        overflow: hidden;
    }
    .table-custom thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .table-custom th, .table-custom td {
        padding: 10px 12px;
        vertical-align: middle;
        font-size: 13px;
    }
    .badge-found {
        background: #d1fae5;
        color: #065f46;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-missing {
        background: #fee2e2;
        color: #991b1b;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-damaged {
        background: #fed7aa;
        color: #92400e;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-moved {
        background: #dbeafe;
        color: #1e40af;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-pending {
        background: #f3f4f6;
        color: #6b7280;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
    }
</style>

<div class="row">
    <!-- Info Sesi -->
    <div class="col-md-5 mb-4">
        <div class="info-card">
            <div class="info-card-header">
                <h6><i class="bi bi-info-circle me-2"></i> Informasi Sesi</h6>
            </div>
            <div class="info-card-body">
                <div class="info-row">
                    <div class="info-label">Nama Sesi</div>
                    <div class="info-value fw-semibold">{{ $stockOpname->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Lokasi</div>
                    <div class="info-value">{{ $stockOpname->location->name ?? 'Semua Lokasi' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        @if($stockOpname->status == 'draft')
                            <span class="badge-status badge-draft"><i class="bi bi-file-earmark me-1"></i> Draft</span>
                        @elseif($stockOpname->status == 'in_progress')
                            <span class="badge-status badge-progress"><i class="bi bi-play-circle me-1"></i> Berjalan</span>
                        @else
                            <span class="badge-status badge-completed"><i class="bi bi-check-circle me-1"></i> Selesai</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dibuat Oleh</div>
                    <div class="info-value">{{ $stockOpname->creator->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dibuat Tanggal</div>
                    <div class="info-value">{{ $stockOpname->created_at->format('d/m/Y H:i') }}</div>
                </div>
                @if($stockOpname->started_at)
                <div class="info-row">
                    <div class="info-label">Dimulai</div>
                    <div class="info-value">{{ $stockOpname->started_at->format('d/m/Y H:i') }}</div>
                </div>
                @endif
                @if($stockOpname->completed_at)
                <div class="info-row">
                    <div class="info-label">Selesai</div>
                    <div class="info-value">{{ $stockOpname->completed_at->format('d/m/Y H:i') }}</div>
                </div>
                @endif
                @if($stockOpname->notes)
                <div class="info-row">
                    <div class="info-label">Catatan</div>
                    <div class="info-value">{{ $stockOpname->notes }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Statistik -->
    <div class="col-md-7 mb-4">
        <div class="info-card">
            <div class="info-card-header">
                <h6><i class="bi bi-graph-up me-2"></i> Statistik</h6>
            </div>
            <div class="info-card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <i class="bi bi-box-seam text-primary fs-3"></i>
                            <div class="stat-number">{{ $summary['total'] }}</div>
                            <div class="stat-label">Total Aset</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            <div class="stat-number text-success">{{ $summary['found'] }}</div>
                            <div class="stat-label">Ditemukan</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <i class="bi bi-question-circle-fill text-danger fs-3"></i>
                            <div class="stat-number text-danger">{{ $summary['missing'] }}</div>
                            <div class="stat-label">Hilang</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stat-card">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
                            <div class="stat-number text-warning">{{ $summary['damaged'] }}</div>
                            <div class="stat-label">Rusak</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Progress Verifikasi</span>
                    <span class="fw-bold">{{ $summary['scanned'] }}/{{ $summary['total'] }} ({{ $progress }}%)</span>
                </div>
                <div class="progress progress-custom mb-3">
                    <div class="progress-bar progress-bar-custom" style="width: {{ $progress }}%"></div>
                </div>
                
                <div class="row g-2">
                    <div class="col-4 text-center">
                        <span class="badge badge-moved">Berpindah: {{ $summary['moved'] }}</span>
                    </div>
                    <div class="col-4 text-center">
                        <span class="badge badge-pending">Tersisa: {{ $summary['total'] - $summary['scanned'] }}</span>
                    </div>
                    <div class="col-4 text-center">
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            Akurasi: {{ $summary['total'] > 0 ? round(($summary['found'] / $summary['total']) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Aset -->
<div class="card">
    <div class="card-header bg-white">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2 text-primary"></i> Daftar Aset
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Lokasi</th>
                        <th>Status Verifikasi</th>
                        <th>Petugas</th>
                        <th>Waktu Scan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockOpname->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <code class="bg-light px-2 py-1 rounded">{{ $item->asset->asset_code ?? '-' }}</code>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $item->asset->name ?? '-' }}</span>
                        </td>
                        <td>{{ $item->expected_location ?? '-' }}</td>
                        <td>
                            @if($item->scanned_at)
                                @if($item->actual_status == 'found')
                                    <span class="badge-found"><i class="bi bi-check-circle me-1"></i> Ditemukan</span>
                                @elseif($item->actual_status == 'missing')
                                    <span class="badge-missing"><i class="bi bi-question-circle me-1"></i> Hilang</span>
                                @elseif($item->actual_status == 'damaged')
                                    <span class="badge-damaged"><i class="bi bi-exclamation-triangle me-1"></i> Rusak</span>
                                @elseif($item->actual_status == 'moved')
                                    <span class="badge-moved"><i class="bi bi-arrow-repeat me-1"></i> Berpindah</span>
                                @endif
                            @else
                                <span class="badge-pending"><i class="bi bi-hourglass me-1"></i> Tertunda</span>
                            @endif
                        </td>
                        <td>{{ $item->scanner->name ?? '-' }}</td>
                        <td>{{ $item->scanned_at ? $item->scanned_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 mb-0">Belum ada aset dalam sesi ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ringkasan Singkat -->
@if($summary['moved'] > 0)
<div class="alert alert-info mt-3 mb-0">
    <div class="d-flex align-items-center">
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
        <div>
            <strong>Info:</strong> Terdapat {{ $summary['moved'] }} aset yang berpindah lokasi.
            <span class="text-muted">Perubahan lokasi telah otomatis diperbarui di database.</span>
        </div>
    </div>
</div>
@endif

@if($summary['missing'] > 0)
<div class="alert alert-danger mt-3 mb-0">
    <div class="d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
        <div>
            <strong>Perhatian:</strong> Terdapat {{ $summary['missing'] }} aset yang tidak ditemukan.
            <span class="text-muted">Segera lakukan penelusuran lebih lanjut.</span>
        </div>
    </div>
</div>
@endif

@if($summary['damaged'] > 0)
<div class="alert alert-warning mt-3 mb-0">
    <div class="d-flex align-items-center">
        <i class="bi bi-tools fs-4 me-3"></i>
        <div>
            <strong>Catatan:</strong> Terdapat {{ $summary['damaged'] }} aset yang rusak.
            <span class="text-muted">Segera lakukan perbaikan atau pengajuan penggantian.</span>
        </div>
    </div>
</div>
@endif
@endsection