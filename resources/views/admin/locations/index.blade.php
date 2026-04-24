@extends('admin.layouts.app')

@section('title', 'Manajemen Lokasi')
@section('page-title', 'Daftar Lokasi')

@section('breadcrumb')
    <li class="breadcrumb-item active">Lokasi</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Lokasi
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Lokasi</th>
                        <th>Lokasi Induk</th>
                        <th>Detail</th>
                        <th class="text-center">Jumlah Aset</th>
                        <th class="text-center">Level</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $location->code }}</span>
                        </td>
                        <td>
                            @for($i = 0; $i < $location->level; $i++)
                                &nbsp;&nbsp;&nbsp;
                            @endfor
                            @if($location->level > 0)
                                <i class="bi bi-arrow-return-right text-muted"></i>
                            @endif
                            {{ $location->name }}
                        </td>
                        <td>
                            {{ $location->parent->name ?? '-' }}
                            @if($location->parent)
                                <br>
                                <small class="text-muted">({{ $location->parent->code }})</small>
                            @endif
                        </td>
                        <td>
                            @if($location->building)
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <i class="bi bi-building"></i> {{ $location->building }}
                                </span>
                            @endif
                            @if($location->floor)
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <i class="bi bi-layers"></i> Lantai {{ $location->floor }}
                                </span>
                            @endif
                            @if($location->room)
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <i class="bi bi-door-closed"></i> {{ $location->room }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                {{ $location->assets_count }} aset
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $levelBadge = [
                                    0 => 'danger',
                                    1 => 'warning',
                                    2 => 'info',
                                    3 => 'secondary',
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
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.locations.show', $location) }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.locations.edit', $location) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Yakin ingin menghapus lokasi ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                         </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-geo-alt display-1 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada data lokasi</p>
                            <a href="{{ route('admin.locations.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus-lg"></i> Tambah Lokasi Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        Menampilkan {{ $locations->firstItem() ?? 0 }} - {{ $locations->lastItem() ?? 0 }} 
                        dari {{ $locations->total() }} data
                    </small>
                </div>
                <div>
                    {{ $locations->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection