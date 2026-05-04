@extends('admin.layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan & Analitik')

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('admin.reports.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i> Export PDF
        </a>
    </div>
@endsection

@section('content')
<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="location_id" class="form-select form-select-sm">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $locationId == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter"></i> Filter</button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white text-center h-100">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
                <small class="text-white-50">Total Nilai Aset</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-danger text-white text-center h-100">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</div>
                <small class="text-white-50">Total Penyusutan</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-warning text-white text-center h-100">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold">Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}</div>
                <small class="text-white-50">Biaya Maintenance</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-info text-white text-center h-100">
            <div class="card-body py-3">
                <div class="fs-4 fw-bold">{{ $totalLoans }} <small class="fs-6">({{ $overdueLoans }} overdue)</small></div>
                <small class="text-white-50">Total Peminjaman</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Chart: Maintenance per Bulan -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-graph-up me-1"></i>Maintenance 6 Bulan</h6></div>
            <div class="card-body"><canvas id="maintenanceChart" height="200"></canvas></div>
        </div>
    </div>
    
    <!-- Chart: Aset by Status -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-pie-chart me-1"></i>Aset by Status</h6></div>
            <div class="card-body"><canvas id="statusChart" height="200"></canvas></div>
        </div>
    </div>
    
    <!-- Tabel: Aset by Kategori -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-tags me-1"></i>Aset per Kategori</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    @foreach($assetsByCategory as $cat)
                    <tr><td>{{ $cat->name }}</td><td class="text-end fw-bold">{{ $cat->assets_count }}</td></tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    
    <!-- Tabel: Aset by Lokasi -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-geo-alt me-1"></i>Aset per Lokasi</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    @foreach($assetsByLocation->take(10) as $loc)
                    <tr><td>{{ $loc->name }}</td><td class="text-end fw-bold">{{ $loc->assets_count }}</td></tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    
    <!-- Tabel: Maintenance Terbaru -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-wrench me-1"></i>Maintenance Terbaru</h6>
                <small class="text-muted">{{ $maintenances->count() }} data</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>Tanggal</th><th>Aset</th><th>Judul</th><th>Biaya</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($maintenances as $m)
                            <tr>
                                <td>{{ $m->maintenance_date->format('d/m/Y') }}</td>
                                <td>{{ $m->asset->name ?? '-' }}</td>
                                <td>{{ $m->title }}</td>
                                <td>{{ number_format($m->cost, 0, ',', '.') }}</td>
                                <td><span class="badge bg-{{ $m->status == 'completed' ? 'success' : 'warning' }}">{{ $m->status_label }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Maintenance Chart
new Chart(document.getElementById('maintenanceChart'), {
    type: 'bar',
    data: {
        labels: @json(array_column($maintenanceChart, 'month')),
        datasets: [{
            label: 'Jumlah', data: @json(array_column($maintenanceChart, 'count')),
            backgroundColor: '#4361ee', borderRadius: 6
        }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});

// Status Chart
const statusData = @json($assetsByStatus->pluck('total'));
const statusLabels = @json($assetsByStatus->pluck('status'));
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{ data: statusData, backgroundColor: ['#28a745','#4361ee','#ffc107','#dc3545','#6c757d'] }]
    },
    options: { responsive: true, maintainAspectRatio: false }
});
</script>
@endpush