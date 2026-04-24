@extends('admin.layouts.app')

@section('title', 'Edit Maintenance')
@section('page-title', 'Edit Maintenance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.maintenances.index') }}">Maintenance</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.maintenances.show', $maintenance) }}">Detail</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.maintenances.show', $maintenance) }}" class="btn btn-info">
            <i class="bi bi-eye"></i> Detail
        </a>
        <a href="{{ route('admin.maintenances.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Edit Maintenance</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.maintenances.update', $maintenance) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Aset <span class="text-danger">*</span></label>
                        <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                            <option value="">Pilih Aset</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" {{ old('asset_id', $maintenance->asset_id) == $asset->id ? 'selected' : '' }}>
                                    {{ $asset->name }} ({{ $asset->asset_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $maintenance->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" required>{{ old('description', $maintenance->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Maintenance <span class="text-danger">*</span></label>
                                <input type="date" name="maintenance_date" class="form-control @error('maintenance_date') is-invalid @enderror" 
                                       value="{{ old('maintenance_date', $maintenance->maintenance_date->format('Y-m-d')) }}" required>
                                @error('maintenance_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Teknisi</label>
                                <input type="text" name="technician" class="form-control @error('technician') is-invalid @enderror" 
                                       value="{{ old('technician', $maintenance->technician) }}">
                                @error('technician')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Biaya</label>
                                <input type="number" name="cost" class="form-control @error('cost') is-invalid @enderror" 
                                       value="{{ old('cost', $maintenance->cost) }}" step="1000">
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $maintenance->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ old('status', $maintenance->status) == 'in_progress' ? 'selected' : '' }}>Dalam Proses</option>
                                    <option value="completed" {{ old('status', $maintenance->status) == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    <option value="cancelled" {{ old('status', $maintenance->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                  rows="2">{{ old('notes', $maintenance->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.maintenances.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection