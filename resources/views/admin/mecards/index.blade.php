@extends('admin.layouts.app')

@section('title', 'MeCard Generate')
@section('page-title', 'MeCard Generate')

@section('header-actions')
    <a href="{{ route('admin.mecards.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Buat MeCard
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Telepon</th>
                        <th>Email</th>
                        <th>Perusahaan</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mecards as $mecard)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $mecard->name }}</td>
                        <td>{{ $mecard->title ?? '-' }}</td>
                        <td>{{ $mecard->phone ?? '-' }}</td>
                        <td>{{ $mecard->email ?? '-' }}</td>
                        <td>{{ $mecard->company ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.mecards.show', $mecard) }}" class="btn btn-sm btn-info" title="Lihat"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.mecards.edit', $mecard) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="{{ route('admin.mecards.download-qr', $mecard) }}" class="btn btn-sm btn-success" title="Download QR"><i class="bi bi-qr-code"></i></a>
                                <a href="{{ route('admin.mecards.print', $mecard) }}" class="btn btn-sm btn-secondary" title="Cetak Kartu Digital"><i class="bi bi-printer"></i></a>
                                <button onclick="confirmDelete('{{ route('admin.mecards.destroy', $mecard) }}')" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada MeCard</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $mecards->links() }}
@endsection