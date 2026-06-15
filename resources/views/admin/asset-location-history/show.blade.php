@extends('admin.layouts.app')

@section('title', 'History Perpindahan - ' . $asset->name)
@section('page-title', 'History Perpindahan: ' . $asset->name)

@section('header-actions')
    <a href="{{ route('admin.assets.show', $asset) }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali ke Aset
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ $asset->name }}</h5>
                <small class="text-muted">Kode: {{ $asset->asset_code }} | Lokasi Saat Ini: 
                    <strong>{{ $asset->location->name ?? '-' }}</strong>
                </small>
            </div>
            <div>
                <span class="badge bg-primary">Total Perpindahan: {{ $histories->total() }}</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Dari</th>
                        <th>Ke</th>
                        <th>Alasan</th>
                        <th>Diubah Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $history->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $history->old_location_name ?? '-' }}</td>
                        <td>
                            @if($history->new_location_name)
                                <span class="text-success">{{ $history->new_location_name }}</span>
                            @else
                                <span class="text-danger">Dihapus</span>
                            @endif
                        </td>
                        <td>{{ $history->reason ?? '-' }}</td>
                        <td>{{ $history->changer->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2">Belum ada history perpindahan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $histories->links() }}
</div>
@endsection