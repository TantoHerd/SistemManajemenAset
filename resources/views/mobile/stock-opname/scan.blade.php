<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#667eea">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan Aset - {{ $session->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        body { background: #f5f5f5; font-family: -apple-system, BlinkMacSystemFont, sans-serif; }
        .mobile-container { max-width: 480px; margin: 0 auto; background: white; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
        .barcode-input { text-align: center; font-size: 20px; letter-spacing: 2px; padding: 15px; border-radius: 50px; border: 2px solid #eef2f6; }
        .barcode-input:focus { border-color: #667eea; outline: none; }
        .result-card { background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-radius: 16px; padding: 20px; margin-top: 20px; border: 1px solid #eef2f6; }
        .status-btn { border-radius: 12px; padding: 12px; font-weight: 600; transition: all 0.2s; border: none; color: white; width: 100%; }
        .status-btn-found { background: #10b981; }
        .status-btn-missing { background: #ef4444; }
        .status-btn-damaged { background: #f59e0b; }
        .status-btn-moved { background: #3b82f6; }
        .progress-bar-custom { background: linear-gradient(90deg, #10b981 0%, #059669 100%); border-radius: 10px; height: 8px; }
        .scan-btn { 
            border-radius: 50px; 
            padding: 12px 20px; 
            font-weight: 600;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            width: 100%;
            font-size: 16px;
        }
        .scan-btn:active { transform: scale(0.95); }
        
        /* Modal Scanner Mobile */
        .modal-scanner {
            background: rgba(0, 0, 0, 0.95);
        }
        .modal-scanner .modal-content {
            background: transparent;
            border: none;
            box-shadow: none;
        }
        .modal-scanner .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }
        .modal-scanner .modal-header .btn-close {
            background-color: white;
            border-radius: 50%;
            opacity: 1;
            padding: 10px;
        }
        .modal-scanner .modal-body {
            padding: 0 20px 20px;
        }
        .modal-scanner .modal-footer {
            border-top: none;
            justify-content: center;
            padding-top: 15px;
        }
        #qr-reader {
            width: 100%;
            min-height: 300px;
            border-radius: 16px;
            overflow: hidden;
        }
        #qr-reader video {
            border-radius: 16px;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <div class="header">
            <a href="{{ route('mobile.stock-opname.index') }}" class="position-absolute start-0 ms-3 text-white" style="font-size: 24px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <i class="bi bi-upc-scan fs-2"></i>
            <h5 class="mt-1">Scan Aset</h5>
            <p class="mb-0 small">{{ $session->name }}</p>
        </div>

        <div class="p-3">
            <!-- Progress -->
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1 small">
                    <span>Progress</span>
                    <span id="progressText">{{ $stats['scanned'] }}/{{ $stats['total'] }}</span>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar-custom" id="progressBar" style="width: {{ $stats['progress'] }}%"></div>
                </div>
            </div>

            <!-- Input Barcode dengan Tombol Scan -->
            <div class="mb-3">
                <label class="form-label fw-bold">Masukkan Kode Aset</label>
                <div class="input-group">
                    <input type="text" id="barcodeInput" class="form-control barcode-input" placeholder="Kode aset" autofocus>
                    <button class="btn btn-primary" type="button" id="manualCheckBtn" style="border-radius: 0 50px 50px 0; padding: 0 20px;">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="d-grid gap-2 mt-2">
                    <button type="button" class="scan-btn" data-bs-toggle="modal" data-bs-target="#scanModal">
                        <i class="bi bi-camera me-2"></i> Scan Barcode / QR Code
                    </button>
                </div>
                <small class="text-muted">Tekan Enter atau klik tombol Cek</small>
            </div>

            <div id="loading" style="display: none;" class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Memproses...</p>
            </div>

            <!-- Hasil Scan -->
            <div id="scanResult" style="display: none;">
                <div class="result-card">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-check-circle-fill text-success fs-3 me-2"></i>
                        <h6 class="mb-0 fw-bold">Hasil Scan</h6>
                    </div>
                    <table class="table table-sm">
                        <tr><th>Kode Aset:</th><td><strong id="resultCode"></strong></td></tr>
                        <tr><th>Nama Aset:</th><td><span id="resultName"></span></td></tr>
                        <tr><th>Lokasi:</th><td><span id="resultLocation"></span></td></tr>
                        <tr><th>Kondisi:</th><td><span id="resultCondition"></span></td></tr>
                    </table>
                    <hr>
                    <div class="mb-2">
                        <label class="fw-bold mb-2">Status Verifikasi</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn status-btn status-btn-found w-100 py-2" onclick="submitStatus('found')">
                                    <i class="bi bi-check-circle"></i> Ditemukan
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn status-btn status-btn-missing w-100 py-2" onclick="submitStatus('missing')">
                                    <i class="bi bi-question-circle"></i> Hilang
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn status-btn status-btn-damaged w-100 py-2" onclick="showNote('damaged')">
                                    <i class="bi bi-exclamation-triangle"></i> Rusak
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn status-btn status-btn-moved w-100 py-2" onclick="showLocation()">
                                    <i class="bi bi-arrow-repeat"></i> Berpindah
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="extraFields" style="display: none;">
                        <div id="locationField" style="display: none;">
                            <input type="text" id="newLocation" class="form-control mb-2" placeholder="Lokasi baru">
                        </div>
                        <textarea id="notes" class="form-control mb-2" rows="2" placeholder="Catatan..."></textarea>
                        <button class="btn btn-primary w-100" onclick="submitWithExtra()">Simpan</button>
                        <button class="btn btn-secondary w-100 mt-2" onclick="hideExtra()">Batal</button>
                    </div>
                </div>
            </div>

            <!-- Alert -->
            <div id="alertMessage" style="display: none;" class="alert alert-success mt-3"></div>
        </div>
    </div>

    <!-- ============ MODAL SCANNER ============ -->
    <div class="modal fade modal-scanner" id="scanModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">
                        <i class="bi bi-upc-scan me-2"></i> Scan Barcode
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="qr-reader"></div>
                    <div class="text-center text-white mt-3">
                        <p>Arahkan kamera ke barcode aset</p>
                        <small class="text-white-50">Klik Batal atau tekan ESC untuk menutup</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentItemId = null;
    let currentExtraStatus = null;
    let totalItems = {{ $stats['total'] }};
    let scannedItems = {{ $stats['scanned'] }};
    let html5QrCode = null;

    // ============ SCANNER ============
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
            showAlert('Gagal akses kamera', 'error');
        });
    }

    function stopScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => html5QrCode = null).catch(() => {});
        }
    }

    // ============ INPUT MANUAL ============
    document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            let barcode = this.value.trim();
            if (barcode) scanAsset(barcode);
        }
    });

    document.getElementById('manualCheckBtn')?.addEventListener('click', function() {
        let barcode = document.getElementById('barcodeInput').value.trim();
        if (barcode) scanAsset(barcode);
    });

    // ============ SCAN ASSET ============
    function scanAsset(barcode) {
        if (!barcode) {
            showAlert('Barcode kosong', 'warning');
            return;
        }
        
        showLoading(true);
        hideResult();
        
        let sessionId = {{ $session->id ?? 0 }};
        let url = '/stock-opname-mobile/scan-asset/' + sessionId + '?barcode=' + encodeURIComponent(barcode);
        
        console.log('Scan URL:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            showLoading(false);
            console.log('Scan response:', data);
            
            if (data.success) {
                currentItemId = data.itemId;
                currentAssetName = data.asset.name;
                
                document.getElementById('resultCode').innerText = data.asset.asset_code;
                document.getElementById('resultName').innerText = data.asset.name;
                document.getElementById('resultLocation').innerText = data.asset.location_name;
                document.getElementById('resultCondition').innerText = data.asset.condition || 'Baik';
                
                document.getElementById('scanResult').style.display = 'block';
                document.getElementById('barcodeInput').value = '';
                document.getElementById('barcodeInput').focus();
            } else {
                showAlert(data.message || 'Aset tidak ditemukan', 'error');
                document.getElementById('barcodeInput').value = '';
                document.getElementById('barcodeInput').focus();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showLoading(false);
            showAlert('Error: ' + error.message, 'error');
            document.getElementById('barcodeInput').value = '';
            document.getElementById('barcodeInput').focus();
        });
    }

    function resetScanState() {
        currentItemId = null;
        currentAssetName = null;
        currentExtraStatus = null;
        document.getElementById('scanResult').style.display = 'none';
        document.getElementById('extraFields').style.display = 'none';
        document.getElementById('locationField').style.display = 'none';
        document.getElementById('newLocation').value = '';
        document.getElementById('notes').value = '';
        document.getElementById('barcodeInput').value = '';
        document.getElementById('barcodeInput').focus();
    }

    // ============ SUBMIT STATUS ============
    function submitStatus(status, location = '', notes = '') {
        if (status === 'moved' && !location) {
            showAlert('Lokasi baru harus diisi', 'warning');
            return;
        }
        if (!currentItemId) {
            showAlert('Silakan scan aset terlebih dahulu', 'warning');
            return;
        }

        showLoading(true);
        
        let sessionId = {{ $session->id ?? 0 }};
        let url = '/stock-opname-mobile/submit-scan/' + sessionId + '/' + currentItemId;
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        
        console.log('Submit URL:', url);
        console.log('Data:', {
            actual_status: status,
            actual_location: location,
            notes: notes
        });
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                actual_status: status,
                actual_location: location,
                notes: notes
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error('HTTP error ' + response.status + ': ' + text);
                });
            }
            return response.json();
        })
        .then(data => {
            showLoading(false);
            console.log('Response data:', data);
            
            if (data.success) {
                // ========== UPDATE PROGRESS ==========
                scannedItems++;
                let remaining = totalItems - scannedItems;
                let percent = Math.round((scannedItems / totalItems) * 100);
                
                document.getElementById('progressBar').style.width = percent + '%';
                document.getElementById('progressText').innerText = scannedItems + '/' + totalItems;
                
                // ========== HIDE RESULT & RESET ==========
                document.getElementById('scanResult').style.display = 'none';
                document.getElementById('extraFields').style.display = 'none';
                document.getElementById('locationField').style.display = 'none';
                document.getElementById('newLocation').value = '';
                document.getElementById('notes').value = '';
                document.getElementById('barcodeInput').value = '';
                
                // ========== SHOW NOTIFICATION ==========
                let statusText = {
                    'found': 'Ditemukan ✅',
                    'missing': 'Hilang ❌',
                    'damaged': 'Rusak ⚠️',
                    'moved': 'Berpindah 🔄'
                };
                
                showAlert('✓ Aset "' + currentAssetName + '" - ' + statusText[status], 'success');
                
                // ========== CHECK COMPLETED ==========
                if (data.completed) {
                    showAlert('🎉 Stock Opname Selesai!', 'success');
                    setTimeout(() => {
                        window.location.href = '/stock-opname-mobile';
                    }, 2000);
                } else {
                    // ========== FOCUS UNTUK SCAN BERIKUTNYA ==========
                    document.getElementById('barcodeInput').focus();
                }
            } else {
                showAlert(data.message || 'Gagal menyimpan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showLoading(false);
            showAlert('Error: ' + error.message, 'error');
        });
    }

    // ============ SWEETALERT ============
    function showAlert(message, type = 'success') {
        const icons = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };
        
        const titles = {
            'success': 'Berhasil! ✅',
            'error': 'Gagal! ❌',
            'warning': 'Peringatan! ⚠️',
            'info': 'Informasi ℹ️'
        };
        
        Swal.fire({
            title: titles[type] || 'Info',
            text: message,
            icon: icons[type] || 'info',
            confirmButtonColor: '#4361ee',
            confirmButtonText: 'OK',
            timer: type === 'success' ? 2000 : 4000,
            timerProgressBar: true,
            showConfirmButton: type !== 'success',
            toast: type === 'success',
            position: 'top-end',
            showClass: {
                popup: 'animate__animated animate__fadeInRight'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutRight'
            }
        });
    }

    function showLoading(show) {
        document.getElementById('loading').style.display = show ? 'block' : 'none';
    }

    function hideResult() {
        document.getElementById('scanResult').style.display = 'none';
        hideExtra();
    }

    function showLocation() {
        currentExtraStatus = 'moved';
        document.getElementById('locationField').style.display = 'block';
        document.getElementById('extraFields').style.display = 'block';
    }

    function showNote(status) {
        currentExtraStatus = status;
        document.getElementById('extraFields').style.display = 'block';
    }

    function hideExtra() {
        document.getElementById('extraFields').style.display = 'none';
        document.getElementById('locationField').style.display = 'none';
        document.getElementById('newLocation').value = '';
        document.getElementById('notes').value = '';
    }

    function submitWithExtra() {
        let location = document.getElementById('newLocation').value;
        let notes = document.getElementById('notes').value;
        submitStatus(currentExtraStatus, location, notes);
    }



    document.getElementById('barcodeInput').focus();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>