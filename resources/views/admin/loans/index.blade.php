@extends('admin.layouts.app')

@section('title', 'Daftar Peminjaman')
@section('page-title', 'Manajemen Peminjaman Aset')

@section('breadcrumb')
    <li class="breadcrumb-item active">Peminjaman</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.loans.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Ajukan Peminjaman
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="row g-2">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="statusFilter" onchange="filterStatus(this.value)">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Approval</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Sedang Dipinjam</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
            <div class="col-md-3 ms-auto">
                <input type="text" class="form-control form-control-sm" placeholder="Cari..." id="searchInput" value="{{ request('search') }}">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Aset</th>
                        <th>Peminjam</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr>
                        <td><code>{{ $loan->loan_code }}</code></td>
                        <td>
                            <a href="{{ route('admin.assets.show', $loan->asset) }}">{{ $loan->asset->name }}</a>
                            <br><small class="text-muted">{{ $loan->asset->asset_code }}</small>
                        </td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                        <td>
                            {{ $loan->expected_return_date->format('d/m/Y') }}
                            @if($loan->actual_return_date)
                                <br><small class="text-success">Kembali: {{ $loan->actual_return_date->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $loan->status_badge }}">{{ $loan->status_label }}</span>
                            @if($loan->isOverdue())
                                <br><small class="text-danger">{{ now()->diffInDays($loan->expected_return_date) }} hari terlambat</small>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($loan->status === 'pending')
                                    <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success" title="Setujui"><i class="bi bi-check"></i></button>
                                    </form>
                                @endif
                                @if(in_array($loan->status, ['active', 'overdue']))
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#returnModal{{ $loan->id }}" title="Kembalikan">
                                        <i class="bi bi-box-arrow-in-left"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Belum ada data peminjaman
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{ $loans->links() }}
@endsection

@push('scripts')
<script>
function filterStatus(status) {
    let url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    window.location = url;
}

$('#searchInput').on('keypress', function(e) {
    if (e.which === 13) {
        let url = new URL(window.location);
        url.searchParams.set('search', $(this).val());
        window.location = url;
    }
});
</script>
@endpush