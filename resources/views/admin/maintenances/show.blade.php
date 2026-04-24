@extends('admin.layouts.app')

@section('title', 'Detail Maintenance')
@section('page-title', 'Detail Maintenance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.maintenances.index') }}">Maintenance</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.maintenances.edit', $maintenance) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.maintenances.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Maintenance</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="35%">Aset</th>
                        <td>
                            <strong>{{ $maintenance->asset->name ?? '-' }}</strong>
                            <br>
                            <small>{{ $maintenance->asset->asset_code ?? '-' }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Judul</th>
                        <td>{{ $maintenance->title }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $maintenance->description }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Maintenance</th>
                        <td>{{ $maintenance->maintenance_date ? $maintenance->maintenance_date->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Teknisi</th>
                        <td>{{ $maintenance->technician ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Biaya</th>
                        <td>{{ $maintenance->formatted_cost }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $maintenance->status_badge }}-subtle text-{{ $maintenance->status_badge }}">
                                {{ $maintenance->status_label }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $maintenance->notes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $maintenance->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ $maintenance->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Aset</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="35%">Kode Aset</th>
                        <td>{{ $maintenance->asset->asset_code ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Nama Aset</th>
                        <td>{{ $maintenance->asset->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Serial Number</th>
                        <td>{{ $maintenance->asset->serial_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $maintenance->asset->location->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status Aset</th>
                        <td>{{ $maintenance->asset->status_label ?? '-' }}</td>
                    </tr>
                </table>
                
                <div class="mt-3">
                    <a href="{{ route('admin.assets.show', $maintenance->asset) }}" class="btn btn-info w-100">
                        <i class="bi bi-eye"></i> Lihat Detail Aset
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection