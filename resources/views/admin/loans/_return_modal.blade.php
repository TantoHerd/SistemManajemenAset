<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.loans.return', $loan) }}" method="POST">
                @csrf
                
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="bi bi-box-arrow-in-left me-1"></i>Pengembalian Aset
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    
                    {{-- Info Peminjaman --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block">Kode Peminjaman</small>
                                <strong>{{ $loan->loan_code }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block">Status</small>
                                <span class="badge bg-{{ $loan->status_badge }}">{{ $loan->status_label }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block">Aset</small>
                                <strong>{{ $loan->asset->name }}</strong>
                                <br><code>{{ $loan->asset->asset_code }}</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-3 p-3">
                                <small class="text-muted d-block">Peminjam</small>
                                <strong>{{ $loan->user->name }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Tanggal --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block">Tanggal Pinjam</small>
                                <strong>{{ $loan->loan_date->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block">Estimasi Kembali</small>
                                <strong>{{ $loan->expected_return_date->format('d M Y') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded-3 p-3 text-center">
                                <small class="text-muted d-block">Hari Ini</small>
                                <strong class="text-primary">{{ now()->format('d M Y') }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Status Keterlambatan --}}
                    @php
                        $isLate = now()->startOfDay()->gt($loan->expected_return_date);
                        $daysLate = $isLate ? now()->diffInDays($loan->expected_return_date) : 0;
                        $fine = $daysLate * 10000;
                        $daysBorrowed = $loan->loan_date->diffInDays(now());
                    @endphp
                    
                    @if($isLate)
                    <div class="alert alert-danger d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                        <div>
                            <strong>Keterlambatan!</strong>
                            <p class="mb-0">
                                Aset terlambat <strong>{{ $daysLate }} hari</strong> dari batas pengembalian.
                                <br>Denda: <strong class="fs-5">Rp {{ number_format($fine, 0, ',', '.') }}</strong> 
                                ({{ $daysLate }} hari × Rp 10.000)
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-success d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-check-circle fs-2"></i>
                        <div>
                            <strong>Tepat Waktu!</strong>
                            <p class="mb-0">Aset dikembalikan sesuai jadwal. Tidak ada denda.</p>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Durasi Peminjaman --}}
                    <div class="bg-info bg-opacity-10 rounded-3 p-3 mb-3 text-center">
                        <small class="text-muted d-block">Total Durasi Peminjaman</small>
                        <strong class="fs-5">{{ $daysBorrowed }} hari</strong>
                    </div>
                    
                    {{-- Kondisi Setelah Kembali --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Kondisi Aset Setelah Kembali <span class="text-danger">*</span>
                        </label>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="condition_after" value="Baik" id="baik" checked>
                            <label class="form-check-label" for="baik">✅ Baik (Tidak ada kerusakan)</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="condition_after" value="Rusak Ringan" id="ringan">
                            <label class="form-check-label" for="ringan">⚠️ Rusak Ringan (Lecet, minor)</label>
                        </div>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="condition_after" value="Rusak Berat" id="berat">
                            <label class="form-check-label" for="berat">🔴 Rusak Berat (Perlu perbaikan)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="condition_after" value="Hilang" id="hilang">
                            <label class="form-check-label" for="hilang">❌ Hilang</label>
                        </div>
                    </div>
                    
                    {{-- Catatan Tambahan --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Catatan Pengembalian</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-box-arrow-in-left me-1"></i>Konfirmasi Pengembalian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>