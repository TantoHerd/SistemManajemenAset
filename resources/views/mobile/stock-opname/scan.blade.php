<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#667eea">
    <title>Scan Aset - {{ $session->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f5f5f5; font-family: -apple-system, BlinkMacSystemFont, sans-serif; }
        .mobile-container { max-width: 480px; margin: 0 auto; background: white; min-height: 100vh; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
        .barcode-input { text-align: center; font-size: 20px; letter-spacing: 2px; padding: 15px; border-radius: 50px; border: 2px solid #eef2f6; }
        .barcode-input:focus { border-color: #667eea; outline: none; }
        .result-card { background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-radius: 16px; padding: 20px; margin-top: 20px; border: 1px solid #eef2f6; }
        .status-btn { border-radius: 12px; padding: 12px; font-weight: 600; transition: all 0.2s; }
        .status-btn-found { background: #10b981; color: white; }
        .status-btn-missing { background: #ef4444; color: white; }
        .status-btn-damaged { background: #f59e0b; color: white; }
        .status-btn-moved { background: #3b82f6; color: white; }
        .progress-bar-custom { background: linear-gradient(90deg, #10b981 0%, #059669 100%); border-radius: 10px; height: 8px; }
    </style>
</head>
<body>
    <div class="mobile-container">
        <div class="header">
            <a href="{{ route('mobile.stock-opname.index') }}" class="position-absolute start-0 ms-3 text-white">
                <i class="bi bi-arrow-left fs-4"></i>
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

            <!-- Input Barcode -->
            <div class="mb-4">
                <label class="form-label fw-bold">Masukkan Kode Aset</label>
                <input type="text" id="barcodeInput" class="form-control barcode-input" placeholder="Kode aset / scan barcode" autofocus>
                <small class="text-muted">Tekan Enter setelah memasukkan kode</small>
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
                        <tr><th>Kode Aset:</th><td><strong id="resultCode"></strong></span></td></tr>
                        <tr><th>Nama Aset:</th><td><span id="resultName"></span></td></tr>
                        <tr><th>Lokasi:</th><td><span id="resultLocation"></span></td></tr>
                        <tr><th>Kondisi:</th><td><span id="resultCondition"></span></td></tr>
                    </table>
                    <hr>
                    <div class="mb-2">
                        <label class="fw-bold mb-2">Status Verifikasi</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <button class="btn status-btn-found w-100 py-2" onclick="submitStatus('found')">
                                    <i class="bi bi-check-circle"></i> Ditemukan
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn status-btn-missing w-100 py-2" onclick="submitStatus('missing')">
                                    <i class="bi bi-question-circle"></i> Hilang
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn status-btn-damaged w-100 py-2" onclick="showNote('damaged')">
                                    <i class="bi bi-exclamation-triangle"></i> Rusak
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn status-btn-moved w-100 py-2" onclick="showLocation()">
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

    <script>
        let currentItemId = null;
        let currentExtraStatus = null;
        let totalItems = {{ $stats['total'] }};
        let scannedItems = {{ $stats['scanned'] }};

        document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                let barcode = this.value.trim();
                if (barcode) scanAsset(barcode);
            }
        });

        function scanAsset(barcode) {
            showLoading(true);
            hideResult();
            
            fetch('{{ route("mobile.stock-opname.scan-asset", $session) }}?barcode=' + encodeURIComponent(barcode))
                .then(res => res.json())
                .then(data => {
                    showLoading(false);
                    if (data.success) {
                        currentItemId = data.itemId;
                        document.getElementById('resultCode').innerText = data.asset.asset_code;
                        document.getElementById('resultName').innerText = data.asset.name;
                        document.getElementById('resultLocation').innerText = data.asset.location_name;
                        document.getElementById('resultCondition').innerText = data.asset.condition;
                        document.getElementById('scanResult').style.display = 'block';
                        document.getElementById('barcodeInput').value = '';
                    } else {
                        showAlert(data.message, 'danger');
                        document.getElementById('barcodeInput').value = '';
                        document.getElementById('barcodeInput').focus();
                    }
                })
                .catch(err => {
                    showLoading(false);
                    showAlert('Error: ' + err.message, 'danger');
                    document.getElementById('barcodeInput').value = '';
                    document.getElementById('barcodeInput').focus();
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

        function submitStatus(status, location = '', notes = '') {
            if (status === 'moved' && !location) {
                showAlert('Lokasi baru harus diisi', 'warning');
                return;
            }
            if (!currentItemId) return;

            showLoading(true);
            
            fetch('{{ route("mobile.stock-opname.submit-scan", ["session" => $session, "item" => "__ITEM_ID__"]) }}'.replace('__ITEM_ID__', currentItemId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    actual_status: status,
                    actual_location: location,
                    notes: notes
                })
            })
            .then(res => res.json())
            .then(data => {
                showLoading(false);
                if (data.success) {
                    scannedItems++;
                    let remaining = totalItems - scannedItems;
                    let percent = Math.round((scannedItems / totalItems) * 100);
                    
                    document.getElementById('progressBar').style.width = percent + '%';
                    document.getElementById('progressText').innerText = scannedItems + '/' + totalItems;
                    document.getElementById('scanResult').style.display = 'none';
                    hideExtra();
                    
                    let statusText = {found:'Ditemukan', missing:'Hilang', damaged:'Rusak', moved:'Berpindah'};
                    showAlert('✓ ' + statusText[status] + ' - Lanjut ke aset berikutnya', 'success');
                    
                    if (data.completed) {
                        showAlert('🎉 Stock Opname Selesai!', 'success');
                        setTimeout(() => window.location.href = '{{ route("mobile.stock-opname.index") }}', 2000);
                    } else {
                        document.getElementById('barcodeInput').focus();
                    }
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(err => {
                showLoading(false);
                showAlert('Error: ' + err.message, 'danger');
            });
        }

        function showAlert(msg, type) {
            let alertDiv = document.getElementById('alertMessage');
            alertDiv.className = 'alert alert-' + type + ' mt-3';
            alertDiv.innerText = msg;
            alertDiv.style.display = 'block';
            setTimeout(() => alertDiv.style.display = 'none', 2000);
        }

        document.getElementById('barcodeInput').focus();
    </script>
</body>
</html>