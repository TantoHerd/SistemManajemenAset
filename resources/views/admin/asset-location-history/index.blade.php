@extends('admin.layouts.app')

@section('title', 'History Perpindahan Aset')
@section('page-title', 'History Perpindahan Aset')

@section('header-actions')
    <button onclick="exportHistory()" class="btn btn-success btn-sm">
        <i class="bi bi-download"></i> Export
    </button>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-filter me-2"></i> Filter
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Aset</label>
                <select name="asset_id" class="form-select">
                    <option value="">Semua Aset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}" {{ request('asset_id') == $asset->id ? 'selected' : '' }}>
                            {{ $asset->asset_code }} - {{ $asset->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Cari</label>
                <input type="text" name="search" class="form-control" placeholder="Lokasi..." value="{{ request('search') }}">
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.asset-location-history.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-repeat"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Waktu</th>
                        <th>Aset</th>
                        <th>Perpindahan</th>
                        <th>Alasan</th>
                        <th>Diubah Oleh</th>
                        <th width="80">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $history)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <small>{{ $history->created_at->format('d/m/Y H:i:s') }}</small>
                        </td>
                        <td>
                            <strong>{{ $history->asset->name ?? '-' }}</strong>
                            <br>
                            <small class="text-muted">{{ $history->asset->asset_code ?? '-' }}</small>
                        </td>
                        <td>
                            {!! $history->movement_text !!}
                        </td>
                        <td>{{ $history->reason ?? '-' }}</td>
                        <td>{{ $history->changer->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.assets.show', $history->asset_id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2">Belum ada history perpindahan aset</p>
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

<script>
function exportHistory() {
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.asset-location-history.export") }}';
    form.innerHTML = '@csrf';
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection