@extends('admin.layouts.app')

@section('title', 'Buat Stock Opname')
@section('page-title', 'Buat Sesi Stock Opname')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.stock-opname.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label required">Nama Sesi</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                       placeholder="Contoh: Stock Opname Desember 2024" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Lokasi / Ruangan</label>
                <select name="location_id" class="form-select @error('location_id') is-invalid @enderror">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->full_path }}
                                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Pilih lokasi spesifik atau biarkan kosong untuk semua aset</small>
                @error('location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                          rows="3" placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                <strong>Informasi:</strong>
                <ul class="mb-0 mt-2">
                    <li>Sesi akan membuat daftar semua aset berdasarkan lokasi yang dipilih</li>
                    <li>Proses stock opname dilakukan dengan scan barcode aset</li>
                    <li>Anda dapat memulai sesi setelah membuatnya</li>
                </ul>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Buat Sesi
                </button>
                <a href="{{ route('admin.stock-opname.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection