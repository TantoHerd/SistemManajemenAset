@extends('admin.layouts.app')

@section('title', 'Edit CCTV')
@section('page-title', 'Edit CCTV - ' . $cctv->name)

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cctvs.update', $cctv) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $cctv->name) }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $cctv->location) }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">IP Address <span class="text-danger">*</span></label>
                            <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address', $cctv->ip_address) }}" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Port <span class="text-danger">*</span></label>
                            <input type="number" name="port" class="form-control" value="{{ old('port', $cctv->port) }}" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $cctv->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $cctv->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="error" {{ $cctv->status === 'error' ? 'selected' : '' }}>Error</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ old('username', $cctv->username) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Password</label>
                            <input type="text" name="password" class="form-control" value="{{ old('password', $cctv->password) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand', $cctv->brand) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Model</label>
                            <input type="text" name="model" class="form-control" value="{{ old('model', $cctv->model) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                Stream URL 
                                <small class="text-muted">(untuk live view)</small>
                            </label>
                            <input type="text" name="stream_url" class="form-control" value="{{ old('stream_url', $cctv->stream_url) }}" placeholder="http://192.168.1.100:80/stream">
                            <small class="text-muted">Hikvision: rtsp://IP:554/Streaming/Channels/1</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                Snapshot URL 
                                <small class="text-muted">(untuk preview gambar)</small>
                            </label>
                            <input type="text" name="snapshot_url" class="form-control" value="{{ old('snapshot_url', $cctv->snapshot_url) }}" placeholder="http://192.168.1.100:80/snapshot.jpg">
                            <small class="text-muted">
                                Hikvision: http://IP/ISAPI/Streaming/channels/1/picture | 
                                Dahua: http://IP/cgi-bin/snapshot.cgi?channel=1
                            </small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save"></i> Update</button>
                    <a href="{{ route('admin.cctvs.index') }}" class="btn btn-secondary mt-3">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection