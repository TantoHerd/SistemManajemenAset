@extends('admin.layouts.app')

@section('title', 'Riwayat Maintenance')
@section('page-title', 'Riwayat Maintenance Selesai')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.maintenances.index') }}">Maintenance</a></li>
    <li class="breadcrumb-item active">Riwayat</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="bi bi-clock-history"></i> Riwayat Maintenance Selesai
        </h5>
        <small class="text-muted">Menampilkan maintenance yang sudah selesai</small>
    </div>
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
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenances as $maintenance)
                    <tr>
                        <td>
                            <strong>{{ $maintenance->asset->name ?? '-' }}</strong>
                            <br>
                            <small class="text-muted">{{ $maintenance->asset->asset_code ?? '-' }}</small>
                        </td>
                        <td>{{ $maintenance->title }}</td>
                        <td>{{ $maintenance->maintenance_date ? $maintenance->maintenance_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ $maintenance->technician ?? '-' }}</td>
                        <td>{{ $maintenance->formatted_cost }}</td>
                        <td>
                            <a href="{{ route('admin.maintenances.show', $maintenance) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-archive display-6 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada riwayat maintenance selesai</p>
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