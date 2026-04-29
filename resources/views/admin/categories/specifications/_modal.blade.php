<!-- Modal Tambah/Edit Spesifikasi -->
<div class="modal fade" id="addSpecModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="specForm" method="POST" action="{{ route('admin.categories.specifications.store', $category) }}">
                @csrf
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-plus-circle me-1"></i>Tambah Spesifikasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Label -->
                        <div class="col-md-6">
                            <label class="form-label">
                                Label Spesifikasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="label" 
                                   class="form-control" 
                                   placeholder="Contoh: Processor, RAM, Warna"
                                   required>
                            <small class="text-muted">
                                <i class="bx bx-info-circle"></i> 
                                Nama spesifikasi yang akan ditampilkan
                            </small>
                        </div>
                        
                        <!-- Tipe Data -->
                        <div class="col-md-6">
                            <label class="form-label">
                                Tipe Data <span class="text-danger">*</span>
                            </label>
                            <select name="type" class="form-select" required>
                                <option value="">Pilih Tipe Data</option>
                                <option value="text">
                                    <i class="bx bx-text"></i> Text - Input teks pendek
                                </option>
                                <option value="number">
                                    <i class="bx bx-hash"></i> Angka - Input angka
                                </option>
                                <option value="textarea">
                                    <i class="bx bx-align-left"></i> Teks Panjang - Textarea
                                </option>
                                <option value="date">
                                    <i class="bx bx-calendar"></i> Tanggal - Date picker
                                </option>
                                <option value="boolean">
                                    <i class="bx bx-toggle-left"></i> Ya/Tidak - Switch toggle
                                </option>
                                <option value="select">
                                    <i class="bx bx-list-ul"></i> Pilihan - Dropdown select
                                </option>
                            </select>
                        </div>
                        
                        <!-- Options untuk Select -->
                        <div class="col-12" id="options-container" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0 fw-semibold">
                                            <i class="bx bx-list-check me-1"></i>Pilihan (Options)
                                        </label>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-option-btn">
                                            <i class="bx bx-plus me-1"></i>Tambah Pilihan
                                        </button>
                                    </div>
                                    <div id="options-list">
                                        <!-- Dynamic option rows -->
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bx bx-info-circle"></i> 
                                        Minimal 2 pilihan untuk tipe select
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Placeholder -->
                        <div class="col-md-6">
                            <label class="form-label">Placeholder</label>
                            <input type="text" 
                                   name="placeholder" 
                                   class="form-control" 
                                   placeholder="Contoh: Masukkan tipe processor">
                            <small class="text-muted">
                                Teks petunjuk di dalam input field
                            </small>
                        </div>
                        
                        <!-- Sort Order -->
                        <div class="col-md-6">
                            <label class="form-label">Urutan</label>
                            <input type="number" 
                                   name="sort_order" 
                                   class="form-control" 
                                   value="0"
                                   min="0">
                            <small class="text-muted">
                                Urutan tampilan (0 = paling atas)
                            </small>
                        </div>
                        
                        <!-- Help Text -->
                        <div class="col-12">
                            <label class="form-label">Teks Bantuan</label>
                            <textarea name="help_text" 
                                      class="form-control" 
                                      rows="2"
                                      placeholder="Petunjuk pengisian spesifikasi ini"></textarea>
                        </div>
                        
                        <!-- Required -->
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_required" 
                                       value="1"
                                       id="is_required">
                                <label class="form-check-label" for="is_required">
                                    <strong>Wajib Diisi</strong>
                                </label>
                            </div>
                            <small class="text-muted d-block ms-4">
                                Checklist jika spesifikasi ini harus diisi saat input aset
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>Simpan Spesifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>