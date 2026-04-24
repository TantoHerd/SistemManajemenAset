@extends('admin.layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Kategori</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.show', $category) }}">{{ $category->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-info">
            <i class="bi bi-eye"></i> Detail
        </a>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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
                    <i class="bi bi-pencil-square text-warning"></i> Edit Kategori: 
                    <span class="text-primary">{{ $category->name }}</span>
                </h5>
                <small class="text-muted">Perbarui data kategori aset di bawah ini</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-danger">
                            Nama Kategori <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Contoh: Laptop, Desktop PC, Printer"
                               value="{{ old('name', $category->name) }}" required>
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
                               value="{{ old('code', $category->code) }}" required>
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
                                  placeholder="Deskripsi kategori aset...">{{ old('description', $category->description) }}</textarea>
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
                                   value="{{ old('useful_life_months', $category->useful_life_months) }}" required>
                            <span class="input-group-text">bulan</span>
                        </div>
                        <small class="text-muted">
                            Masa manfaat standar untuk aset dengan kategori ini. 
                            {{ $category->useful_life_in_years }} tahun
                        </small>
                        @error('useful_life_months')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-info">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Update Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection