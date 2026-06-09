@extends('admin.layouts.app')

@section('title', 'Stock Opname - Scan Aset')
@section('page-title', 'Stock Opname: ' . $stockOpname->name)

@section('header-actions')
    <a href="{{ route('admin.stock-opname.show', $stockOpname) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<style>
    .scan-card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        border: none;
    }
    .scan-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 25px 30px;
    }
    .progress-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #eef2f6;
    }
    .input-scan {
        border-radius: 50px !important;
        border: 2px solid #eef2f6;
        padding: 15px 25px;
        font-size: 18px;
        transition: all 0.3s;
    }
    .input-scan:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    .result-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 20px;
        border: 1px solid #eef2f6;
        margin-top: 25px;
    }
    .status-btn {
        border-radius: 12px;
        padding: 15px 10px;
        transition: all 0.2s;
        font-weight: 600;
        border: 2px solid transparent;
    }
    .status-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .status-btn-found {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    .status-btn-missing {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    .status-btn-damaged {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }
    .status-btn-moved {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #eef2f6;
    }
    .info-label {
        width: 120px;
        font-weight: 600;
        color: #6b7280;
    }
    .info-value {
        flex: 1;
        color: #1f2937;
        font-weight: 500;
    }
    .badge-condition {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-good {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-warning {
        background: #fed7aa;
        color: #92400e;
    }
    .progress-bar-custom {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        height: 12px;
    }
    .toast-custom {
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        min-width: 300px;
        background: white;
    }
    .toast-custom.toast-success {
        border-left: 4px solid #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
    }
    .toast-custom.toast-success .toast-body {
        color: #065f46;
    }
    .toast-custom.toast-error {
        border-left: 4px solid #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
    }
    .toast-custom.toast-error .toast-body {
        color: #991b1b;
    }
    .toast-custom.toast-warning {
        border-left: 4px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #ffffff 100%);
    }
    .toast-custom.toast-warning .toast-body {
        color: #92400e;
    }
    .toast-custom .toast-body {
        padding: 12px 16px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .toast-custom .toast-body i {
        font-size: 20px;
    }
    .btn-outline-secondary:hover {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border: none;
        color: white;
    }
</style>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <!-- Progress Card -->
        <div class="progress-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="text-muted small text-uppercase tracking-wide">Progress Stock Opname</span>
                    <h4 class="mb-0 mt-1">
                        <span id="scannedCount">{{ $scanned ?? 0 }}</span> 
                        <small class="text-muted fs-6">dari {{ $total ?? 0 }}</small>
                    </h4>
                </div>
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                        <i class="bi bi-hourglass-split me-1"></i> 
                        <span id="remainingTotal">{{ ($total ?? 0) - ($scanned ?? 0) }}</span> tersisa
                    </span>
                </div>
            </div>
            
            <div class="progress mb-3" style="height: 12px; border-radius: 10px; background: #eef2f6;">
                @php
                    $progressPercent = ($total ?? 0) > 0 ? round((($scanned ?? 0) / ($total ?? 0)) * 100) : 0;
                @endphp
                <div class="progress-bar progress-bar-custom" style="width: {{ $progressPercent }}%">
                    {{ $progressPercent }}%
                </div>
            </div>
            
            <div class="row text-center">
                <div class="col-6">
                    <div class="p-2 bg-light rounded">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                        <span class="fw-bold" id="scannedTotal">{{ $scanned ?? 0 }}</span>
                        <span class="text-muted">Terverifikasi</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 bg-light rounded">
                        <i class="bi bi-hourglass me-1 text-warning"></i>
                        <span class="fw-bold" id="pendingTotal">{{ ($total ?? 0) - ($scanned ?? 0) }}</span>
                        <span class="text-muted">Tertunda</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scan Card -->
        <div class="card scan-card">
            <div class="scan-header text-white">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-upc-scan fs-1"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold">Scan Barcode Aset</h4>
                        <p class="mb-0 opacity-75">Masukkan atau scan kode aset untuk verifikasi</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Input Scan -->
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bi bi-upc-scan text-primary fs-5"></i>
                        </span>
                        <input type="text" id="barcodeInput" class="form-control input-scan border-start-0 ps-0 text-center" 
                                placeholder="AST20260422-510AA7" autofocus>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">
                            <i class="bi bi-keyboard me-1"></i> Tekan <kbd>Enter</kbd> setelah memasukkan kode
                        </small>
                    </div>
                </div>
                
                <div id="loading" style="display: none;" class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 48px; height: 48px;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memproses data aset...</p>
                </div>
                
                <!-- Hasil Scan -->
                <div id="scanResult" style="display: none;">
                    <div class="result-card p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill text-success fs-3 me-2"></i>
                            <h5 class="mb-0 fw-bold">Hasil Scan Aset</h5>
                            <span class="ms-auto" id="statusBadge"></span>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Kode Aset</div>
                            <div class="info-value">
                                <code class="bg-light px-2 py-1 rounded" id="resultAssetCode"></code>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Nama Aset</div>
                            <div class="info-value fw-semibold" id="resultAssetName"></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Lokasi</div>
                            <div class="info-value">
                                <i class="bi bi-geo-alt text-muted me-1"></i>
                                <span id="resultAssetLocation"></span>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Kondisi</div>
                            <div class="info-value">
                                <span class="badge-condition badge-good" id="resultAssetCondition">Baik</span>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="mb-3">
                            <label class="fw-bold mb-2 d-block">Status Verifikasi</label>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <button class="btn status-btn status-btn-found w-100" onclick="submitStatus('found')">
                                        <i class="bi bi-check-circle fs-4 d-block mb-1"></i>
                                        Ditemukan
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn status-btn status-btn-missing w-100" onclick="submitStatus('missing')">
                                        <i class="bi bi-question-circle fs-4 d-block mb-1"></i>
                                        Hilang
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn status-btn status-btn-damaged w-100" onclick="showSimpleNote('damaged')">
                                        <i class="bi bi-exclamation-triangle fs-4 d-block mb-1"></i>
                                        Rusak
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn status-btn status-btn-moved w-100" onclick="showMovedForm()">
                                        <i class="bi bi-arrow-repeat fs-4 d-block mb-1"></i>
                                        Berpindah
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="movedForm" style="display: none;" class="mt-4 p-3 bg-light rounded-3">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt me-1"></i> Lokasi Baru
                                </label>
                                <input type="text" id="newLocation" class="form-control" placeholder="Contoh: Ruang Server Lt.2">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-chat me-1"></i> Catatan
                                </label>
                                <textarea id="scanNote" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="submitStatus('moved')">
                                    <i class="bi bi-save"></i> Simpan
                                </button>
                                <button class="btn btn-light" onclick="hideMovedForm()">
                                    Batal
                                </button>
                            </div>
                        </div>
                        
                        <div id="simpleNote" style="display: none;" class="mt-4 p-3 bg-light rounded-3">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-chat me-1"></i> Catatan
                                </label>
                                <textarea id="simpleScanNote" class="form-control" rows="2" placeholder="Catatan kerusakan..."></textarea>
                            </div>
                            <button class="btn btn-primary w-100" onclick="submitWithNote()">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
    <div id="liveToast" class="toast toast-custom" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                Pesan
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
let currentItemId = null;
let currentAssetName = null;
let currentExtraStatus = null;
let totalItems = {{ $total ?? 0 }};
let scannedItems = {{ $scanned ?? 0 }};

// Function update progress display
function updateProgressDisplay(scanned, total) {
    let remaining = total - scanned;
    let progressPercent = total > 0 ? Math.round((scanned / total) * 100) : 0;
    
    // Update header progress (hanya angka scanned)
    document.getElementById('scannedCount').innerText = scanned;
    
    // Update statistik
    document.getElementById('scannedTotal').innerText = scanned;
    document.getElementById('pendingTotal').innerText = remaining;
    document.getElementById('remainingTotal').innerText = remaining;
    
    // Update progress bar
    let progressBar = document.querySelector('.progress-bar-custom');
    if (progressBar) {
        progressBar.style.width = progressPercent + '%';
        progressBar.innerText = progressPercent + '%';
    }
}

// Toast function
function showToast(message, type = 'success') {
    let toast = document.getElementById('liveToast');
    let toastBody = document.getElementById('toastMessage');
    
    toastBody.innerText = message;
    
    let toastHeader = document.querySelector('.toast-custom');
    if (type === 'success') {
        toastHeader.style.borderLeftColor = '#10b981';
    } else if (type === 'error') {
        toastHeader.style.borderLeftColor = '#ef4444';
    } else if (type === 'warning') {
        toastHeader.style.borderLeftColor = '#f59e0b';
    }
    
    let bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

// Scan barcode
document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        let barcode = this.value.trim();
        if (barcode) {
            scanAsset(barcode);
        }
    }
});

function scanAsset(barcode) {
    document.getElementById('loading').style.display = 'block';
    document.getElementById('scanResult').style.display = 'none';
    document.getElementById('movedForm').style.display = 'none';
    document.getElementById('simpleNote').style.display = 'none';
    
    let url = '{{ route("admin.stock-opname.scan-asset", $stockOpname) }}';
    let fullUrl = url + '?barcode=' + encodeURIComponent(barcode);
    
    fetch(fullUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';
        
        if (data.success) {
            currentItemId = data.itemId;
            currentAssetName = data.asset.name;
            document.getElementById('resultAssetCode').innerText = data.asset.asset_code;
            document.getElementById('resultAssetName').innerText = data.asset.name;
            document.getElementById('resultAssetLocation').innerText = data.asset.location_name;
            
            let conditionBadge = document.getElementById('resultAssetCondition');
            if (data.asset.condition === 'Baik' || data.asset.condition === 'good') {
                conditionBadge.className = 'badge-condition badge-good';
                conditionBadge.innerText = 'Baik';
            } else {
                conditionBadge.className = 'badge-condition badge-warning';
                conditionBadge.innerText = data.asset.condition;
            }
            
            document.getElementById('scanResult').style.display = 'block';
            document.getElementById('barcodeInput').value = '';
            document.getElementById('newLocation').value = '';
            document.getElementById('scanNote').value = '';
            document.getElementById('simpleScanNote').value = '';
        } else {
            showToast(data.message, 'error');
            document.getElementById('barcodeInput').value = '';
            document.getElementById('barcodeInput').focus();
        }
    })
    .catch(error => {
        document.getElementById('loading').style.display = 'none';
        showToast('Terjadi kesalahan: ' + error.message, 'error');
        document.getElementById('barcodeInput').value = '';
        document.getElementById('barcodeInput').focus();
    });
}

function showMovedForm() {
    document.getElementById('movedForm').style.display = 'block';
    document.getElementById('simpleNote').style.display = 'none';
    currentExtraStatus = 'moved';
}

function hideMovedForm() {
    document.getElementById('movedForm').style.display = 'none';
    document.getElementById('newLocation').value = '';
}

function showSimpleNote(status) {
    currentExtraStatus = status;
    document.getElementById('simpleNote').style.display = 'block';
    document.getElementById('movedForm').style.display = 'none';
}

function submitWithNote() {
    let notes = document.getElementById('simpleScanNote').value;
    submitStatus(currentExtraStatus, '', notes);
}

function submitStatus(status, actualLocation = '', notes = '') {
    if (status === 'moved' && !actualLocation) {
        actualLocation = document.getElementById('newLocation').value;
        notes = document.getElementById('scanNote').value;
    }
    
    if (status === 'moved' && !actualLocation) {
        showToast('Lokasi baru harus diisi', 'warning');
        return;
    }
    
    if (!currentItemId) {
        showToast('Silakan scan aset terlebih dahulu', 'error');
        return;
    }
    
    document.getElementById('loading').style.display = 'block';
    
    let url = '{{ route("admin.stock-opname.process-scan", ["stockOpname" => $stockOpname, "item" => "__ITEM_ID__"]) }}';
    url = url.replace('__ITEM_ID__', currentItemId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            actual_status: status,
            actual_location: actualLocation,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading').style.display = 'none';
        
        if (data.success) {
            scannedItems++;
            
            // Panggil function update progress
            updateProgressDisplay(scannedItems, totalItems);
            
            document.getElementById('scanResult').style.display = 'none';
            document.getElementById('movedForm').style.display = 'none';
            document.getElementById('simpleNote').style.display = 'none';
            
            let statusText = {
                'found': 'Ditemukan',
                'missing': 'Hilang',
                'damaged': 'Rusak',
                'moved': 'Berpindah'
            };
            
            showToast('✓ Aset "' + currentAssetName + '" - ' + statusText[status], 'success');
            
            if (data.completed) {
                showToast('🎉 Stock Opname Selesai! Mengalihkan ke laporan...', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.stock-opname.report", $stockOpname) }}';
                }, 2000);
            } else {
                document.getElementById('barcodeInput').focus();
            }
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        document.getElementById('loading').style.display = 'none';
        showToast('Terjadi kesalahan: ' + error.message, 'error');
    });
}

document.getElementById('barcodeInput').focus();
</script>
@endsection