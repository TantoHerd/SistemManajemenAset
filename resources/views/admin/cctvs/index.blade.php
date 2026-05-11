@extends('admin.layouts.app')

@section('title', 'CCTV')
@section('page-title', 'Manajemen CCTV')

@section('header-actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" onclick="pingAll()">
            <i class="bi bi-arrow-repeat me-1"></i>Ping Semua
        </button>
        <a href="{{ route('admin.cctvs.import') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-upload me-1"></i>Import
        </a>
        <a href="{{ route('admin.cctvs.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Tambah CCTV
        </a>
    </div>
@endsection

@section('content')
<!-- Per Page + Info -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small">Tampilkan</span>
        <form method="GET" id="perPageForm">
            <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <option value="8" {{ ($perPage ?? 12) == 8 ? 'selected' : '' }}>8</option>
                <option value="12" {{ ($perPage ?? 12) == 12 ? 'selected' : '' }}>12</option>
                <option value="16" {{ ($perPage ?? 12) == 16 ? 'selected' : '' }}>16</option>
                <option value="24" {{ ($perPage ?? 12) == 24 ? 'selected' : '' }}>24</option>
            </select>
        </form>
        <span class="text-muted small">per halaman</span>
    </div>
    <small class="text-muted">
        Total: <strong>{{ $cctvs->total() }}</strong> CCTV
    </small>
</div>
<div class="row g-3">
    @forelse($cctvs as $cctv)
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card shadow-sm h-100" id="cctv-{{ $cctv->id }}">
            <div class="position-absolute top-0 end-0 m-2" id="status-{{ $cctv->id }}">
                <span class="badge bg-{{ $cctv->status === 'active' ? 'success' : 'danger' }}">
                    {{ $cctv->status === 'active' ? 'ONLINE' : 'OFFLINE' }}
                </span>
            </div>
            
            <!-- Snapshot -->
            <div class="text-center bg-dark rounded-top" style="height: 140px; overflow: hidden;">
                <img src="{{ route('admin.cctvs.snapshot', $cctv) }}" 
                     alt="Snapshot" 
                     id="snap-{{ $cctv->id }}"
                     class="w-100 h-100"
                     style="object-fit: cover;"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div style="display: none; height: 100%; align-items: center; justify-content: center; color: #666; font-size: 13px;">
                    <i class="bi bi-camera-video-off me-1"></i> No Signal
                </div>
            </div>
            
            <div class="card-body text-center py-2">
                <h6 class="fw-bold mb-0 small">{{ $cctv->name }}</h6>
                <small class="text-muted">{{ $cctv->ip_address }}:{{ $cctv->port }}</small>
                @if($cctv->location)
                    <br><small class="text-muted">📍 {{ $cctv->location }}</small>
                @endif
                
                <div class="mt-2 d-flex justify-content-center gap-1 flex-wrap">
                    <a href="{{ route('admin.cctvs.show', $cctv) }}" class="btn btn-sm btn-info" title="Detail"><i class="bi bi-eye"></i></a>
                    <a href="{{ $cctv->stream_url }}" target="_blank" class="btn btn-sm btn-success" title="Live View"><i class="bi bi-play-circle"></i></a>
                    <button onclick="pingSingle({{ $cctv->id }})" class="btn btn-sm btn-outline-primary" title="Ping"><i class="bi bi-broadcast"></i></button>
                    <a href="{{ route('admin.cctvs.edit', $cctv) }}" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                    <button onclick="confirmDelete('{{ route('admin.cctvs.destroy', $cctv) }}')" class="btn btn-sm btn-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-camera-video-off fs-1 d-block mb-2"></i>
        <p>Belum ada CCTV</p>
        <a href="{{ route('admin.cctvs.create') }}" class="btn btn-primary btn-sm">Tambah CCTV</a>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-3">
    {{ $cctvs->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endsection

@push('styles')
<style>
.pagination {
    --bs-pagination-padding-x: 0.6rem;
    --bs-pagination-padding-y: 0.3rem;
    --bs-pagination-font-size: 0.8rem;
    gap: 3px;
}
.pagination .page-link {
    border-radius: 6px !important;
    border: none;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.pagination .page-item.active .page-link {
    background: #1E3A5F;
}
</style>
@endpush

@push('scripts')
<script>
function pingSingle(id) {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    fetch(`/admin/cctvs/${id}/ping`)
        .then(r => r.json())
        .then(data => {
            const statusEl = document.getElementById('status-' + id);
            if (data.online) {
                statusEl.innerHTML = '<span class="badge bg-success">ONLINE</span>';
                toastr.success(data.ip + ' - Online');
            } else {
                statusEl.innerHTML = '<span class="badge bg-danger">OFFLINE</span>';
                toastr.error(data.ip + ' - Offline');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-broadcast"></i>';
        });
}

function pingAll() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Ping...';
    
    fetch('/admin/cctvs/ping-all', { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'} })
        .then(r => r.json())
        .then(data => {
            data.forEach(c => {
                const statusEl = document.getElementById('status-' + c.id);
                if (c.online) {
                    statusEl.innerHTML = '<span class="badge bg-success">ONLINE</span>';
                } else {
                    statusEl.innerHTML = '<span class="badge bg-danger">OFFLINE</span>';
                }
            });
            const online = data.filter(c => c.online).length;
            toastr.info(`${online}/${data.length} CCTV Online`);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Ping Semua';
        });
}
</script>
@endpush