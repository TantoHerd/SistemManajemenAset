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