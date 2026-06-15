@extends('admin.layouts.app')

@section('title', 'Audit Log')
@section('page-title', 'Aktivitas Sistem (Audit Log)')

@section('header-actions')
    <button class="btn btn-danger btn-sm" onclick="cleanLog()">
        <i class="bi bi-trash"></i> Hapus Log > 3 Bulan
    </button>
    <button class="btn btn-success btn-sm" onclick="exportLog()">
        <i class="bi bi-download"></i> Export
    </button>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-journal-text me-2"></i> Filter Log
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Aksi</label>
                <select name="action" class="form-select">
                    <option value="">Semua</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Modul</label>
                <select name="module" class="form-select">
                    <option value="">Semua</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                            {{ ucfirst($module) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Cari user, record, IP..." value="{{ request('search') }}">
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('admin.audit-log.index') }}" class="btn btn-secondary">
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
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Modul</th>
                        <th>Record</th>
                        <th>IP Address</th>
                        <th width="80">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <small>{{ $log->created_at->format('d/m/Y H:i:s') }}</small>
                        </td>
                        <td>
                            <strong>{{ $log->username ?? 'System' }}</strong>
                        </td>
                        <td>{!! $log->action_badge !!}</td>
                        <td>{!! $log->module_badge !!}</td>
                        <td>
                            <small class="text-muted">{{ $log->record_name ?? '-' }}</small>
                        </td>
                        <td>
                            <code class="small">{{ $log->ip_address }}</code>
                        </td>
                        <td>
                            <a href="{{ route('admin.audit-log.show', $log) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2">Belum ada aktivitas tercatat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $logs->links() }}
</div>

<script>
function exportLog() {
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.audit-log.export") }}';
    form.innerHTML = '@csrf';
    document.body.appendChild(form);
    form.submit();
}

function cleanLog() {
    if (confirm('Hapus semua log lebih dari 3 bulan? Tindakan ini tidak dapat dibatalkan!')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.audit-log.clean") }}';
        form.innerHTML = '@csrf @method("DELETE")';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection