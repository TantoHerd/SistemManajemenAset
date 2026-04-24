@extends('admin.layouts.app')

@section('title', 'Laporan Maintenance')
@section('page-title', 'Laporan Maintenance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.maintenances.index') }}">Maintenance</a></li>
    <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
<!-- Statistik -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $stats['total'] }}</h3>
                <small>Total Maintenance</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                <small>Pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $stats['completed'] }}</h3>
                <small>Selesai</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3 class="mb-0">Rp {{ number_format($stats['total_cost'], 0, ',', '.') }}</h3>
                <small>Total Biaya</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Aset</label>
                <select name="asset_id" class="form-select">
                    <option value="">Semua Aset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                            {{ $asset->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $value)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.maintenances.report') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Laporan -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Aset</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Teknisi</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenances as $maintenance)
                    <tr>
                        <td>
                            <strong>{{ $maintenance->asset->name ?? '-' }}</strong>
                            <br>
                            <small>{{ $maintenance->asset->asset_code ?? '-' }}</small>
                        </td>
                        <td>{{ $maintenance->title }}</td>
                        <td>{{ $maintenance->maintenance_date ? $maintenance->maintenance_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $maintenance->technician ?? '-' }}</td>
                        <td>{{ $maintenance->formatted_cost }}</td>
                        <td>
                            <span class="badge bg-{{ $maintenance->status_badge }}-subtle text-{{ $maintenance->status_badge }}">
                                {{ $maintenance->status_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.maintenances.show', $maintenance) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox display-6 text-muted"></i>
                            <p class="text-muted mt-2">Tidak ada data maintenance</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $maintenances->links() }}
        </div>
    </div>
</div>
@endsection