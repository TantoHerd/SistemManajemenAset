@extends('admin.layouts.app')

@section('title', 'Import CCTV')
@section('page-title', 'Import Data CCTV')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-1-circle text-primary me-1"></i>Download Template</h6>
            </div>
            <div class="card-body text-center py-4">
                <i class="bi bi-file-earmark-excel fs-1 text-success d-block mb-2"></i>
                <h6>Template Import CCTV</h6>
                <p class="text-muted mb-3">Download template, isi data CCTV, lalu upload kembali</p>
                <a href="{{ route('admin.cctvs.import.template') }}" class="btn btn-success">
                    <i class="bi bi-download me-1"></i> Download Template
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-2-circle text-primary me-1"></i>Upload File</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.cctvs.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">File Excel <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format: .xlsx, .xls, .csv (Max 10MB)</small>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Import</button>
                    <a href="{{ route('admin.cctvs.index') }}" class="btn btn-secondary">Batal</a>
                </form>

                @if(isset($import_errors) && count($import_errors) > 0)
                <div class="card mt-3 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Error Import ({{ $import_total ?? 0 }})</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 small">
                            @foreach($import_errors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection