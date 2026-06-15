@extends('admin.layouts.app')

@section('title', 'Detail Log')
@section('page-title', 'Detail Aktivitas')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i> Informasi Log
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="150">Waktu</th>
                        <td>{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <th>User</th>
                        <td>{{ $auditLog->username ?? 'System' }}</td>
                    </tr>
                    <tr>
                        <th>Aksi</th>
                        <td>{!! $auditLog->action_badge !!}</td>
                    </tr>
                    <tr>
                        <th>Modul</th>
                        <td>{!! $auditLog->module_badge !!}</td>
                    </tr>
                    <tr>
                        <th>Record</th>
                        <td>{{ $auditLog->record_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Record ID</th>
                        <td><code>{{ $auditLog->record_id ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <th>IP Address</th>
                        <td><code>{{ $auditLog->ip_address ?? '-' }}</code></td>
                    </tr>
                    <tr>
                        <th>User Agent</th>
                        <td><small>{{ $auditLog->user_agent ?? '-' }}</small></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if($auditLog->old_data || $auditLog->new_data)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-code-square me-2"></i> Perubahan Data
                </h6>
            </div>
            <div class="card-body">
                @if($auditLog->old_data)
                <div class="mb-3">
                    <label class="fw-bold">Data Lama:</label>
                    <pre class="bg-light p-2 rounded small">{{ json_encode($auditLog->old_data, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif

                @if($auditLog->new_data)
                <div>
                    <label class="fw-bold">Data Baru:</label>
                    <pre class="bg-light p-2 rounded small">{{ json_encode($auditLog->new_data, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('admin.audit-log.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>
@endsection