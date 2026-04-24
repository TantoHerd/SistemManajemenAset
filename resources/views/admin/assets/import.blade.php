@extends('admin.layouts.app')

@section('title', 'Import Data Aset')
@section('page-title', 'Import Data Aset dari Excel')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.assets.index') }}">Aset</a></li>
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-upload text-primary"></i> Import Data Aset
                </h5>
                <small class="text-muted">Upload file Excel/CSV untuk import data aset secara massal</small>
            </div>
            <div class="card-body">
                <!-- Info Box -->
                <div class="alert alert-info">
                    <div class="d-flex">
                        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                        <div>
                            <strong>Panduan Import:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Download template CSV terlebih dahulu</li>
                                <li>Isi data sesuai dengan format yang tersedia</li>
                                <li>Kolom wajib: <strong>nama_aset, kode_kategori, kode_lokasi, harga_beli, tanggal_beli</strong></li>
                                <li>Kode kategori dan kode lokasi harus sudah ada di sistem</li>
                                <li>Status yang tersedia: tersedia, dipakai, maintenance, rusak, dihapus</li>
                                <li>Maksimal ukuran file: 5MB</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Template Download -->
                <div class="mb-4">
                    <a href="{{ url('/assets/import/template') }}" class="btn btn-outline-primary">
                        <i class="bi bi-download"></i> Download Template Excel
                    </a>
                    <small class="text-muted d-block mt-1">
                        Template berisi 2 sheet: "Data Aset" untuk mengisi data, dan "Panduan" untuk petunjuk pengisian
                    </small>
                </div>
                
                <hr>
                
                <!-- Form Upload - PAKAI URL LANGSUNG -->
                <form action="{{ url('/assets/import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="file" class="form-label fw-semibold text-danger">
                            File Excel/CSV <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file" id="file" 
                            class="form-control @error('file') is-invalid @enderror"
                            accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format: .xlsx, .xls, .csv (Max 10MB)</small>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div id="loadingSpinner" style="display: none; text-align: center; margin: 20px 0;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Sedang memproses data. Mohon tunggu...</p>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Import akan menambahkan data baru. Pastikan data sudah benar sebelum upload.
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                            <i class="bi bi-upload"></i> Import Data
                        </button>
                    </div>
                </form>
                
                <!-- Error Display -->
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
        
        <!-- Format Kolom -->
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-table"></i> Format Kolom Template
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kolom</th>
                                <th>Deskripsi</th>
                                <th>Wajib</th>
                                <th>Contoh</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>kode_aset</td><td>Kode unik aset</td><td>Tidak</td><td>AST-001</td></tr>
                            <tr><td>nama_aset</td><td>Nama aset</td><td>Ya</td><td>Dell XPS 15</td></tr>
                            <tr><td>serial_number</td><td>Nomor seri</td><td>Tidak</td><td>SN123456</td></tr>
                            <tr><td>model</td><td>Model aset</td><td>Tidak</td><td>XPS 15 9520</td></tr>
                            <tr><td>brand</td><td>Merek aset</td><td>Tidak</td><td>Dell</td></tr>
                            <tr><td>kode_kategori</td><td>Kode kategori</td><td>Ya</td><td>LAP</td></tr>
                            <tr><td>kode_lokasi</td><td>Kode lokasi</td><td>Ya</td><td>IT-RM</td></tr>
                            <tr><td>status</td><td>Status aset</td><td>Tidak</td><td>tersedia</td></tr>
                            <tr><td>tanggal_beli</td><td>Tanggal pembelian</td><td>Ya</td><td>2024-01-15</td></tr>
                            <tr><td>harga_beli</td><td>Harga beli</td><td>Ya</td><td>25000000</td></tr>
                            <tr><td>nilai_residu</td><td>Nilai residu</td><td>Tidak</td><td>2500000</td></tr>
                            <tr><td>masa_manfaat</td><td>Masa manfaat (bulan)</td><td>Tidak</td><td>48</td></tr>
                            <tr><td>garansi_berakhir</td><td>Garansi berakhir</td><td>Tidak</td><td>2026-01-15</td></tr>
                            <tr><td>catatan</td><td>Catatan tambahan</td><td>Tidak</td><td>Laptop untuk tim IT</td></tr>
                        </tbody>
                    </table>
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