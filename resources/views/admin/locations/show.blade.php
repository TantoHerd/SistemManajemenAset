@extends('admin.layouts.app')

@section('title', 'Detail Lokasi - ' . $location->name)
@section('page-title', 'Detail Lokasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.locations.index') }}">Lokasi</a></li>
    <li class="breadcrumb-item active">{{ $location->name }}</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informasi Lokasi -->
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi Lokasi
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="35%">Kode Lokasi</th>
                        <td><code>{{ $location->code }}</code></td>
                    </tr>
                    <tr>
                        <th>Nama Lokasi</th>
                        <td>{{ $location->name }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi Induk</th>
                        <td>
                            @if($location->parent)
                                <a href="{{ route('admin.locations.show', $location->parent) }}">
                                    {{ $location->parent->full_path }}
                                </a>
                            @else
                                <span class="text-muted">- Root -</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Full Path</th>
                        <td><span class="badge bg-info-subtle text-info px-3 py-2">{{ $location->full_path }}</span></td>
                    </tr>
                    <tr>
                        <th>Level</th>
                        <td>
                            @php
                                $levelBadge = [
                                    0 => 'danger',
                                    1 => 'warning',
                                    2 => 'info',
                                ][$location->level] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $levelBadge }}-subtle text-{{ $levelBadge }} px-3 py-2 rounded-pill">
                                Level {{ $location->level }}
                                @if($location->level == 0)
                                    (Gedung)
                                @elseif($location->level == 1)
                                    (Lantai)
                                @elseif($location->level == 2)
                                    (Ruangan)
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Gedung</th>
                        <td>{{ $location->building ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lantai</th>
                        <td>{{ $location->floor ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Ruangan</th>
                        <td>{{ $location->room ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $location->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Total Aset</th>
                        <td>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                {{ $location->assets->count() }} aset
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Dibuat</th>
                        <td>{{ $location->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Diperbarui</th>
                        <td>{{ $location->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Sub Lokasi -->
    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-diagram-3"></i> Sub Lokasi
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Detail</th>
                                <th>Jumlah Aset</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($location->children as $child)
                            <tr>
                                <td><code>{{ $child->code }}</code></td>
                                <td>
                                    <i class="bi bi-arrow-return-right text-muted"></i>
                                    {{ $child->name }}
                                </td>
                                <td>
                                    @if($child->building)
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bi bi-building"></i> {{ $child->building }}
                                        </span>
                                    @endif
                                    @if($child->floor)
                                        <span class="badge bg-secondary-subtle text-secondary">
                                            <i class="bi bi-layers"></i> Lantai {{ $child->floor }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                                        {{ $child->assets->count() }} aset
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.locations.show', $child) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-diagram-3"></i> Tidak ada sub lokasi
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

<!-- Daftar Aset dalam Lokasi -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-hdd-stack"></i> Daftar Aset di Lokasi Ini
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode Aset</th>
                                <th>Nama Aset</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Nilai Saat Ini</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($location->assets as $asset)
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
                                    <p class="text-muted mt-2">Belum ada aset di lokasi ini</p>
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