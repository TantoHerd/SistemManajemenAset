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
        <a href="{{ request()->routeIs('admin.assets.create') }}" class="btn btn-success">
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

                {{-- <div class="row mt-3">
                    <div class="col-12">
                        <a href="{{ url('/admin/assets?reset=1') }}" class="btn btn-secondary" onclick="localStorage.clear(); sessionStorage.clear();">
                            <i class="bi bi-arrow-repeat"></i> Reset Filter
                        </a>
                    </div>
                </div> --}}
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
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Assign</th>
                        <th>Status</th>
                        {{-- <th class="text-end">Nilai</th> --}}
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
                                        <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Yakin ingin menghapus aset ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
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

<!-- Batch Actions -->
<div id="batchActions" class="position-fixed bottom-0 end-0 m-3" style="display: none; z-index: 1050;">
    <div class="card shadow-lg border-0">
        <div class="card-body py-2 px-3">
            <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold">
                    <i class="bi bi-check-circle-fill text-primary"></i>
                    <span id="selectedCount">0</span> aset dipilih
                </span>
                <button type="button" class="btn btn-sm btn-primary" id="batchPrintBtn">
                    <i class="bi bi-printer"></i> Cetak Label
                </button>
                <button type="button" class="btn btn-sm btn-secondary" id="clearSelection">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scan QR Code Modal -->
<div class="modal fade" id="scanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-upc-scan"></i> Scan QR Code Aset
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-reader" style="width: 100%; max-width: 400px; margin: 0 auto;"></div>
                <div id="qr-reader-results" class="mt-3"></div>
                <div id="scan-result" class="mt-2"></div>
                <p class="text-muted mt-2 small">
                    <i class="bi bi-camera"></i> Arahkan kamera ke QR Code aset
                </p>
                <hr>
                <div class="mt-2">
                    <label class="form-label small">Atau masukkan kode manual:</label>
                    <div class="input-group">
                        <input type="text" id="manual-qrcode" class="form-control" placeholder="Kode Aset">
                        <button class="btn btn-primary" id="manualCheckBtn">
                            <i class="bi bi-search"></i> Cek
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal - Mobile Friendly -->
<div class="modal fade" id="resultModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 20px; margin: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-info-circle-fill text-primary"></i> Detail Aset
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0" id="resultModalBody" style="max-height: 70vh; overflow-y: auto;">
                <!-- Content akan diisi JavaScript -->
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary w-100 py-2 rounded-pill" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
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
$(document).ready(function() {
    // Select All
    $('#selectAll').on('change', function() {
        $('.asset-checkbox').prop('checked', this.checked);
        updateBatchActions();
    });
    
    $('.asset-checkbox').on('change', function() {
        updateBatchActions();
    });
    
    function updateBatchActions() {
        let count = $('.asset-checkbox:checked').length;
        $('#selectedCount').text(count);
        
        if (count > 0) {
            $('#batchActions').fadeIn();
        } else {
            $('#batchActions').fadeOut();
        }
    }
    
    // Batch Print
    $('#batchPrintBtn').on('click', function() {
        let selectedIds = [];
        $('.asset-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            alert('Pilih minimal satu aset');
            return;
        }
        
        let form = $('<form action="{{ route("admin.assets.print-labels") }}" method="POST" target="_blank"></form>');
        form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
        
        selectedIds.forEach(function(id) {
            form.append('<input type="hidden" name="asset_ids[]" value="' + id + '">');
        });
        
        $('body').append(form);
        form.submit();
        form.remove();
    });
    
    $('#clearSelection').on('click', function() {
        $('.asset-checkbox').prop('checked', false);
        $('#selectAll').prop('checked', false);
        updateBatchActions();
    });
    
    // Scanner
    let html5QrCode = null;
    
    // Start scanner when modal opens
    $('#scanModal').on('shown.bs.modal', function() {
        startScanner();
    });
    
    // Stop scanner when modal closes
    $('#scanModal').on('hidden.bs.modal', function() {
        stopScanner();
    });
    
    function startScanner() {
        if (html5QrCode) {
            stopScanner();
        }
        
        html5QrCode = new Html5Qrcode("qr-reader");
        
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };
        
        html5QrCode.start(
            { facingMode: "environment" }, // kamera belakang
            config,
            (decodedText, decodedResult) => {
                // Success callback
                console.log("QR Code scanned:", decodedText);
                $('#scan-result').html('<div class="alert alert-success">QR Code terdeteksi: ' + decodedText + '</div>');
                stopScanner();
                $('#scanModal').modal('hide');
                checkQRCode(decodedText);
            },
            (errorMessage) => {
                // Error callback (ignore, just for debugging)
                // console.log(errorMessage);
            }
        ).catch((err) => {
            console.error("Gagal start scanner:", err);
            $('#scan-result').html('<div class="alert alert-danger">Gagal mengakses kamera: ' + err + '</div>');
        });
    }
    
    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode = null;
            }).catch((err) => {
                console.error("Gagal stop scanner:", err);
            });
        }
    }
    
    function checkQRCode(qrCode) {
        $.ajax({
            url: '{{ route("admin.assets.scan") }}',
            method: 'POST',
            data: { barcode: qrCode },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAssetDetail(response.asset);
                } else {
                    $('#resultModalBody').html('<div class="alert alert-warning">' + response.message + '</div>');
                    $('#resultModal').modal('show');
                }
            },
            error: function(xhr) {
                $('#resultModalBody').html('<div class="alert alert-danger">Terjadi kesalahan: Aset tidak ditemukan</div>');
                $('#resultModal').modal('show');
            }
        });
    }
    
    function showAssetDetail(asset) {
        let checkBtn = '';
        if (asset.status === 'available') {
            checkBtn = '<button class="btn btn-success w-100 py-2 rounded-pill mt-2" onclick="toggleCheckInOut(' + asset.id + ')"><i class="bi bi-box-arrow-right"></i> Checkout Aset</button>';
        } else if (asset.status === 'in_use') {
            checkBtn = '<button class="btn btn-warning w-100 py-2 rounded-pill mt-2" onclick="toggleCheckInOut(' + asset.id + ')"><i class="bi bi-box-arrow-in-left"></i> Checkin Aset</button>';
        }
        
        $('#resultModalBody').html(`
            <div class="text-center mb-3">
                <span class="badge bg-${asset.status_badge_class} px-3 py-2 rounded-pill fs-6">${asset.status_label}</span>
            </div>
            
            <div class="card bg-light border-0 rounded-3 mb-2">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-5 text-muted small">Kode Aset</div>
                        <div class="col-7 fw-semibold">${asset.asset_code}</div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light border-0 rounded-3 mb-2">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-5 text-muted small">Nama Aset</div>
                        <div class="col-7 fw-semibold">${asset.name}</div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light border-0 rounded-3 mb-2">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-5 text-muted small">Serial Number</div>
                        <div class="col-7">${asset.serial_number || '-'}</div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light border-0 rounded-3 mb-2">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-5 text-muted small">Lokasi</div>
                        <div class="col-7">${asset.location?.full_path || asset.location?.name || '-'}</div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light border-0 rounded-3 mb-2">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-5 text-muted small">Nilai Beli</div>
                        <div class="col-7">${asset.formatted_purchase_price}</div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light border-0 rounded-3 mb-2">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-5 text-muted small">Nilai Saat Ini</div>
                        <div class="col-7 fw-bold text-primary">${asset.formatted_current_value}</div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mt-3">
                <a href="/admin/assets/${asset.id}" class="btn btn-info flex-grow-1 py-2 rounded-pill">
                    <i class="bi bi-eye"></i> Detail
                </a>
                ${checkBtn}
            </div>
        `);
        $('#resultModal').modal('show');
    }
    
    $('#manualCheckBtn').on('click', function() {
        let qrCode = $('#manual-qrcode').val();
        if (qrCode) {
            $('#scanModal').modal('hide');
            checkQRCode(qrCode);
            $('#manual-qrcode').val('');
        } else {
            alert('Masukkan kode aset');
        }
    });
    
    window.toggleCheckInOut = function(assetId) {
        $.ajax({
            url: '/admin/assets/' + assetId + '/toggle-checkinout',
            method: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $('#resultModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan');
            }
        });
    };
    
    // Pastikan modal ditutup dengan benar
    $('#scanModal').on('hidden.bs.modal', function() {
        stopScanner();
        $('#scan-result').html('');
    });
    
    $('.checkinout-btn').on('click', function() {
        let assetId = $(this).data('id');
        let status = $(this).data('status');
        
        if (status === 'available') {
            if (confirm('Checkout aset ini?')) {
                toggleCheckInOut(assetId);
            }
        } else if (status === 'in_use') {
            if (confirm('Checkin aset ini?')) {
                toggleCheckInOut(assetId);
            }
        } else {
            alert('Aset tidak dapat di-checkin/out');
        }
    });
    
    // Export Excel
    $('#exportExcelBtn').on('click', function(e) {
        e.preventDefault();
        let params = new URLSearchParams(window.location.search);
        let url = '{{ route("admin.assets.export") }}?' + params.toString();
        window.location.href = url;
    });

    $('#resetFilterBtn').on('click', function() {
        // Hapus session dengan memanggil URL
        window.location.href = '{{ route("admin.assets.reset-filter") }}';
    });
});
</script>
@endpush