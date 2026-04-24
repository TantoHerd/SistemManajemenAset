@extends('admin.layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Kategori</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle text-primary"></i> Form Tambah Kategori
                </h5>
                <small class="text-muted">Lengkapi data kategori aset di bawah ini</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-danger">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Contoh: Laptop, Desktop PC, Printer"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="code" class="form-label fw-semibold text-danger">
                            Kode Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="code" id="code" 
                               class="form-control @error('code') is-invalid @enderror"
                               placeholder="Contoh: LAP, PC, PRN"
                               value="{{ old('code') }}" required>
                        <small class="text-muted">Kode unik untuk identifikasi kategori (max 50 karakter)</small>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">
                            Deskripsi <span class="text-muted fw-normal">(Opsional)</span>
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Deskripsi kategori aset...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="useful_life_months" class="form-label fw-semibold text-danger">
                            Masa Manfaat (Bulan) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" name="useful_life_months" id="useful_life_months" 
                                   class="form-control @error('useful_life_months') is-invalid @enderror"
                                   placeholder="48"
                                   value="{{ old('useful_life_months', 48) }}" required>
                            <span class="input-group-text">bulan</span>
                        </div>
                        <small class="text-muted">
                            Masa manfaat standar untuk aset dengan kategori ini. 
                            Contoh: Laptop 48 bulan (4 tahun), PC 60 bulan (5 tahun)
                        </small>
                        @error('useful_life_months')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection