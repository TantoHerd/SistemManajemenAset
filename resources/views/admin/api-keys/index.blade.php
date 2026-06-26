@extends('admin.layouts.app')

@section('title', 'API Keys')
@section('page-title', 'Manajemen API Key')

@section('header-actions')
    <a href="{{ route('admin.api-keys.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Generate API Key
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>API Key</th>
                        <th>Dibuat Oleh</th>
                        <th>IP Whitelist</th>
                        <th>Status</th>
                        <th>Terakhir Digunakan</th>
                        <th>Kadaluarsa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keys as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>
                            <code class="bg-light p-1 rounded" style="font-size: 11px;">
                                {{ substr($key->key, 0, 20) }}...
                            </code>
                        </td>
                        <td>{{ $key->user->name ?? '-' }}</td>
                        <td>
                            @if($key->allowed_ips)
                                {{ implode(', ', $key->allowed_ips) }}
                            @else
                                <span class="text-muted">Semua IP</span>
                            @endif
                        </td>
                        <td>
                            @if($key->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                            @if($key->expires_at && $key->expires_at->isPast())
                                <span class="badge bg-warning">Kadaluarsa</span>
                            @endif
                        </td>
                        <td>{{ $key->last_used_at ? $key->last_used_at->diffForHumans() : '-' }}</td>
                        <td>{{ $key->expires_at ? $key->expires_at->format('d/m/Y') : 'Tidak Ada' }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.api-keys.show', $key) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.api-keys.edit', $key) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.api-keys.toggle', $key->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $key->is_active ? 'btn-secondary' : 'btn-success' }}">
                                        <i class="bi {{ $key->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.api-keys.destroy', $key) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-key fs-1 text-muted"></i>
                            <p class="mt-2">Belum ada API Key</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $keys->links() }}
@endsection