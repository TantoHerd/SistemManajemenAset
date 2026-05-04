@extends('admin.layouts.app')

@section('title', 'Import User')
@section('page-title', 'Import Data User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        
        <!-- STEP 1: Download Template -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-1-circle text-primary me-1"></i>Download Template</h6>
            </div>
            <div class="card-body text-center py-4">
                <i class="bi bi-file-earmark-excel fs-1 text-success d-block mb-2"></i>
                <h6>Template Import User</h6>
                <p class="text-muted mb-3">
                    Template berisi <strong>3 sheet</strong>: Template Import (isi data), Petunjuk (panduan), Data Referensi (daftar role)
                </p>
                <a href="{{ route('admin.users.import.template') }}" class="btn btn-success btn-lg">
                    <i class="bi bi-download me-1"></i> Download Template Excel
                </a>
            </div>
        </div>
        
        <!-- STEP 2: Isi Template -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-2-circle text-primary me-1"></i>Isi Data di Template</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <div class="d-flex gap-2">
                        <i class="bi bi-info-circle-fill fs-4"></i>
                        <div>
                            <strong>Buka file Excel yang sudah didownload, lalu:</strong>
                            <ol class="mb-0 mt-2 small">
                                <li>Buka sheet <strong>"Template Import"</strong></li>
                                <li><strong>Hapus baris contoh (baris 2-3)</strong> yang berwarna oranye</li>
                                <li>Isi data user mulai dari baris 2</li>
                                <li>Gunakan <strong>dropdown</strong> untuk kolom: Role, Status</li>
                                <li>Lihat sheet <strong>"Petunjuk"</strong> untuk panduan lengkap</li>
                                <li>Lihat sheet <strong>"Data Referensi"</strong> untuk daftar role & permission</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- STEP 3: Upload -->
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-3-circle text-primary me-1"></i>Upload File</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold">
                            <i class="bi bi-file-earmark-excel text-success me-1"></i>Pilih File Excel
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file" id="file" 
                            class="form-control @error('file') is-invalid @enderror"
                            accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">
                            <i class="bi bi-check-circle text-success"></i> Format: .xlsx, .xls, .csv | Max: 10MB
                        </small>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Loading -->
                    <div id="loadingSpinner" style="display: none; text-align: center; margin: 20px 0;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 fw-semibold">Sedang memproses data. Mohon tunggu...</p>
                        <small class="text-muted">Proses import mungkin memerlukan waktu beberapa saat</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                            <i class="bi bi-upload me-1"></i> Import Data
                        </button>
                    </div>
                </form>
                
                <!-- Error Display -->
                @if(session('import_errors'))
                <div class="alert alert-danger mt-3 mb-0">
                    <h6><i class="bi bi-exclamation-circle me-1"></i>Detail Error ({{ count(session('import_errors')) }}):</h6>
                    <ul class="mb-0 small">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
        
        <!-- INFO TAMBAHAN -->
        <div class="row g-3">
            <!-- Kolom Wajib -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 small fw-bold"><i class="bi bi-check-circle text-success me-1"></i>Kolom Wajib</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0 small">
                            <tr><td class="fw-semibold">nama_lengkap</td><td>Nama lengkap user</td></tr>
                            <tr><td class="fw-semibold">email</td><td>Email valid & unik</td></tr>
                            <tr><td class="fw-semibold">role</td><td>Role (dropdown)</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Tips -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-white py-2">
                        <h6 class="mb-0 small fw-bold"><i class="bi bi-lightbulb text-warning me-1"></i>Tips</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li class="mb-1">Gunakan <strong>dropdown</strong> untuk Role & Status</li>
                            <li class="mb-1">Password default: <strong>password123</strong></li>
                            <li class="mb-1">Role tersedia: super_admin, admin, technician, user, viewer</li>
                            <li>Lihat sheet <strong>"Data Referensi"</strong> untuk info role</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('importForm')?.addEventListener('submit', function() {
        let fileInput = document.getElementById('file');
        if (fileInput.files.length > 0) {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('submitBtn').innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
        }
    });
</script>
@endpush