@extends('admin.layouts.app')

@section('title', 'Laporan Stock Opname')
@section('page-title', 'Laporan Stock Opname')

@section('header-actions')
    <div class="d-flex gap-2 no-print">
        <button onclick="window.print()" class="btn btn-secondary btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak
        </button>
        <a href="{{ route('admin.stock-opname.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<style>
    /* Style Umum */
    .report-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        color: white;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        border: 1px solid #eef2f6;
    }
    .stat-number {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stat-label {
        color: #6b7280;
        font-size: 14px;
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
    .summary-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 16px;
        border: 1px solid #eef2f6;
        padding: 15px;
    }
    
    /* Header Perusahaan untuk Print */
    .print-header {
        display: none;
    }
    
    /* ========== STYLE PRINT ========== */
    @media print {
        /* Sembunyikan semua icon dan elemen tidak perlu */
        .no-print,
        .header-actions,
        .btn,
        .sidebar,
        .navbar,
        .main-sidebar,
        .main-header,
        .breadcrumb,
        .card-tools,
        button,
        i,
        .bi,
        [class*="bi-"] {
            display: none !important;
        }
        
        /* Tampilkan header perusahaan saat print */
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .print-header h2 {
            font-size: 18px;
            margin-bottom: 5px;
            color: #333;
        }
        .print-header p {
            font-size: 11px;
            color: #666;
            margin: 0;
        }
        
        /* Reset body */
        body {
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 11px !important;
        }
        
        /* Container */
        .container-fluid, .content-wrapper, .main-content {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        
        /* Header laporan */
        .report-header {
            background: #667eea !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            border-radius: 0 !important;
            padding: 10px 15px !important;
            margin-bottom: 15px !important;
        }
        .report-header h2 {
            font-size: 14px !important;
            margin-bottom: 3px !important;
        }
        .report-header p {
            font-size: 9px !important;
        }
        .report-header .badge {
            font-size: 8px !important;
        }
        
        /* Stat card */
        .stat-card {
            background: white !important;
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            padding: 6px !important;
            border-radius: 6px !important;
        }
        .stat-number {
            font-size: 14px !important;
        }
        .stat-label {
            font-size: 8px !important;
        }
        
        /* Summary card */
        .summary-card {
            background: #f8f9ff !important;
            border: 1px solid #ddd !important;
            padding: 8px !important;
            margin-bottom: 8px !important;
        }
        .summary-card h6 {
            font-size: 11px !important;
            margin-bottom: 6px !important;
        }
        .summary-card .fs-4 {
            font-size: 12px !important;
        }
        .summary-card small {
            font-size: 8px !important;
        }
        
        /* Tabel */
        .table-custom th, 
        .table-custom td {
            padding: 4px 6px !important;
            font-size: 9px !important;
        }
        .table-custom thead {
            background: #667eea !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table-custom th {
            font-size: 9px !important;
        }
        
        /* Card header */
        .card-header {
            padding: 5px 10px !important;
        }
        .card-header h6 {
            font-size: 10px !important;
        }
        
        /* Badge */
        .badge-found, .badge-missing, .badge-damaged, .badge-moved {
            padding: 2px 5px !important;
            font-size: 8px !important;
        }
        
        /* Progress bar */
        .progress {
            height: 4px !important;
        }
        
        /* Margin */
        .mb-4, .mt-4 {
            margin-bottom: 6px !important;
            margin-top: 6px !important;
        }
        .mb-3 {
            margin-bottom: 5px !important;
        }
        .row {
            margin-bottom: 3px !important;
        }
        
        /* Page setup */
        @page {
            size: A4;
            margin: 0.6cm;
        }
        
        /* Pastikan tidak terpotong */
        .stat-card, .card, .summary-card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    }
</style>

<!-- Header Perusahaan untuk Print (hanya muncul saat print) -->
<div class="print-header">
    <h2>{{ $companyName ?? 'SIMASET' }}</h2>
    <p>Laporan Stock Opname - Sistem Manajemen Aset IT</p>
    <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>
</div>

<div class="report-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-2 fw-bold">Laporan Stock Opname</h2>
            <p class="mb-0 opacity-75">ID: #{{ $stockOpname->id }} | Dicetak: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
        <div class="text-end">
            <div class="mb-1">Status: 
                @if($stockOpname->status == 'completed')
                    <span class="badge bg-success">✓ Selesai</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Informasi Sesi -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="summary-card">
            <h6 class="fw-bold mb-2">
                <i class="bi bi-info-circle me-2 text-primary no-print"></i>Informasi Sesi
            </h6>
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Nama Sesi</small>
                    <p class="fw-semibold mb-1">{{ $stockOpname->name }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Lokasi</small>
                    <p class="fw-semibold mb-1">{{ $stockOpname->location->name ?? 'Semua Lokasi' }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Tanggal Selesai</small>
                    <p class="fw-semibold mb-1">{{ $stockOpname->completed_at ? $stockOpname->completed_at->format('d/m/Y H:i') : '-' }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Petugas</small>
                    <p class="fw-semibold mb-1">{{ $stockOpname->creator->name }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ringkasan -->
    <div class="col-md-6 mb-3">
        <div class="summary-card">
            <h6 class="fw-bold mb-2">
                <i class="bi bi-bar-chart-steps me-2 text-primary no-print"></i>Ringkasan Verifikasi
            </h6>
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Total Aset</small>
                    <p class="fw-semibold fs-4 mb-0">{{ $summary['total'] }}</p>
                </div>
                <div class="col-6">
                    <small class="text-muted">Progress</small>
                    <p class="fw-semibold fs-4 mb-0">{{ $summary['scanned'] }}/{{ $summary['total'] }}</p>
                </div>
            </div>
            <div class="progress mt-2" style="height: 6px; border-radius: 10px;">
                @php $progressPercent = $summary['total'] > 0 ? round(($summary['scanned'] / $summary['total']) * 100) : 0; @endphp
                <div class="progress-bar bg-success" style="width: {{ $progressPercent }}%; border-radius: 10px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Card -->
<div class="row mb-3">
    <div class="col-md-3 col-6 mb-2">
        <div class="stat-card">
            <i class="bi bi-check-circle-fill text-success fs-4 no-print"></i>
            <div class="stat-number text-success">{{ $summary['found'] }}</div>
            <div class="stat-label">Ditemukan</div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="stat-card">
            <i class="bi bi-question-circle-fill text-danger fs-4 no-print"></i>
            <div class="stat-number text-danger">{{ $summary['missing'] }}</div>
            <div class="stat-label">Hilang</div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="stat-card">
            <i class="bi bi-exclamation-triangle-fill text-warning fs-4 no-print"></i>
            <div class="stat-number text-warning">{{ $summary['damaged'] }}</div>
            <div class="stat-label">Rusak</div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-2">
        <div class="stat-card">
            <i class="bi bi-arrow-repeat text-info fs-4 no-print"></i>
            <div class="stat-number text-info">{{ $summary['moved'] }}</div>
            <div class="stat-label">Berpindah</div>
        </div>
    </div>
</div>

<!-- Tabel Aset Hilang -->
@if($summary['missing'] > 0)
<div class="card mb-3">
    <div class="card-header bg-danger bg-opacity-10 border-0 py-2">
        <h6 class="mb-0 fw-bold text-danger">
            <i class="bi bi-question-circle me-2 no-print"></i> Aset Tidak Ditemukan ({{ $summary['missing'] }})
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Lokasi Terakhir</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOpname->items->where('actual_status', 'missing') as $item)
                    <tr>
                        <td>{{ $item->asset->asset_code ?? '-' }}</span></td>
                        <td>{{ $item->asset->name ?? '-' }}</span></td>
                        <td>{{ $item->expected_location ?? '-' }}</span></td>
                        <td>{{ $item->notes ?? '-' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Tabel Aset Rusak -->
@if($summary['damaged'] > 0)
<div class="card mb-3">
    <div class="card-header bg-warning bg-opacity-10 border-0 py-2">
        <h6 class="mb-0 fw-bold text-warning">
            <i class="bi bi-exclamation-triangle me-2 no-print"></i> Aset Rusak ({{ $summary['damaged'] }})
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Lokasi</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOpname->items->where('actual_status', 'damaged') as $item)
                    <tr>
                        <td>{{ $item->asset->asset_code ?? '-' }}</span></td>
                        <td>{{ $item->asset->name ?? '-' }}</span></td>
                        <td>{{ $item->expected_location ?? '-' }}</span></td>
                        <td>{{ $item->notes ?? '-' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Tabel Aset Berpindah -->
@if($summary['moved'] > 0)
<div class="card mb-3">
    <div class="card-header bg-info bg-opacity-10 border-0 py-2">
        <h6 class="mb-0 fw-bold text-info">
            <i class="bi bi-arrow-repeat me-2 no-print"></i> Aset Berpindah ({{ $summary['moved'] }})
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Lokasi Lama</th>
                        <th>Lokasi Baru</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockOpname->items->where('actual_status', 'moved') as $item)
                    <tr>
                        <td>{{ $item->asset->asset_code ?? '-' }}</span></td>
                        <td>{{ $item->asset->name ?? '-' }}</span></td>
                        <td>{{ $item->expected_location ?? '-' }}</span></td>
                        <td>{{ $item->actual_location ?? '-' }}</span></td>
                        <td>{{ $item->notes ?? '-' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Kesimpulan -->
<div class="summary-card mt-2">
    <div class="row align-items-center">
        <div class="col-md-8">
            <i class="bi bi-clipboard-check text-primary me-1 no-print"></i>
            <strong>Kesimpulan:</strong>
            <span class="text-muted">
                Dari {{ $summary['total'] }} aset, {{ $summary['found'] }} ditemukan,
                {{ $summary['missing'] }} hilang, {{ $summary['damaged'] }} rusak,
                {{ $summary['moved'] }} berpindah.
            </span>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">
                Akurasi: {{ $summary['total'] > 0 ? round(($summary['found'] / $summary['total']) * 100) : 0 }}%
            </span>
        </div>
    </div>
</div>
@endsection