@extends('admin.layouts.app')

@section('title', 'Daftar Aset')
@section('page-title', 'Daftar Aset IT')

@section('breadcrumb')
    <li class="breadcrumb-item active">Aset</li>
@endsection

@section('header-actions')
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scanModal">
            <i class="bi bi-upc-scan"></i> Scan QR Code
        </button>
        <a href="{{ route('admin.assets.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Tambah Aset
        </a>
        <a href="{{ route('admin.assets.import') }}" class="btn btn-warning">
            <i class="bi bi-upload"></i> Import
        </a>
        <div class="dropdown">
            <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-download"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.assets.export') }}">
                        <i class="bi bi-file-earmark-excel text-success"></i> Export ke Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.amortization.export') }}">
                        <i class="bi bi-graph-down"></i> Export Amortisasi
                    </a>
                </li>
            </ul>
        </div>
    </div>
@endsection

@section('content')
<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="bi bi-funnel"></i> Filter & Pencarian
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.assets.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ ($categoryFilter ?? request('category')) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold">Lokasi (Induk)</label>
                    <select name="location" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ ($locationFilter ?? request('location')) == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                    {{-- <small class="text-muted">Menampilkan semua aset di lokasi ini & sub-lokasinya</small> --}}
                </div>
                
                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $key => $value)
                            <option value="{{ $key }}" {{ ($statusFilter ?? request('status')) == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold">Assign</label>
                    <select name="assigned_to" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ ($assignedToFilter ?? request('assigned_to')) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}
                
                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold">Tampilkan</label>
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="10" {{ ($perPage ?? request('per_page', 15)) == 10 ? 'selected' : '' }}>10 Baris</option>
                        <option value="15" {{ ($perPage ?? request('per_page', 15)) == 15 ? 'selected' : '' }}>15 Baris</option>
                        <option value="25" {{ ($perPage ?? request('per_page', 15)) == 25 ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ ($perPage ?? request('per_page', 15)) == 50 ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ ($perPage ?? request('per_page', 15)) == 100 ? 'selected' : '' }}>100 Baris</option>
                    </select>
                </div>
                
                <div class="col-md-2 col-sm-6">
                    <label class="form-label fw-semibold">Pencarian</label>
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari kode/nama/serial..." 
                               value="{{ $searchFilter ?? request('search') }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6 d-flex align-items-end">
                    <a href="{{ route('admin.assets.reset-filter') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Assets Table Card -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="40" class="text-center">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th>
                            <x-sortable-header column="asset_code" label="Kode Aset" :current="$sort ?? null" :direction="$direction ?? null" />
                        </th>
                        <th>
                            <x-sortable-header column="name" label="Nama Aset" :current="$sort ?? null" :direction="$direction ?? null" />
                        </th>
                         <th>
                            <x-sortable-header column="category_name" label="Kategori" :current="$sort ?? null" :direction="$direction ?? null" />
                        </th>
                        <th>
                            <x-sortable-header column="location_name" label="Lokasi" :current="$sort ?? null" :direction="$direction ?? null" />
                        </th>
                        <th>Assign</th>
                        <th>
                            <x-sortable-header column="status" label="Status" :current="$sort ?? null" :direction="$direction ?? null" />
                        </th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $asset)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input asset-checkbox" value="{{ $asset->id }}">
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $asset->asset_code }}</span>
                            <br>
                            <small class="text-muted">SN: {{ $asset->serial_number ?? '-' }}</small>
                        </td>
                        <td>{{ $asset->name }}</td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                {{ $asset->category->name ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <i class="bi bi-geo-alt-fill text-muted me-1"></i>
                            {{ $asset->location->name ?? '-' }}
                        </td>
                        <td>
                            @if($asset->assignedTo)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm">
                                        @if($asset->assignedTo->avatar)
                                            <img src="{{ asset('storage/' . $asset->assignedTo->avatar) }}" class="rounded-circle" width="30" height="30">
                                        @else
                                            <div class="avatar-initial rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                {{ strtoupper(substr($asset->assignedTo->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <strong>{{ $asset->assignedTo->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $asset->assignedTo->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">
                                    <i class="bi bi-person"></i> Belum diassign
                                </span>
                            @endif
                        </td>
                        <td>
                            @php
                                $badgeClass = [
                                    'available' => 'success',
                                    'in_use' => 'primary',
                                    'maintenance' => 'warning',
                                    'damaged' => 'danger',
                                    'disposed' => 'secondary'
                                ][$asset->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }} px-3 py-2 rounded-pill">
                                {{ $asset->status_label }}
                            </span>
                        </td>
                        {{-- <td class="text-end">
                            <span class="fw-semibold">{{ $asset->formatted_current_value }}</span>
                        </td> --}}
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.assets.show', $asset) }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.assets.edit', $asset) }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.assets.print-label', $asset) }}" target="_blank">
                                            <i class="bi bi-upc-scan"></i> Cetak Label
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item checkinout-btn" 
                                                data-id="{{ $asset->id }}" 
                                                data-status="{{ $asset->status }}">
                                            <i class="bi bi-arrow-left-right"></i>
                                            {{ $asset->status === 'available' ? 'Checkout' : ($asset->status === 'in_use' ? 'Checkin' : 'Tidak Tersedia') }}
                                        </button>
                                    </li>
                                    <li>
                                        <button onclick="confirmDelete('{{ route('admin.assets.destroy', $asset) }}', 'Aset {{ $asset->name }} akan dihapus!')" class="dropdown-item text-danger">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada data aset</p>
                            <a href="{{ route('admin.assets.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus-lg"></i> Tambah Aset Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <small class="text-muted">
                        Menampilkan {{ $assets->firstItem() ?? 0 }} - {{ $assets->lastItem() ?? 0 }} 
                        dari {{ $assets->total() }} data
                    </small>
                </div>
                <div>
                    {{ $assets->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Batch Actions - Floating -->
<div id="batchActions" class="position-fixed bottom-0 end-0 m-3" style="display: none; z-index: 1050;">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body py-2 px-4">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="fw-semibold">
                    <i class="bi bi-check-circle-fill text-primary"></i>
                    <span id="selectedCount">0</span> aset dipilih
                </span>
                
                <select id="batchStatus" class="form-select form-select-sm" style="width: 130px;" onchange="batchAction('status', this.value)">
                    <option value="">Ubah Status...</option>
                    @foreach($statuses as $key => $val)<option value="{{ $key }}">{{ $val }}</option>@endforeach
                </select>
                
                <select id="batchCategory" class="form-select form-select-sm" style="width: 140px;" onchange="batchAction('category', this.value)">
                    <option value="">Ubah Kategori...</option>
                    @foreach($categories as $cat)<option value="{{ $cat->id }}">{{ $cat->name }}</option>@endforeach
                </select>
                
                <select id="batchLocation" class="form-select form-select-sm" style="width: 140px;" onchange="batchAction('location', this.value)">
                    <option value="">Ubah Lokasi...</option>
                    @foreach($locations as $loc)<option value="{{ $loc->id }}">{{ $loc->name }}</option>@endforeach
                </select>
                
                <button type="button" class="btn btn-sm btn-primary" onclick="batchPrint()">
                    <i class="bi bi-printer"></i> Cetak
                </button>
                
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmBulkDelete()">
                    <i class="bi bi-trash"></i> Hapus
                </button>
                
                <button type="button" class="btn btn-sm btn-link text-decoration-none" onclick="clearSelection()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@include('admin.assets._scan-modal')

@include('admin.assets._result-modal')

@endsection

@push('styles')
<style>
    .avatar-sm {
        width: 30px;
        height: 30px;
    }
    .btn-group .dropdown-toggle::after {
        display: none;
    }
    .table td {
        vertical-align: middle;
    }
    .bg-success-subtle { background-color: #d1e7dd; }
    .bg-primary-subtle { background-color: #cfe2ff; }
    .bg-warning-subtle { background-color: #fff3cd; }
    .bg-danger-subtle { background-color: #f8d7da; }
    .bg-secondary-subtle { background-color: #e9ecef; }
    .text-success { color: #0f5132 !important; }
    .text-primary { color: #084298 !important; }
    .text-warning { color: #664d03 !important; }
    .text-danger { color: #842029 !important; }
    .text-secondary { color: #41464b !important; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// ============================================
// BATCH ACTIONS (Vanilla JS)
// ============================================
function updateBatchActions() {
    const checked = document.querySelectorAll('.asset-checkbox:checked');
    const count = checked.length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('batchActions').style.display = count > 0 ? 'block' : 'none';
}

document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.asset-checkbox').forEach(cb => cb.checked = this.checked);
    updateBatchActions();
});

document.querySelectorAll('.asset-checkbox').forEach(cb => {
    cb.addEventListener('change', updateBatchActions);
});

function clearSelection() {
    document.querySelectorAll('.asset-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll') && (document.getElementById('selectAll').checked = false);
    document.getElementById('batchStatus') && (document.getElementById('batchStatus').value = '');
    document.getElementById('batchCategory') && (document.getElementById('batchCategory').value = '');
    document.getElementById('batchLocation') && (document.getElementById('batchLocation').value = '');
    updateBatchActions();
}

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.asset-checkbox:checked')).map(c => c.value).join(',');
}

function batchAction(type, value) {
    if (!value) return;
    const count = document.querySelectorAll('.asset-checkbox:checked').length;
    
    // SweetAlert konfirmasi
    const labels = {
        'status': 'Ubah Status',
        'category': 'Ubah Kategori', 
        'location': 'Ubah Lokasi'
    };
    
    Swal.fire({
        title: labels[type] + '?',
        text: 'Yakin ingin mengubah ' + count + ' aset?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4361ee',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = getSelectedIds();
            let url = '';
            if (type === 'status') url = '{{ route("admin.assets.bulk-status") }}';
            else if (type === 'category') url = '{{ route("admin.assets.bulk-category") }}';
            else if (type === 'location') url = '{{ route("admin.assets.bulk-location") }}';
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="ids" value="' + ids + '">';
            if (type === 'status') form.innerHTML += '<input type="hidden" name="status" value="' + value + '">';
            else if (type === 'category') form.innerHTML += '<input type="hidden" name="category_id" value="' + value + '">';
            else if (type === 'location') form.innerHTML += '<input type="hidden" name="location_id" value="' + value + '">';
            document.body.appendChild(form);
            form.submit();
        } else {
            // Reset dropdown kalau batal
            document.getElementById('batch' + type.charAt(0).toUpperCase() + type.slice(1)).value = '';
        }
    });
}

function batchPrint() {
    const ids = Array.from(document.querySelectorAll('.asset-checkbox:checked')).map(c => c.value);
    if (ids.length === 0) { 
        Swal.fire('Oops!', 'Pilih aset dulu!', 'warning');
        return; 
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.assets.print-labels") }}';
    form.target = '_blank';
    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
    ids.forEach(id => { form.innerHTML += '<input type="hidden" name="asset_ids[]" value="' + id + '">'; });
    document.body.appendChild(form);
    form.submit();
    form.remove();
}

function confirmBulkDelete() {
    const count = document.querySelectorAll('.asset-checkbox:checked').length;
    if (count === 0) return;
    
    // SweetAlert
    Swal.fire({
        title: 'Hapus ' + count + ' Aset?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const ids = getSelectedIds();
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.assets.bulk-delete") }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="ids" value="' + ids + '">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Init
document.addEventListener('DOMContentLoaded', function() {
    updateBatchActions();
});

// ============================================
// SCANNER
// ============================================
let html5QrCode = null;

document.getElementById('scanModal')?.addEventListener('shown.bs.modal', function() {
    startScanner();
});

document.getElementById('scanModal')?.addEventListener('hidden.bs.modal', function() {
    stopScanner();
    document.getElementById('scan-result').innerHTML = '';
});

function startScanner() {
    if (html5QrCode) stopScanner();
    html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => {
            document.getElementById('scan-result').innerHTML = '<div class="alert alert-success">QR terdeteksi: ' + decodedText + '</div>';
            stopScanner();
            bootstrap.Modal.getInstance(document.getElementById('scanModal')).hide();
            checkQRCode(decodedText);
        },
        () => {}
    ).catch(err => {
        document.getElementById('scan-result').innerHTML = '<div class="alert alert-danger">Gagal akses kamera</div>';
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => html5QrCode = null).catch(() => {});
    }
}

function checkQRCode(qrCode) {
    console.log('Checking QR:', qrCode); // Debug
    
    fetch('/admin/assets/scan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ barcode: qrCode })
    })
    .then(r => r.json())
    .then(response => {
        console.log('Response:', response); // Debug
        if (response.success) {
            showAssetDetail(response.asset);
        } else {
            alert(response.message || 'Aset tidak ditemukan');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Gagal menghubungi server. Cek koneksi.');
    });
}

function showAssetDetail(asset) {
    console.log('Showing asset:', asset); // Debug
    
    let checkBtn = '';
    if (asset.status === 'available') {
        checkBtn = '<button class="btn btn-success w-100 py-2 rounded-pill mt-2" onclick="toggleCheckInOut(' + asset.id + ')"><i class="bi bi-box-arrow-right"></i> Checkout</button>';
    } else if (asset.status === 'in_use') {
        checkBtn = '<button class="btn btn-warning w-100 py-2 rounded-pill mt-2" onclick="toggleCheckInOut(' + asset.id + ')"><i class="bi bi-box-arrow-in-left"></i> Checkin</button>';
    }
    
    document.getElementById('resultModalBody').innerHTML = `
        <div class="text-center mb-3">
            <span class="badge bg-${asset.status_badge_class} px-3 py-2 rounded-pill fs-6">${asset.status_label}</span>
        </div>
        <div class="card bg-light border-0 rounded-3 mb-2">
            <div class="card-body py-2"><div class="row"><div class="col-5 text-muted small">Kode</div><div class="col-7 fw-semibold">${asset.asset_code}</div></div></div>
        </div>
        <div class="card bg-light border-0 rounded-3 mb-2">
            <div class="card-body py-2"><div class="row"><div class="col-5 text-muted small">Nama</div><div class="col-7 fw-semibold">${asset.name}</div></div></div>
        </div>
        <div class="card bg-light border-0 rounded-3 mb-2">
            <div class="card-body py-2"><div class="row"><div class="col-5 text-muted small">Serial</div><div class="col-7">${asset.serial_number || '-'}</div></div></div>
        </div>
        <div class="card bg-light border-0 rounded-3 mb-2">
            <div class="card-body py-2"><div class="row"><div class="col-5 text-muted small">Lokasi</div><div class="col-7">${asset.location?.name || '-'}</div></div></div>
        </div>
        <div class="d-flex gap-2 mt-3">
            <a href="/admin/assets/${asset.id}" class="btn btn-info flex-grow-1 py-2 rounded-pill"><i class="bi bi-eye"></i> Detail</a>
            ${checkBtn}
        </div>`;
    
    // Tampilkan modal
    var resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
    resultModal.show();
}

document.getElementById('manualCheckBtn')?.addEventListener('click', function() {
    const code = document.getElementById('manual-qrcode').value;
    if (code) {
        bootstrap.Modal.getInstance(document.getElementById('scanModal')).hide();
        checkQRCode(code);
        document.getElementById('manual-qrcode').value = '';
    }
});

window.toggleCheckInOut = function(assetId) {
    fetch('/admin/assets/' + assetId + '/toggle-checkinout', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(response => {
        if (response.success) {
            bootstrap.Modal.getInstance(document.getElementById('resultModal')).hide();
            location.reload();
        }
    });
};
</script>
@endpush