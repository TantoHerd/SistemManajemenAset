@extends('admin.layouts.app')

@section('title', 'Detail Kategori - ' . $category->name)
@section('page-title', 'Detail Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Kategori</a></li>
    <li class="breadcrumb-item active">{{ $category->name }}</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informasi Kategori -->
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi Kategori
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="35%">Kode Kategori</th>
                        <td><code>{{ $category->code }}</code></td>
                    </tr>
                    <tr>
                        <th>Nama Kategori</th>
                        <td>{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $category->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Masa Manfaat</th>
                        <td>
                            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                {{ $category->useful_life_months }} bulan 
                                ({{ $category->useful_life_in_years }} tahun)
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Aset</th>
                        <td>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                {{ $category->assets->count() }} aset
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $category->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ $category->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Statistik -->
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-graph-up"></i> Statistik Kategori
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="mb-0 text-primary">{{ $category->assets->count() }}</h3>
                            <small class="text-muted">Total Aset</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            @php
                                $totalValue = $category->assets->sum('current_value');
                            @endphp
                            <h3 class="mb-0 text-success">Rp {{ number_format($totalValue, 0, ',', '.') }}</h3>
                            <small class="text-muted">Total Nilai Aset</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Aset dalam Kategori -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-hdd-stack"></i> Daftar Aset dalam Kategori Ini
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Aset</th>
                                <th>Nama Aset</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Nilai Saat Ini</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($category->assets as $asset)
                            <tr>
                                <td><code>{{ $asset->asset_code }}</code></td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->location->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'available' => 'success',
                                            'in_use' => 'primary',
                                            'maintenance' => 'warning',
                                            'damaged' => 'danger',
                                            'disposed' => 'secondary'
                                        ][$asset->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }} px-3 py-2 rounded-pill">
                                        {{ $asset->status_label }}
                                    </span>
                                </td>
                                <td>{{ $asset->formatted_current_value }}</td>
                                <td>
                                    <a href="{{ route('admin.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox display-6 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada aset dalam kategori ini</p>
                                    <a href="{{ route('admin.assets.create') }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus-lg"></i> Tambah Aset
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection