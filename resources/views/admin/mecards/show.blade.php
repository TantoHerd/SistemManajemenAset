@extends('admin.layouts.app')

@section('title', 'Detail MeCard')
@section('page-title', $mecard->name)

@section('header-actions')
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.mecards.print', $mecard) }}" class="btn btn-outline-secondary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i>Cetak Kartu
        </a>
        <a href="{{ route('admin.mecards.download-qr', $mecard) }}" class="btn btn-success btn-sm">
            <i class="bi bi-qr-code me-1"></i>Download QR
        </a>
        <a href="{{ route('admin.mecards.edit', $mecard) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Edit
        </a>
        <a href="{{ route('admin.mecards.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row g-3">
    
    <!-- Detail Kontak -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-1 text-primary"></i>Detail Kontak</h6></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th width="90">Nama</th><td>: {{ $mecard->name }}</td></tr>
                    @if($mecard->title)<tr><th>Jabatan</th><td>: {{ $mecard->title }}</td></tr>@endif
                    @if($mecard->phone)<tr><th>Telepon</th><td>: {{ $mecard->phone }}</td></tr>@endif
                    @if($mecard->email)<tr><th>Email</th><td>: {{ $mecard->email }}</td></tr>@endif
                    @if($mecard->company)<tr><th>Perusahaan</th><td>: {{ $mecard->company }}</td></tr>@endif
                    @if($mecard->address)<tr><th>Alamat</th><td>: {{ $mecard->address }}</td></tr>@endif
                    @if($mecard->note)<tr><th>Catatan</th><td>: {{ $mecard->note }}</td></tr>@endif
                </table>
            </div>
        </div>
    </div>
    
    <!-- Kartu Nama Digital -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-1 text-danger"></i>Kartu Nama Digital</h6></div>
            <div class="card-body p-0">
                <iframe src="{{ route('admin.mecards.preview', $mecard) }}" 
                        style="width: 100%; height: 280px; border: none; border-radius: 0 0 12px 12px;"
                        scrolling="no">
                </iframe>
            </div>
        </div>
    </div>
    
</div>
@endsection