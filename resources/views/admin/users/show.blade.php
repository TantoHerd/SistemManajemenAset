@extends('admin.layouts.app')

@section('title', 'Detail User - ' . $user->name)
@section('page-title', 'Detail User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="mb-3">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle" width="120" height="120">
                    @else
                        <div class="avatar-initial rounded-circle bg-label-primary mx-auto" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                <div class="mb-2">
                    @php
                        $roleColors = [
                            'super_admin' => 'danger',
                            'admin' => 'warning',
                            'technician' => 'info',
                            'user' => 'secondary',
                        ];
                    @endphp
                    <span class="badge bg-{{ $roleColors[$user->roles->first()->name ?? 'user'] }}-subtle text-{{ $roleColors[$user->roles->first()->name ?? 'user'] }} px-3 py-2">
                        {{ ucfirst($user->roles->first()->name ?? '-') }}
                    </span>
                </div>
                @if($user->status === 'active')
                    <span class="badge bg-success-subtle text-success">Aktif</span>
                @else
                    <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi Lengkap
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="30%">Nama Lengkap</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td>{{ $user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $user->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ ucfirst($user->roles->first()->name ?? '-') }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Terakhir Login</th>
                        <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i:s') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>IP Terakhir</th>
                        <td>{{ $user->last_login_ip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Bergabung Sejak</th>
                        <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection