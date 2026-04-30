@extends('admin.layouts.app')

@section('title', 'Ajukan Peminjaman')
@section('page-title', 'Form Pengajuan Peminjaman Aset')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-box-arrow-right me-1"></i>Form Peminjaman</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.loans.store') }}" method="POST">
                    @csrf
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Aset <span class="text-danger">*</span></label>
                            <select name="asset_id" class="form-select" required>
                                <option value="">Pilih Aset</option>
                                @foreach($assets as $a)
                                    <option value="{{ $a->id }}" {{ (old('asset_id', $asset->id ?? '') == $a->id) ? 'selected' : '' }}>
                                        {{ $a->name }} ({{ $a->asset_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Peminjam <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Pilih User</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="date" name="loan_date" class="form-control" value="{{ old('loan_date', date('Y-m-d')) }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Estimasi Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="expected_return_date" class="form-control" value="{{ old('expected_return_date') }}" required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Tujuan Peminjaman</label>
                            <textarea name="purpose" class="form-control" rows="2" placeholder="Contoh: Untuk meeting client, presentasi, dll">{{ old('purpose') }}</textarea>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Kondisi Aset Saat Dipinjam</label>
                            <textarea name="condition_before" class="form-control" rows="2" placeholder="Deskripsikan kondisi aset">{{ old('condition_before') }}</textarea>
                        </div>
                    </div>
                    
                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i>Ajukan Peminjaman</button>
                        <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i>Informasi</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-1"></i>Aset harus berstatus <strong>Tersedia</strong></li>
                    <li class="mb-2"><i class="bi bi-clock text-warning me-1"></i>Maksimal peminjaman <strong>14 hari</strong></li>
                    <li class="mb-2"><i class="bi bi-exclamation-triangle text-danger me-1"></i>Denda <strong>Rp 10.000/hari</strong> jika terlambat</li>
                    <li><i class="bi bi-shield-check text-primary me-1"></i>Periksa kondisi aset sebelum & sesudah</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection