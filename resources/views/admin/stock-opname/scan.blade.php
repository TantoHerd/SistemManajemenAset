@extends('admin.layouts.app')

@section('title', 'Scan Aset - Stock Opname')
@section('page-title', 'Scan Aset: ' . $stockOpname->name)

@section('header-actions')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#scanModal">
        <i class="bi bi-upc-scan"></i> Scan QR Code
    </button>
    <a href="{{ route('admin.stock-opname.show', $stockOpname) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<style>
    .progress-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #eef2f6;
    }
    .progress-bar-custom {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        height: 12px;
    }
    .result-card {
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 20px;
        border: 1px solid #eef2f6;
        margin-top: 25px;
    }
    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #eef2f6;
    }
    .info-label {
        width: 100px;
        font-weight: 600;
        color: #6b7280;
    }
    .info-value {
        flex: 1;
        color: #1f2937;
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
    .status-btn {
        border-radius: 12px;
        padding: 12px 10px;
        transition: all 0.2s;
        font-weight: 600;
        border: none;
        color: white;
    }
    .status-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .status-btn-found { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .status-btn-missing { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .status-btn-damaged { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .status-btn-moved { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .scan-result-flash {
        animation: flashSuccess 0.5s;
    }
    @keyframes flashSuccess {
        0% { background: rgba(16, 185, 129, 0.3); }
        100% { background: transparent; }
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
        <div class="card">
            <div class="card-body">
                <!-- Input Scan -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Masukkan Kode Aset</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0">
                            <i class="bi bi-upc-scan text-primary fs-5"></i>
                        </span>
                        <input type="text" id="barcodeInput" class="form-control border-start-0 text-center" 
                               placeholder="AST20260422-510AA7" autofocus>
                        <button class="btn btn-primary" type="button" id="manualScanBtn">
                            <i class="bi bi-search"></i> Cek
                        </button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#scanModal">
                            <i class="bi bi-upc-scan"></i> Scan
                        </button>
                    </div>
                    <small class="text-muted">Tekan <kbd>Enter</kbd> atau klik <kbd>Cek</kbd></small>
                </div>
                
                <div id="loading" style="display: none;" class="text-center py-5">
                    <div class="spinner-border text-primary" style="width: 48px; height: 48px;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memproses data aset...</p>
                </div>
                
                <!-- Hasil Scan -->
                <div id="scanResult" style="display: none;">
                    <div class="result-card p-4 scan-result-flash" id="scanResultCard">
                        <div class="d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill text-success fs-3 me-2"></i>
                            <h5 class="mb-0 fw-bold">Hasil Scan Aset</h5>
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

<!-- ============ MODAL SCANNER (SAMA SEPERTI DAFTAR ASET) ============ -->
@include('admin.stock-opname._scan-modal')

<!-- ============ MODAL HASIL SCAN (SAMA SEPERTI DAFTAR ASET) ============ -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-info-circle text-primary me-2"></i>Detail Aset
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="resultModalBody">
                <!-- Dinamis dari JavaScript -->
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
    <div id="liveToast" class="toast toast-custom" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Pesan</div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentItemId = null;
let currentAssetName = null;
let currentExtraStatus = null;
let totalItems = {{ $total ?? 0 }};
let scannedItems = {{ $scanned ?? 0 }};
let html5QrCode = null;

// Toast function
function showToast(message, type = 'success') {
    let toast = document.getElementById('liveToast');
    let toastBody = document.getElementById('toastMessage');
    toastBody.innerText = message;
    
    let toastHeader = document.querySelector('.toast-custom');
    if (type === 'success') toastHeader.style.borderLeftColor = '#10b981';
    else if (type === 'error') toastHeader.style.borderLeftColor = '#ef4444';
    else if (type === 'warning') toastHeader.style.borderLeftColor = '#f59e0b';
    
    let bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

// ============ SCANNER MODAL (SAMA SEPERTI DAFTAR ASET) ============
document.getElementById('scanModal')?.addEventListener('shown.bs.modal', function() {
    startScanner();
});

document.getElementById('scanModal')?.addEventListener('hidden.bs.modal', function() {
    stopScanner();
});

function startScanner() {
    if (html5QrCode) stopScanner();
    html5QrCode = new Html5Qrcode("qr-reader");
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        (decodedText) => {
            stopScanner();
            bootstrap.Modal.getInstance(document.getElementById('scanModal')).hide();
            document.getElementById('barcodeInput').value = decodedText;
            scanAsset(decodedText);
        },
        () => {}
    ).catch(err => {
        showToast('Gagal akses kamera', 'error');
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => html5QrCode = null).catch(() => {});
    }
}

// ============ MANUAL SCAN ============
document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        let barcode = this.value.trim();
        if (barcode) scanAsset(barcode);
    }
});

document.getElementById('manualScanBtn').addEventListener('click', function() {
    let barcode = document.getElementById('barcodeInput').value.trim();
    if (barcode) scanAsset(barcode);
});

// ============ SCAN ASSET ============
function scanAsset(barcode) {
    if (!barcode) {
        showToast('Barcode kosong', 'warning');
        return;
    }
    
    document.getElementById('loading').style.display = 'block';
    document.getElementById('scanResult').style.display = 'none';
    document.getElementById('movedForm').style.display = 'none';
    document.getElementById('simpleNote').style.display = 'none';
    
    // Ambil ID sesi dari data atau hardcode
    let sessionId = {{ $stockOpname->id ?? 0 }};
    
    if (!sessionId) {
        showToast('ID sesi tidak ditemukan', 'error');
        document.getElementById('loading').style.display = 'none';
        return;
    }
    
    let url = '/admin/stock-opname/' + sessionId + '/scan-asset';
    let fullUrl = url + '?barcode=' + encodeURIComponent(barcode);
    
    console.log('Scan URL:', fullUrl); // Debug
    
    fetch(fullUrl, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
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
            showToast(data.message || 'Error', 'error');
            document.getElementById('barcodeInput').value = '';
            document.getElementById('barcodeInput').focus();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading').style.display = 'none';
        showToast('Error: ' + error.message, 'error');
        document.getElementById('barcodeInput').value = '';
        document.getElementById('barcodeInput').focus();
    });
}

// ============ STATUS FUNCTIONS ============
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
    
    // Ambil ID sesi
    let sessionId = {{ $stockOpname->id ?? 0 }};
    
    // Buat URL dengan ID yang benar
    let url = '/admin/stock-opname/' + sessionId + '/scan/' + currentItemId;
    
    console.log('Submit URL:', url);
    console.log('Data:', {
        actual_status: status,
        actual_location: actualLocation,
        notes: notes
    });
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            actual_status: status,
            actual_location: actualLocation,
            notes: notes
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('HTTP error ' + response.status + ': ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        document.getElementById('loading').style.display = 'none';
        
        if (data.success) {
            scannedItems++;
            let remaining = totalItems - scannedItems;
            let progressPercent = Math.round((scannedItems / totalItems) * 100);
            
            document.getElementById('scannedCount').innerHTML = scannedItems + ' <small class="text-muted fs-6">dari ' + totalItems + '</small>';
            document.getElementById('scannedTotal').innerText = scannedItems;
            document.getElementById('pendingTotal').innerText = remaining;
            
            let progressBar = document.querySelector('.progress-bar-custom');
            if (progressBar) {
                progressBar.style.width = progressPercent + '%';
                progressBar.innerText = progressPercent + '%';
            }
            
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
            showToast(data.message || 'Gagal menyimpan', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading').style.display = 'none';
        showToast('Error: ' + error.message, 'error');
    });
}

// Di dalam script section
document.getElementById('manualCheckBtn')?.addEventListener('click', function() {
    const code = document.getElementById('manual-qrcode').value;
    if (code) {
        bootstrap.Modal.getInstance(document.getElementById('scanModal')).hide();
        document.getElementById('barcodeInput').value = code;
        scanAsset(code);
        document.getElementById('manual-qrcode').value = '';
    }
});

// Enter key untuk manual input di modal
document.getElementById('manual-qrcode')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('manualCheckBtn').click();
    }
});

// ============ NOTIFICATION WITH SWEETALERT ============
function showToast(message, type = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: type,
        title: message
    });
}

function showAlert(title, message, type = 'success') {
    const icons = {
        'success': 'success',
        'error': 'error', 
        'warning': 'warning',
        'info': 'info'
    };
    
    const colors = {
        'success': '#10b981',
        'error': '#ef4444',
        'warning': '#f59e0b',
        'info': '#3b82f6'
    };
    
    Swal.fire({
        title: title,
        text: message,
        icon: icons[type] || 'info',
        confirmButtonColor: colors[type] || '#4361ee',
        confirmButtonText: 'OK',
        timer: type === 'success' ? 2000 : 4000,
        timerProgressBar: true,
        showConfirmButton: type !== 'success'
    });
}

// ============ CONFIRMATION DIALOG ============
function showConfirm(title, message, callback) {
    Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4361ee',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}

document.getElementById('barcodeInput').focus();
</script>
@endsection