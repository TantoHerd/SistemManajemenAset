<!-- Scan Modal -->
<div class="modal fade" id="scanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-upc-scan text-primary me-2"></i>Scan QR Code / Barcode
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="qr-reader" style="width:100%; min-height:300px;"></div>
                <div id="scan-result" class="mt-3"></div>
                
                <hr class="my-3">
                <label class="fw-semibold mb-2">Atau masukkan manual:</label>
                <div class="input-group">
                    <input type="text" id="manual-qrcode" class="form-control" placeholder="Kode aset">
                    <button class="btn btn-primary" id="manualCheckBtn">
                        <i class="bi bi-search"></i> Cek
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>