@extends('admin.layouts.app')

@section('title', 'Tambah Aset')
@section('page-title', 'Tambah Aset Baru')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.assets.index') }}">Aset</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle text-primary"></i> Form Tambah Aset
                </h5>
                <small class="text-muted">Lengkapi data aset di bawah ini</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.assets.store') }}" method="POST" id="assetForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <!-- Kode Aset -->
                            <div class="mb-3">
                                <label for="asset_code" class="form-label fw-semibold">
                                    Kode Aset <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                                    <input type="text" name="asset_code" id="asset_code" 
                                           class="form-control @error('asset_code') is-invalid @enderror"
                                           placeholder="Biarkan kosong untuk generate otomatis"
                                           value="{{ old('asset_code') }}">
                                </div>
                                <small class="text-muted">Kosongkan untuk generate kode otomatis</small>
                                @error('asset_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Nama Aset -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold text-danger">
                                    Nama Aset <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-tag"></i>
                                    </span>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder="Contoh: Dell XPS 15 Laptop"
                                           value="{{ old('name') }}" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Serial Number -->
                            <div class="mb-3">
                                <label for="serial_number" class="form-label fw-semibold">
                                    Serial Number <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                                    <input type="text" name="serial_number" id="serial_number" 
                                           class="form-control @error('serial_number') is-invalid @enderror"
                                           placeholder="Nomor seri aset"
                                           value="{{ old('serial_number') }}">
                                </div>
                                @error('serial_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Brand & Model (Row) -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="brand" class="form-label fw-semibold">
                                        Brand <span class="text-muted fw-normal">(Opsional)</span>
                                    </label>
                                    <input type="text" name="brand" id="brand" 
                                           class="form-control @error('brand') is-invalid @enderror"
                                           placeholder="Contoh: Dell, Apple, HP"
                                           value="{{ old('brand') }}">
                                    @error('brand')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="model" class="form-label fw-semibold">
                                        Model <span class="text-muted fw-normal">(Opsional)</span>
                                    </label>
                                    <input type="text" name="model" id="model" 
                                           class="form-control @error('model') is-invalid @enderror"
                                           placeholder="Contoh: XPS 15, MacBook Pro"
                                           value="{{ old('model') }}">
                                    @error('model')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <!-- Kategori -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-semibold text-danger">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select name="category_id" id="category_id" 
                                        class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                data-life="{{ $category->useful_life_months }}"
                                                data-has-specs="{{ $category->activeSpecifications->count() > 0 ? 'true' : 'false' }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->useful_life_months }} bulan)
                                            @if($category->activeSpecifications->count() > 0)
                                                • {{ $category->activeSpecifications->count() }} spesifikasi
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Lokasi -->
                            <div class="mb-3">
                                <label for="location_id" class="form-label fw-semibold text-danger">
                                    Lokasi <span class="text-danger">*</span>
                                </label>
                                <select name="location_id" id="location_id" 
                                        class="form-select @error('location_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" 
                                                {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->full_path ?? $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status" class="form-label fw-semibold text-danger">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status" 
                                        class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ old('status', 'available') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Assign ke User -->
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label fw-semibold">
                                    Assign ke Pengguna <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <select name="assigned_to" id="assigned_to" 
                                        class="form-select @error('assigned_to') is-invalid @enderror">
                                    <option value="">-- Tidak Diassign --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- ============================================ -->
                    <!-- SPESIFIKASI DINAMIS -->
                    <!-- ============================================ -->
                    <div id="specificationsContainer" style="display: none;">
                        <h6 class="mb-3">
                            <i class="bi bi-list-check text-info"></i> 
                            Spesifikasi: <span id="specCategoryName" class="text-primary fw-bold"></span>
                            <span class="badge bg-info ms-2" id="specCount">0</span>
                        </h6>
                        
                        <div class="card mb-3 border-start border-info border-3">
                            <div class="card-body">
                                <div id="specificationsFields" class="row">
                                    <!-- Dynamic fields akan dimuat di sini via AJAX -->
                                </div>
                                <div id="noSpecMessage" class="text-center py-3 text-muted" style="display: none;">
                                    <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                                    <p class="mb-0">Tidak ada spesifikasi untuk kategori ini</p>
                                </div>
                                <div id="specLoading" class="text-center py-3" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0">Memuat spesifikasi...</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                    </div>
                    
                    <!-- Informasi Finansial -->
                    <h6 class="mb-3">
                        <i class="bi bi-currency-dollar text-success"></i> Informasi Finansial
                    </h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="purchase_date" class="form-label fw-semibold text-danger">
                                Tanggal Beli <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="purchase_date" id="purchase_date" 
                                   class="form-control @error('purchase_date') is-invalid @enderror"
                                   value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="purchase_price" class="form-label fw-semibold text-danger">
                                Harga Beli (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="purchase_price" id="purchase_price" 
                                       class="form-control @error('purchase_price') is-invalid @enderror"
                                       placeholder="0"
                                       value="{{ old('purchase_price') }}" required>
                            </div>
                            @error('purchase_price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="residual_value" class="form-label fw-semibold">
                                Nilai Residu (Rp) <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="residual_value" id="residual_value" 
                                       class="form-control @error('residual_value') is-invalid @enderror"
                                       placeholder="10% dari harga beli"
                                       value="{{ old('residual_value') }}">
                            </div>
                            <small class="text-muted">Kosongkan untuk menggunakan 10% dari harga beli</small>
                            @error('residual_value')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="useful_life_months" class="form-label fw-semibold">
                                Masa Manfaat (Bulan) <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="number" name="useful_life_months" id="useful_life_months" 
                                   class="form-control @error('useful_life_months') is-invalid @enderror"
                                   placeholder="Dari kategori"
                                   value="{{ old('useful_life_months') }}">
                            <small class="text-muted">Kosongkan untuk menggunakan dari kategori</small>
                            @error('useful_life_months')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Garansi & Catatan -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="warranty_expiry" class="form-label fw-semibold">
                                Garansi Berakhir <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <input type="date" name="warranty_expiry" id="warranty_expiry" 
                                   class="form-control @error('warranty_expiry') is-invalid @enderror"
                                   value="{{ old('warranty_expiry') }}">
                            @error('warranty_expiry')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label fw-semibold">
                                Catatan <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Catatan tambahan tentang aset...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Tombol Submit -->
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Simpan Aset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // ============================================
    // SPESIFIKASI DINAMIS
    // ============================================
    
    /**
     * Load spesifikasi via AJAX ketika kategori dipilih
     */
    function loadSpecifications(categoryId, categoryName) {
        const container = $('#specificationsContainer');
        const fieldsContainer = $('#specificationsFields');
        const noSpecMessage = $('#noSpecMessage');
        const specLoading = $('#specLoading');
        const specCount = $('#specCount');
        const specCategoryName = $('#specCategoryName');
        
        // Reset
        fieldsContainer.empty();
        noSpecMessage.hide();
        
        if (!categoryId) {
            container.hide();
            return;
        }
        
        // Show container & loading
        container.show();
        specLoading.show();
        specCategoryName.text(categoryName);
        
        $.ajax({
            url: '{{ route("admin.assets.specifications.by-category") }}',
            type: 'GET',
            data: { category_id: categoryId },
            dataType: 'json',
            success: function(response) {
                specLoading.hide();
                
                if (!response.specifications || response.specifications.length === 0) {
                    noSpecMessage.show();
                    specCount.text('0');
                    return;
                }
                
                const specifications = response.specifications;
                specCount.text(specifications.length);
                
                // Render setiap spesifikasi
                specifications.forEach(function(spec) {
                    const fieldHtml = generateSpecField(spec);
                    fieldsContainer.append(fieldHtml);
                });
            },
            error: function(xhr) {
                specLoading.hide();
                fieldsContainer.html(`
                    <div class="col-12">
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Gagal memuat spesifikasi. Silakan coba lagi.
                        </div>
                    </div>
                `);
                console.error('Error loading specifications:', xhr);
            }
        });
    }
    
    /**
     * Generate HTML field untuk spesifikasi
     */
    function generateSpecField(spec) {
        const fieldName = `specifications[${spec.key}]`;
        const requiredAttr = spec.is_required ? 'required' : '';
        const requiredLabel = spec.is_required ? ' <span class="text-danger">*</span>' : '';
        const colClass = spec.type === 'textarea' ? 'col-12' : 'col-md-6';
        
        // Ambil old value jika ada
        const oldValue = '{{ old("specifications") }}' !== '' ? 
                         @json(old('specifications'))[spec.key] || '' : '';
        
        let fieldHtml = `
            <div class="${colClass} mb-3 spec-field" data-spec-key="${spec.key}">
                <label class="form-label fw-semibold">
                    ${spec.label}${requiredLabel}
                </label>`;
        
        switch(spec.type) {
            case 'text':
                fieldHtml += `
                    <input type="text" 
                           name="${fieldName}" 
                           class="form-control"
                           placeholder="${spec.placeholder || 'Masukkan ' + spec.label}"
                           value="${oldValue}"
                           ${requiredAttr}>`;
                break;
                
            case 'number':
                fieldHtml += `
                    <input type="number" 
                           name="${fieldName}" 
                           class="form-control"
                           placeholder="${spec.placeholder || '0'}"
                           value="${oldValue}"
                           step="any"
                           ${requiredAttr}>`;
                break;
                
            case 'textarea':
                fieldHtml += `
                    <textarea name="${fieldName}" 
                              class="form-control"
                              placeholder="${spec.placeholder || 'Masukkan ' + spec.label}"
                              rows="3"
                              ${requiredAttr}>${oldValue}</textarea>`;
                break;
                
            case 'date':
                fieldHtml += `
                    <input type="date" 
                           name="${fieldName}" 
                           class="form-control"
                           value="${oldValue}"
                           ${requiredAttr}>`;
                break;
                
            case 'boolean':
                fieldHtml += `
                    <div class="form-check form-switch mt-3">
                        <input type="hidden" name="${fieldName}" value="0">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="${fieldName}" 
                               value="1"
                               id="spec_${spec.key}"
                               ${oldValue == '1' ? 'checked' : ''}>
                        <label class="form-check-label" for="spec_${spec.key}">Ya</label>
                    </div>`;
                break;
                
            case 'select':
                fieldHtml += `
                    <select name="${fieldName}" 
                            class="form-select"
                            ${requiredAttr}>
                        <option value="">-- Pilih ${spec.label} --</option>`;
                
                if (spec.options && Array.isArray(spec.options)) {
                    spec.options.forEach(function(option) {
                        const selected = (oldValue == option.value) ? 'selected' : '';
                        fieldHtml += `
                            <option value="${option.value}" ${selected}>${option.label}</option>`;
                    });
                }
                
                fieldHtml += `</select>`;
                break;
                
            default:
                fieldHtml += `
                    <input type="text" 
                           name="${fieldName}" 
                           class="form-control"
                           placeholder="${spec.placeholder || ''}"
                           value="${oldValue}"
                           ${requiredAttr}>`;
        }
        
        if (spec.help_text) {
            fieldHtml += `
                <small class="text-muted d-block mt-1">
                    <i class="bi bi-info-circle"></i> ${spec.help_text}
                </small>`;
        }
        
        fieldHtml += `</div>`;
        
        return fieldHtml;
    }
    
    // ============================================
    // EVENT HANDLER
    // ============================================
    
    // Event: Kategori berubah
    $('#category_id').on('change', function() {
        const selectedOption = $(this).find(':selected');
        const categoryId = $(this).val();
        const categoryName = selectedOption.text().split(' (')[0]; // Ambil nama saja
        const usefulLife = selectedOption.data('life');
        
        // Update useful life months
        if (usefulLife && $('#useful_life_months').val() === '') {
            $('#useful_life_months').val(usefulLife);
        }
        
        // Load spesifikasi
        loadSpecifications(categoryId, categoryName);
    });
    
    // Auto-fill useful life months when category changes
    // (existing functionality - kept)
    
    // Auto-calculate residual value (10% of purchase price)
    $('#purchase_price').on('input', function() {
        let price = parseFloat($(this).val()) || 0;
        let residual = price * 0.1;
        
        if ($('#residual_value').val() === '') {
            $('#residual_value').val(residual);
        }
    });
    
    // Format purchase price as currency (for display only)
    $('#purchase_price').on('blur', function() {
        let value = parseFloat($(this).val()) || 0;
        if (value > 0) {
            $(this).css('background-color', '#e8f5e9');
        } else {
            $(this).css('background-color', '');
        }
    });
    
    // ============================================
    // INITIAL LOAD (jika kategori sudah dipilih)
    // ============================================
    if ($('#category_id').val()) {
        const selectedOption = $('#category_id').find(':selected');
        const categoryId = $('#category_id').val();
        const categoryName = selectedOption.text().split(' (')[0];
        
        // Load spesifikasi
        loadSpecifications(categoryId, categoryName);
        
        // Trigger useful life
        const usefulLife = selectedOption.data('life');
        if (usefulLife && $('#useful_life_months').val() === '') {
            $('#useful_life_months').val(usefulLife);
        }
    }
    
    // Trigger residual calculation if purchase price is pre-filled
    if ($('#purchase_price').val()) {
        $('#purchase_price').trigger('input');
    }
    
});
</script>
@endpush