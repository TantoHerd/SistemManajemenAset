@extends('admin.layouts.app')

@section('title', 'Generate API Key')
@section('page-title', 'Generate API Key')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.api-keys.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama API Key</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Integrasi dengan HRIS" required>
                <small class="text-muted">Nama untuk mengidentifikasi API Key ini</small>
            </div>

            <div class="mb-3">
                <label class="form-label">IP Whitelist (Opsional)</label>
                <input type="text" name="allowed_ips" class="form-control" placeholder="10.42.1.100, 192.168.1.50">
                <small class="text-muted">Pisahkan dengan koma. Kosongkan untuk semua IP.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Kadaluarsa (Opsional)</label>
                <input type="date" name="expires_at" class="form-control">
                <small class="text-muted">Kosongkan untuk tidak ada batas waktu</small>
            </div>

            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Perhatian:</strong> API Key hanya akan ditampilkan sekali setelah dibuat.
                Pastikan untuk menyimpannya dengan aman.
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-key"></i> Generate API Key
            </button>
            <a href="{{ route('admin.api-keys.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection