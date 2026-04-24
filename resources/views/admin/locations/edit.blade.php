@extends('admin.layouts.app')

@section('title', 'Edit Lokasi')
@section('page-title', 'Edit Lokasi')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.locations.index') }}">Lokasi</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.locations.show', $location) }}">{{ $location->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.locations.show', $location) }}" class="btn btn-info">
            <i class="bi bi-eye"></i> Detail
        </a>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">
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
                    <i class="bi bi-pencil-square text-warning"></i> Edit Lokasi: 
                    <span class="text-primary">{{ $location->name }}</span>
                </h5>
                <small class="text-muted">Perbarui data lokasi di bawah ini</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.locations.update', $location) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-danger">
                            Nama Lokasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Contoh: Gedung A, Lantai 1, Ruang IT"
                               value="{{ old('name', $location->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label fw-semibold text-danger">
                            Kode Lokasi <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="code" id="code" 
                               class="form-control @error('code') is-invalid @enderror"
                               placeholder="Contoh: GDA, L1, IT-RM"
                               value="{{ old('code', $location->code) }}" required>
                        <small class="text-muted">Kode unik untuk identifikasi lokasi (max 50 karakter)</small>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="parent_id" class="form-label fw-semibold">
                            Lokasi Induk <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">-- Tanpa Induk (Root) --</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $location->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->full_path }} ({{ $parent->code }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Pilih lokasi induk jika lokasi ini berada di dalam lokasi lain.
                        </small>
                        @error('parent_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    <h6 class="mb-3"><i class="bi bi-building"></i> Detail Lokasi (Opsional)</h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="building" class="form-label">Gedung</label>
                            <input type="text" name="building" id="building" 
                                   class="form-control @error('building') is-invalid @enderror"
                                   placeholder="Nama Gedung"
                                   value="{{ old('building', $location->building) }}">
                            @error('building')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="floor" class="form-label">Lantai</label>
                            <input type="text" name="floor" id="floor" 
                                   class="form-control @error('floor') is-invalid @enderror"
                                   placeholder="Nomor Lantai"
                                   value="{{ old('floor', $location->floor) }}">
                            @error('floor')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="room" class="form-label">Ruangan</label>
                            <input type="text" name="room" id="room" 
                                   class="form-control @error('room') is-invalid @enderror"
                                   placeholder="Nama Ruangan"
                                   value="{{ old('room', $location->room) }}">
                            @error('room')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="2" 
                                  class="form-control @error('address') is-invalid @enderror"
                                  placeholder="Alamat lengkap lokasi...">{{ old('address', $location->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <a href="{{ route('admin.locations.show', $location) }}" class="btn btn-info">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Update Lokasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection