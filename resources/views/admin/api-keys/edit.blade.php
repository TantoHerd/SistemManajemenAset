@extends('admin.layouts.app')

@section('title', 'Edit API Key')
@section('page-title', 'Edit API Key')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.api-keys.update', $apiKey) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama API Key</label>
                <input type="text" name="name" class="form-control" value="{{ $apiKey->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">IP Whitelist</label>
                <input type="text" name="allowed_ips" class="form-control" 
                       value="{{ $apiKey->allowed_ips ? implode(', ', $apiKey->allowed_ips) : '' }}">
                <small class="text-muted">Pisahkan dengan koma</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal Kadaluarsa</label>
                <input type="date" name="expires_at" class="form-control" 
                       value="{{ $apiKey->expires_at ? $apiKey->expires_at->format('Y-m-d') : '' }}">
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1"
                           {{ $apiKey->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Aktif</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update
            </button>
            <a href="{{ route('admin.api-keys.index') }}" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
@endsection