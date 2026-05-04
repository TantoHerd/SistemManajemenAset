@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm">
            <i class="bi bi-eye"></i> Detail
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil-square text-warning"></i> Edit User: 
                    <span class="text-primary">{{ $user->name }}</span>
                </h5>
                <small class="text-muted">Perbarui data user di bawah ini</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-semibold">
                                No. Telepon
                            </label>
                            <input type="text" name="phone" id="phone" 
                                   class="form-control"
                                   value="{{ old('phone', $user->phone) }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- ROLES - MULTIPLE CHECKBOX -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Role <span class="text-danger">*</span>
                        </label>
                        <div class="card bg-light border">
                            <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                @foreach($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="roles[]" 
                                           value="{{ $role->name }}" 
                                           id="role_{{ $role->id }}"
                                           {{ in_array($role->name, old('roles', $userRoles ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        <strong>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</strong>
                                        @if($role->name === 'super_admin')
                                            <span class="badge bg-danger ms-1">Full Access</span>
                                        @elseif($role->name === 'admin')
                                            <span class="badge bg-warning ms-1">Administrator</span>
                                        @elseif($role->name === 'technician')
                                            <span class="badge bg-info ms-1">Teknisi</span>
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <small class="text-muted">Bisa pilih lebih dari satu role</small>
                        @error('roles')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label fw-semibold">
                            Alamat
                        </label>
                        <textarea name="address" id="address" rows="2" 
                                  class="form-control">{{ old('address', $user->address) }}</textarea>
                    </div>
                    
                    <hr>
                    
                    <!-- Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted"><i class="bi bi-clock"></i> Dibuat: {{ $user->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted"><i class="bi bi-pencil"></i> Terakhir diupdate: {{ $user->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection