@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Manajemen Aset IT')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- ============================================ -->
<!-- STATISTIK UTAMA -->
<!-- ============================================ -->
<div class="row g-3 mb-3">
    <!-- Total Aset -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Total Aset</div>
                        <div class="h3 mb-0 fw-bold">{{ number_format($totalAssets) }}</div>
                        <small class="text-muted">{{ $totalCategories }} kategori</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-hdd-stack fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Aset Tersedia -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-success border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Tersedia</div>
                        <div class="h3 mb-0 fw-bold">{{ number_format($assetsAvailable) }}</div>
                        <small class="text-success">{{ $availablePercentage }}% dari total</small>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Aset Digunakan -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-primary border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Digunakan</div>
                        <div class="h3 mb-0 fw-bold">{{ number_format($assetsInUse) }}</div>
                        <small class="text-primary">{{ $usagePercentage }}% dari total</small>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-person-check fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Maintenance -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-start border-warning border-4 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Maintenance</div>
                        <div class="h3 mb-0 fw-bold">{{ number_format($assetsMaintenance) }}</div>
                        <small class="text-warning">{{ $pendingMaintenances }} pending</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-wrench fs-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- FINANCIAL SUMMARY -->
<!-- ============================================ -->
<div class="row g-3 mb-3">
    <div class="col-md-3 col-6">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="text-white-50 small text-uppercase fw-semibold">Nilai Aset Saat Ini</div>
                <div class="h5 mb-0 fw-bold mt-1">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-info text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="text-white-50 small text-uppercase fw-semibold">Nilai Pembelian</div>
                <div class="h5 mb-0 fw-bold mt-1">Rp {{ number_format($totalPurchaseValue, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-warning text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="text-white-50 small text-uppercase fw-semibold">Total Penyusutan</div>
                <div class="h5 mb-0 fw-bold mt-1">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card bg-danger text-white shadow-sm h-100">
            <div class="card-body p-3">
                <div class="text-white-50 small text-uppercase fw-semibold">Biaya Maintenance</div>
                <div class="h5 mb-0 fw-bold mt-1">Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- CHARTS ROW 1 -->
<!-- ============================================ -->
<div class="row g-3 mb-3">
    <!-- Pie Chart: Aset per Kategori -->
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-pie-chart text-primary me-1"></i>Aset per Kategori</h6>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="260"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Doughnut Chart: Aset per Status -->
    <div class="col-xl-4 col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart text-primary me-1"></i>Status Aset</h6>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="260"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Horizontal Bar: Aset per Lokasi -->
    <div class="col-xl-4 col-lg-12">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt text-primary me-1"></i>Top Lokasi</h6>
            </div>
            <div class="card-body">
                <canvas id="locationChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- CHARTS ROW 2 -->
<!-- ============================================ -->
<div class="row g-3 mb-3">
    <!-- Line Chart: Maintenance per Bulan -->
    <div class="col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up text-primary me-1"></i>Tren Maintenance 6 Bulan</h6>
            </div>
            <div class="card-body">
                <canvas id="maintenanceChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Bar Chart: Nilai Aset per Kategori -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-currency-dollar text-primary me-1"></i>Nilai per Kategori</h6>
            </div>
            <div class="card-body">
                <canvas id="valueChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TABLES ROW -->
<!-- ============================================ -->
<div class="row g-3">
    <!-- Aset Terbaru -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-1"></i>Aset Terbaru</h6>
                <a href="{{ route('admin.assets.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentAssets as $asset)
                    <a href="{{ route('admin.assets.show', $asset) }}" class="list-group-item list-group-item-action px-3 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">{{ $asset->name }}</div>
                                <small class="text-muted">{{ $asset->asset_code }}</small>
                            </div>
                            <span class="badge bg-label-{{ $asset->status == 'available' ? 'success' : ($asset->status == 'in_use' ? 'primary' : 'warning') }} rounded-pill">
                                {{ $asset->status_label }}
                            </span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        Belum ada aset
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Maintenance Mendatang -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check text-primary me-1"></i>Maintenance Mendatang</h6>
                <a href="{{ route('admin.maintenances.schedule') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($upcomingMaintenances as $maintenance)
                    <div class="list-group-item px-3 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">{{ $maintenance->title ?? 'Tanpa Judul' }}</div>
                                <small class="text-muted">
                                    {{ $maintenance->asset->name ?? '-' }} • 
                                    {{ $maintenance->maintenance_date->format('d M Y') }}
                                </small>
                            </div>
                            <span class="badge bg-label-{{ $maintenance->days_until <= 7 ? 'danger' : 'warning' }} rounded-pill">
                                {{ $maintenance->days_until }} hari lagi
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                        Tidak ada maintenance mendatang
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Garansi Hampir Habis -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-exclamation text-primary me-1"></i>Garansi Hampir Habis</h6>
                <a href="{{ route('admin.assets.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($expiringWarranty as $asset)
                    <a href="{{ route('admin.assets.show', $asset) }}" class="list-group-item list-group-item-action px-3 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">{{ $asset->name }}</div>
                                <small class="text-muted">s/d {{ $asset->warranty_expiry->format('d M Y') }}</small>
                            </div>
                            <span class="badge bg-label-{{ $asset->days_until_warranty <= 30 ? 'danger' : 'warning' }} rounded-pill">
                                {{ $asset->days_until_warranty }} hari
                            </span>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-shield-check fs-3 d-block mb-2"></i>
                        Semua garansi aman
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Warna palette
    const palette = [
        '#4361ee', '#3f37c9', '#4895ef', '#4cc9f0', '#2ec4b6',
        '#06d6a0', '#118ab2', '#073b4c', '#ef476f', '#ffd166'
    ];
    
    // ============================================
    // CHART 1: Aset per Kategori (PIE)
    // ============================================
    const categoryData = @json($categoryStats);
    if (categoryData.length > 0 && document.getElementById('categoryChart')) {
        new Chart(document.getElementById('categoryChart'), {
            type: 'pie',
            data: {
                labels: categoryData.map(i => i.name),
                datasets: [{
                    data: categoryData.map(i => i.total),
                    backgroundColor: palette.slice(0, categoryData.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, font: { size: 11 } }
                    }
                }
            }
        });
    }
    
    // ============================================
    // CHART 2: Status Aset (DOUGHNUT)
    // ============================================
    const statusData = @json($statusStats);
    if (document.getElementById('statusChart')) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusData.map(i => i.name),
                datasets: [{
                    data: statusData.map(i => i.total),
                    backgroundColor: statusData.map(i => i.color),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, font: { size: 11 } }
                    }
                }
            }
        });
    }
    
    // ============================================
    // CHART 3: Top Lokasi (HORIZONTAL BAR)
    // ============================================
    const locationData = @json($locationStats);
    if (locationData.length > 0 && document.getElementById('locationChart')) {
        new Chart(document.getElementById('locationChart'), {
            type: 'bar',
            data: {
                labels: locationData.map(i => i.name),
                datasets: [{
                    label: 'Jumlah Aset',
                    data: locationData.map(i => i.total),
                    backgroundColor: palette,
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
    
    // ============================================
    // CHART 4: Maintenance per Bulan (LINE)
    // ============================================
    const maintenanceData = @json($maintenanceStats);
    if (document.getElementById('maintenanceChart')) {
        new Chart(document.getElementById('maintenanceChart'), {
            type: 'line',
            data: {
                labels: maintenanceData.map(i => i.month),
                datasets: [{
                    label: 'Jumlah Maintenance',
                    data: maintenanceData.map(i => i.total),
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#4361ee',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
    }
    
    // ============================================
    // CHART 5: Nilai Aset per Kategori (BAR)
    // ============================================
    const valueData = @json($valueByCategory);
    if (valueData.length > 0 && document.getElementById('valueChart')) {
        new Chart(document.getElementById('valueChart'), {
            type: 'bar',
            data: {
                labels: valueData.map(i => i.name),
                datasets: [{
                    label: 'Nilai (Rp)',
                    data: valueData.map(i => i.value),
                    backgroundColor: palette.slice(0, valueData.length),
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { 
                            callback: function(v) {
                                return 'Rp ' + (v / 1000000).toFixed(0) + 'jt';
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
    
});
</script>
@endpush

@push('styles')
<style>
.border-start { border-left-width: 4px !important; }
.card { transition: transform 0.2s; }
.card:hover { transform: translateY(-2px); }
.bg-label-success { background-color: rgba(40,167,69,.1); color: #28a745; }
.bg-label-primary { background-color: rgba(67,97,238,.1); color: #4361ee; }
.bg-label-warning { background-color: rgba(255,193,7,.1); color: #ffc107; }
.bg-label-danger { background-color: rgba(220,53,69,.1); color: #dc3545; }
</style>
@endpush