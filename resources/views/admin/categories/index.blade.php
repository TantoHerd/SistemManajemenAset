@extends('admin.layouts.app')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Daftar Kategori Aset')

@section('breadcrumb')
    <li class="breadcrumb-item active">Kategori</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
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
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Masa Manfaat</th>
                        <th class="text-center">Jumlah Aset</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $category->code }}</span>
                        </td>
                        <td>{{ $category->name }}</td>
                        <td>
                            {{ Str::limit($category->description, 50) ?? '-' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                {{ $category->useful_life_months }} bulan
                                <small>({{ $category->useful_life_in_years }} tahun)</small>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                {{ $category->assets_count }} aset
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.categories.show', $category) }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.categories.edit', $category) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-tags display-1 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada data kategori</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus-lg"></i> Tambah Kategori Pertama
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
                        Menampilkan {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} 
                        dari {{ $categories->total() }} data
                    </small>
                </div>
                <div>
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection