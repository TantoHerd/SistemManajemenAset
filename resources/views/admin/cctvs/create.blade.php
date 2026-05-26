@extends('admin.layouts.app')

@section('title', 'Tambah CCTV')
@section('page-title', 'Tambah CCTV')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cctvs.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="CCTV Depan">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="location" class="form-control" placeholder="Lantai 1">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">IP Address <span class="text-danger">*</span></label>
                            <input type="text" name="ip_address" class="form-control" required placeholder="192.168.1.100">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Port <span class="text-danger">*</span></label>
                            <input type="number" name="port" class="form-control" value="80" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Terkait Aset</label>
                            <select name="asset_id" class="form-select">
                                <option value="">-- Tidak Terkait --</option>
                                @foreach(\App\Models\Asset::orderBy('name')->get() as $a)
                                    <option value="{{ $a->id }}" {{ old('asset_id') == $a->id ? 'selected' : '' }}>
                                        {{ $a->asset_code }} - {{ $a->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="admin">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Password</label>
                            <input type="text" name="password" class="form-control">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control" placeholder="Hikvision, Dahua">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Stream URL</label>
                            <input type="text" name="stream_url" class="form-control" placeholder="http://192.168.1.100:80/stream">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Snapshot URL</label>
                            <input type="text" name="snapshot_url" class="form-control" placeholder="http://192.168.1.100:80/snapshot.jpg">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection