@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Manajemen Aset IT')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Statistik Cards -->
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            <i class="bi bi-hdd-stack"></i> Total Aset
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalAssets) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-hdd-stack fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <i class="bi bi-check-circle"></i> Aset Tersedia
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($assetsAvailable) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <i class="bi bi-person-check"></i> Dalam Pemakaian
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($assetsInUse) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-person-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            <i class="bi bi-wrench"></i> Maintenance
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($assetsMaintenance) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-wrench fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Stats -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-primary text-white shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Total Nilai Aset
                        </div>
                        <div class="h5 mb-0 font-weight-bold">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-info text-white shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Nilai Pembelian
                        </div>
                        <div class="h5 mb-0 font-weight-bold">Rp {{ number_format($totalPurchaseValue, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cart-plus fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-warning text-white shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Total Penyusutan
                        </div>
                        <div class="h5 mb-0 font-weight-bold">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-down fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-success text-white shadow">
            <div class="card-body">
                <div class="row">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            Maintenance Selesai
                        </div>
                        <div class="h5 mb-0 font-weight-bold">{{ number_format($completedMaintenances) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-check-all fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Grafik Aset per Kategori -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-pie-chart"></i> Aset per Kategori
                </h6>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Grafik Aset per Status -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-bar-chart"></i> Status Aset
                </h6>
            </div>
            <div class="card-body">
                <canvas id="statusChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Grafik Aset per Lokasi -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-geo-alt"></i> Aset per Lokasi (Top 5)
                </h6>
            </div>
            <div class="card-body">
                <canvas id="locationChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Grafik Maintenance per Bulan -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-calendar"></i> Maintenance per Bulan
                </h6>
            </div>
            <div class="card-body">
                <canvas id="maintenanceChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Aset Terbaru -->
    <div class="col-xl-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-clock-history"></i> Aset Terbaru
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Aset</th>
                                <th>Kategori</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAssets as $asset)
                            <tr>
                                <td><code>{{ $asset->asset_code }}</code></td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->category->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'available' => 'success',
                                            'in_use' => 'primary',
                                            'maintenance' => 'warning',
                                            'damaged' => 'danger',
                                        ][$asset->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }}">
                                        {{ $asset->status_label }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">
                                    Belum ada data aset
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Maintenance Mendatang -->
    <div class="col-xl-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-calendar-check"></i> Maintenance Mendatang
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Aset</th>
                                <th>Tipe</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingMaintenances as $maintenance)
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <strong>{{ $maintenance->asset->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $maintenance->maintenance_date ? $maintenance->maintenance_date->format('d M Y') : '-' }}</small>
                                </div>
                                <span class="badge bg-label-warning">
                                    @php
                                        $diff = now()->diffInDays($maintenance->maintenance_date);
                                    @endphp
                                    {{ $diff }} hari lagi
                                </span>
                            </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Aset per Kategori
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryData = @json($categoryStats);
    new Chart(categoryCtx, {
        type: 'pie',
        data: {
            labels: categoryData.map(item => item.name),
            datasets: [{
                data: categoryData.map(item => item.total),
                backgroundColor: [
                    '#4361ee', '#3b2a9f', '#28a745', '#ffc107', 
                    '#dc3545', '#17a2b8', '#6f42c1', '#fd7e14'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Chart Aset per Status
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($statusStats);
    new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: statusData.map(item => item.name),
            datasets: [{
                label: 'Jumlah Aset',
                data: statusData.map(item => item.total),
                backgroundColor: statusData.map(item => item.color),
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Chart Aset per Lokasi
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    const locationData = @json($locationStats);
    new Chart(locationCtx, {
        type: 'bar',
        data: {
            labels: locationData.map(item => item.name),
            datasets: [{
                label: 'Jumlah Aset',
                data: locationData.map(item => item.total),
                backgroundColor: '#4361ee',
                borderRadius: 8,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Chart Maintenance per Bulan
    const maintenanceCtx = document.getElementById('maintenanceChart').getContext('2d');
    const maintenanceData = @json($maintenanceStats);
    new Chart(maintenanceCtx, {
        type: 'line',
        data: {
            labels: maintenanceData.map(item => item.month),
            datasets: [{
                label: 'Jumlah Maintenance',
                data: maintenanceData.map(item => item.total),
                backgroundColor: 'rgba(67, 97, 238, 0.1)',
                borderColor: '#4361ee',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#4361ee',
                pointBorderColor: '#fff',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
});
</script>
@endpush