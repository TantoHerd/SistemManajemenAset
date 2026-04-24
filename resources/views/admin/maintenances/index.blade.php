@extends('admin.layouts.app')

@section('title', 'Manajemen Maintenance')
@section('page-title', 'Daftar Maintenance')

@section('breadcrumb')
    <li class="breadcrumb-item active">Maintenance</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.maintenances.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Maintenance
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Aset</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Teknisi</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
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
                            <div class="btn-group">
                                <a href="{{ route('admin.maintenances.show', $maintenance) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.maintenances.edit', $maintenance) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.maintenances.destroy', $maintenance) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data maintenance</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $maintenances->links() }}
        </div>
    </div>
</div>
@endsection