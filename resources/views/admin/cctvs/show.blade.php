@extends('admin.layouts.app')

@section('title', $cctv->name)
@section('page-title', $cctv->name)

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <!-- Live Snapshot -->
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-camera me-1"></i>Live Preview</h6>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshSnapshot()">
                    <i class="bi bi-arrow-repeat"></i> Refresh
                </button>
            </div>
            <div class="card-body p-0 bg-dark text-center" style="min-height: 400px;">
                <img src="{{ route('admin.cctvs.snapshot', $cctv) }}" id="snapshot" class="img-fluid" onerror="this.style.display='none'; document.getElementById('noSignal').style.display='block';">
                <div id="noSignal" style="display: none; padding: 100px 0; color: #666;">
                    <i class="bi bi-camera-video-off fs-1 d-block mb-2"></i>
                    No Signal
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Info -->
        <div class="card mb-3">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-info-circle me-1"></i>Informasi</h6></div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th>Status</th><td><span class="badge bg-{{ $cctv->status === 'active' ? 'success' : 'danger' }}">{{ $cctv->status === 'active' ? 'ONLINE' : 'OFFLINE' }}</span></td></tr>
                    <tr><th>IP</th><td>{{ $cctv->ip_address }}:{{ $cctv->port }}</td></tr>
                    @if($cctv->brand)<tr><th>Brand</th><td>{{ $cctv->brand }}</td></tr>@endif
                    @if($cctv->model)<tr><th>Model</th><td>{{ $cctv->model }}</td></tr>@endif
                    @if($cctv->location)<tr><th>Lokasi</th><td>{{ $cctv->location }}</td></tr>@endif
                </table>
                
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ $cctv->stream_url }}" target="_blank" class="btn btn-success btn-sm">
                        <i class="bi bi-play-circle me-1"></i>Live View
                    </a>
                    <button onclick="pingSingle({{ $cctv->id }})" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-broadcast me-1"></i>Ping
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshSnapshot() {
    document.getElementById('snapshot').src = '{{ route("admin.cctvs.snapshot", $cctv) }}?' + new Date().getTime();
}
setInterval(refreshSnapshot, 10000); // Auto refresh setiap 10 detik
</script>
@endpush