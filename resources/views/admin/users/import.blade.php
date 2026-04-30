@extends('admin.layouts.app')

@section('title', 'Import User')
@section('page-title', 'Import Data User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-upload text-primary"></i> Import Data User
                </h5>
                <small class="text-muted">Upload file Excel untuk import data user secara massal</small>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Panduan Import:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Download template Excel terlebih dahulu</li>
                        <li>Isi data sesuai format template</li>
                        <li>Kolom wajib: <strong>nama_lengkap, email, role</strong></li>
                        <li>Role yang tersedia: super_admin, admin, technician, user</li>
                        <li>Status: aktif / nonaktif (default: aktif)</li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <a href="{{ route('admin.users.import.template') }}" class="btn btn-outline-primary">
                        <i class="bi bi-download"></i> Download Template Excel
                    </a>
                </div>
                
                <hr>
                
                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold text-danger">
                            File Excel <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file" id="file" 
                               class="form-control @error('file') is-invalid @enderror"
                               accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format: .xlsx, .xls, .csv (Max 5MB)</small>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Import akan menambahkan data baru. Pastikan data sudah benar sebelum upload.
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ url('/admin/users') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-upload"></i> Import Data
                        </button>
                    </div>
                </form>
                
                @if(session('import_errors'))
                    <div class="alert alert-danger mt-4">
                        <h6><i class="bi bi-exclamation-circle"></i> Detail Error:</h6>
                        <ul class="mb-0">
                            @foreach(session('import_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection