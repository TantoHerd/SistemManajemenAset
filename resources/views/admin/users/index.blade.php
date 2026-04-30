@extends('admin.layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Daftar Pengguna')

@section('breadcrumb')
    <li class="breadcrumb-item active">User</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.import') }}" class="btn btn-warning">
            <i class="bi bi-upload"></i> Import
        </a>
        <a href="{{ route('admin.users.export') }}" class="btn btn-info">
            <i class="bi bi-download"></i> Export
        </a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tambah User
        </a>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="bi bi-people"></i> Daftar Pengguna Sistem
        </h5>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari nama atau email..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    @if(request()->anyFilled(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-repeat"></i> Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Avatar</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Terakhir Login</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="avatar avatar-sm">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle" width="40" height="40">
                                @else
                                    <div class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span class="badge bg-info ms-1">Anda</span>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleColors = [
                                    'super_admin' => 'danger',
                                    'admin' => 'warning',
                                    'technician' => 'info',
                                    'user' => 'secondary',
                                ];
                                $userRole = $user->roles->first()->name ?? 'user';
                            @endphp
                            <span class="badge bg-{{ $roleColors[$userRole] }}-subtle text-{{ $roleColors[$userRole] }} px-3 py-2">
                                {{ ucfirst($userRole) }}
                            </span>
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge bg-success-subtle text-success">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.users.edit', $user) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>
                                    @if($user->id !== auth()->id())
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#passwordModal" 
                                                onclick="setUserId({{ $user->id }})">
                                            <i class="bi bi-key"></i> Ubah Password
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-{{ $user->status === 'active' ? 'ban' : 'check-circle' }}"></i>
                                                {{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Yakin ingin menghapus user ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-people display-6 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada data user</p>
                            <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-lg"></i> Tambah User Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- Modal Change Password -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-key"></i> Ubah Password User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="passwordForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setUserId(userId) {
        let form = document.getElementById('passwordForm');
        form.action = '/admin/users/' + userId + '/password';
    }
</script>
@endpush
@endsection