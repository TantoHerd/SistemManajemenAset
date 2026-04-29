@php
    // Fallback kalau $category tidak ada
    if (!isset($category)) {
        $category = request()->route('category');
        if (!$category instanceof \App\Models\Category) {
            abort(404, 'Kategori tidak ditemukan');
        }
    }
    
    $specifications = $specifications ?? $category->specifications()->ordered()->get();
@endphp

@extends('admin.layouts.app')

@section('title', 'Spesifikasi Kategori')
@section('page-title', 'Spesifikasi Kategori')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Kategori</a></li>
    <li class="breadcrumb-item active">{{ $category->name }}</li>
@endsection

@section('header-actions')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpecModal">
        <i class="bx bx-plus me-1"></i>Tambah Spesifikasi
    </button>
    {{-- <a href="{{ route('admin.categories.specifications.index', $category) }}" 
        class="btn btn-sm btn-info">
            <i class="bx bx-list-check me-1"></i>Spesifikasi
    </a> --}}
@endsection

@section('content')
    <!-- Info Kategori -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">Kategori</h6>
                            <h5 class="text-white mb-0">{{ $category->name }}</h5>
                        </div>
                        <div>
                            <i class="bx bx-category fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">Total Spesifikasi</h6>
                            <h5 class="text-white mb-0">{{ $specifications->count() }}</h5>
                        </div>
                        <div>
                            <i class="bx bx-list-ul fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-1">Spesifikasi Aktif</h6>
                            <h5 class="text-white mb-0">{{ $specifications->where('is_active', true)->count() }}</h5>
                        </div>
                        <div>
                            <i class="bx bx-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-1"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bx bx-error-circle me-1"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Table Spesifikasi -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="bx bx-detail me-1"></i>Daftar Spesifikasi
            </h5>
            <small class="text-muted">
                <i class="bx bx-info-circle me-1"></i>
                Drag & drop untuk mengatur urutan
            </small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="specificationsTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50" class="text-center">#</th>
                            <th width="50">↕</th>
                            <th>Label</th>
                            <th>Key</th>
                            <th>Tipe Data</th>
                            <th class="text-center">Required</th>
                            <th class="text-center">Status</th>
                            <th width="150" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-list">
                        @forelse($specifications as $index => $spec)
                        <tr data-id="{{ $spec->id }}" class="{{ $spec->is_active ? '' : 'table-secondary' }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <i class="bx bx-menu handle cursor-move fs-5 text-muted"></i>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $spec->label }}</span>
                                @if($spec->is_required)
                                    <span class="badge bg-danger ms-1">*</span>
                                @endif
                                @if($spec->help_text)
                                    <div>
                                        <small class="text-muted">
                                            <i class="bx bx-info-circle"></i> {{ $spec->help_text }}
                                        </small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded">{{ $spec->key }}</code>
                            </td>
                            <td>
                                @php
                                    $typeBadges = [
                                        'text' => 'bg-primary',
                                        'number' => 'bg-info',
                                        'textarea' => 'bg-warning',
                                        'date' => 'bg-secondary',
                                        'boolean' => 'bg-dark',
                                        'select' => 'bg-success'
                                    ];
                                    $typeLabels = [
                                        'text' => 'Text',
                                        'number' => 'Angka',
                                        'textarea' => 'Teks Panjang',
                                        'date' => 'Tanggal',
                                        'boolean' => 'Ya/Tidak',
                                        'select' => 'Pilihan'
                                    ];
                                    $typeIcons = [
                                        'text' => 'bx-text',
                                        'number' => 'bx-hash',
                                        'textarea' => 'bx-align-left',
                                        'date' => 'bx-calendar',
                                        'boolean' => 'bx-toggle-left',
                                        'select' => 'bx-list-ul'
                                    ];
                                @endphp
                                <span class="badge {{ $typeBadges[$spec->type] ?? 'bg-primary' }}">
                                    <i class="bx {{ $typeIcons[$spec->type] ?? 'bx-text' }} me-1"></i>
                                    {{ $typeLabels[$spec->type] ?? $spec->type }}
                                </span>
                                @if($spec->type == 'select' && $spec->options)
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            {{ count($spec->options) }} opsi
                                        </small>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($spec->is_required)
                                    <span class="badge bg-danger">Ya</span>
                                @else
                                    <span class="badge bg-secondary">Tidak</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input toggle-active" 
                                           type="checkbox" 
                                           data-id="{{ $spec->id }}"
                                           data-url="{{ route('admin.categories.specifications.toggle-active', [$category, $spec]) }}"
                                           {{ $spec->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" 
                                            class="btn btn-sm btn-warning edit-spec" 
                                            data-spec='@json($spec)'
                                            data-toggle="tooltip"
                                            title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-info view-spec" 
                                            data-spec='@json($spec)'
                                            data-toggle="tooltip"
                                            title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger delete-spec" 
                                            data-id="{{ $spec->id }}"
                                            data-label="{{ $spec->label }}"
                                            data-toggle="tooltip"
                                            title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                    <h6>Belum ada spesifikasi</h6>
                                    <p class="mb-3">Tambahkan spesifikasi untuk kategori {{ $category->name }}</p>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSpecModal">
                                        <i class="bx bx-plus me-1"></i>Tambah Spesifikasi Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Include Modal -->
@include('admin.categories.specifications._modal')
@include('admin.categories.specifications._detail_modal')

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('styles')
<style>
.cursor-move {
    cursor: move;
}
.handle:hover {
    color: #696cff !important;
}
#sortable-list tr {
    transition: all 0.2s ease;
}
#sortable-list tr:hover {
    background-color: #f5f5f9;
}
.sortable-ghost {
    opacity: 0.4;
    background-color: #e7e7ff !important;
}
.sortable-chosen {
    background-color: #f0f0ff !important;
}
</style>
@endpush

@push('scripts')
<!-- SortableJS untuk drag & drop -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
$(document).ready(function() {
    
    // ==================== DRAG & DROP SORTING ====================
    const sortableList = document.getElementById('sortable-list');
    
    if (sortableList) {
        new Sortable(sortableList, {
            handle: '.handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                const orders = [];
                $('#sortable-list tr').each(function(index) {
                    orders.push($(this).data('id'));
                });
                
                // Update order via AJAX
                $.ajax({
                    url: '{{ route("admin.categories.specifications.update-order", $category) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        orders: orders
                    },
                    success: function(response) {
                        // Update nomor urut
                        $('#sortable-list tr').each(function(index) {
                            $(this).find('td:first').text(index + 1);
                        });
                        
                        toastr.success('Urutan spesifikasi berhasil diupdate');
                    },
                    error: function(xhr) {
                        toastr.error('Gagal mengupdate urutan');
                        location.reload();
                    }
                });
            }
        });
    }
    
    // ==================== TOGGLE ACTIVE ====================
    $('.toggle-active').change(function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const isActive = checkbox.prop('checked');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.is_active) {
                    checkbox.closest('tr').removeClass('table-secondary');
                    toastr.success('Spesifikasi diaktifkan');
                } else {
                    checkbox.closest('tr').addClass('table-secondary');
                    toastr.warning('Spesifikasi dinonaktifkan');
                }
            },
            error: function(xhr) {
                checkbox.prop('checked', !isActive);
                toastr.error('Gagal mengubah status');
            }
        });
    });
    
    // ==================== EDIT SPEC ====================
    $('.edit-spec').click(function() {
        const spec = $(this).data('spec');
        const modal = $('#addSpecModal');
        
        // Change modal title
        modal.find('.modal-title').text('Edit Spesifikasi');
        
        // Change form action
        const form = modal.find('#specForm');
        form.attr('action', '{{ route("admin.categories.specifications.update", [$category, "__ID__"]) }}'.replace('__ID__', spec.id));
        form.append('<input type="hidden" name="_method" value="PUT" id="methodField">');
        
        // Fill form fields
        form.find('[name="label"]').val(spec.label);
        form.find('[name="type"]').val(spec.type).trigger('change');
        form.find('[name="placeholder"]').val(spec.placeholder);
        form.find('[name="help_text"]').val(spec.help_text);
        form.find('[name="sort_order"]').val(spec.sort_order);
        
        if (spec.is_required) {
            form.find('[name="is_required"]').prop('checked', true);
        } else {
            form.find('[name="is_required"]').prop('checked', false);
        }
        
        // Handle options for select type
        setTimeout(function() {
            if (spec.type === 'select' && spec.options) {
                loadOptions(spec.options);
            }
        }, 100);
        
        // Show modal
        modal.modal('show');
    });
    
    // ==================== VIEW DETAIL SPEC ====================
    $('.view-spec').click(function() {
        const spec = $(this).data('spec');
        const modal = $('#detailSpecModal');
        
        // Fill detail
        modal.find('#detail-label').text(spec.label);
        modal.find('#detail-key').text(spec.key);
        modal.find('#detail-type').text(getTypeLabel(spec.type));
        modal.find('#detail-required').text(spec.is_required ? 'Ya' : 'Tidak');
        modal.find('#detail-placeholder').text(spec.placeholder || '-');
        modal.find('#detail-help').text(spec.help_text || '-');
        modal.find('#detail-status').text(spec.is_active ? 'Aktif' : 'Nonaktif');
        modal.find('#detail-sort').text(spec.sort_order);
        
        // Options
        let optionsHtml = '-';
        if (spec.type === 'select' && spec.options && spec.options.length > 0) {
            optionsHtml = '<ul class="list-group list-group-flush">';
            spec.options.forEach(function(option) {
                optionsHtml += `<li class="list-group-item py-1">
                    <strong>${option.value}</strong> → ${option.label}
                </li>`;
            });
            optionsHtml += '</ul>';
        }
        modal.find('#detail-options').html(optionsHtml);
        
        modal.modal('show');
    });
    
    // ==================== DELETE SPEC ====================
    $('.delete-spec').click(function() {
        const id = $(this).data('id');
        const label = $(this).data('label');
        
        Swal.fire({
            title: 'Hapus Spesifikasi?',
            html: `Anda yakin ingin menghapus <strong>${label}</strong>?<br>
                   <small class="text-danger">Semua data spesifikasi aset terkait akan ikut terhapus!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="bx bx-trash me-1"></i>Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $('#deleteForm');
                form.attr('action', '{{ route("admin.categories.specifications.destroy", [$category, "__ID__"]) }}'.replace('__ID__', id));
                form.submit();
            }
        });
    });
    
    // ==================== RESET MODAL ON ADD ====================
    $('[data-bs-target="#addSpecModal"]').click(function() {
        const modal = $('#addSpecModal');
        modal.find('.modal-title').text('Tambah Spesifikasi');
        
        const form = modal.find('#specForm');
        form.attr('action', '{{ route("admin.categories.specifications.store", $category) }}');
        form.find('#methodField').remove();
        form[0].reset();
        
        // Reset options container
        $('#options-container').hide();
        $('#options-list').empty();
        
        // Clear validation errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
    });
    
    // ==================== HANDLE TYPE CHANGE ====================
    $('[name="type"]').change(function() {
        const type = $(this).val();
        const optionsContainer = $('#options-container');
        
        if (type === 'select') {
            optionsContainer.show();
        } else {
            optionsContainer.hide();
            $('#options-list').empty();
        }
    });
    
    // ==================== ADD OPTION ====================
    $('#add-option-btn').click(function() {
        addOptionRow();
    });
    
    // ==================== REMOVE OPTION ====================
    $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-row').remove();
    });
    
});

// ==================== HELPER FUNCTIONS ====================
function getTypeLabel(type) {
    const labels = {
        'text': 'Text',
        'number': 'Angka',
        'textarea': 'Teks Panjang',
        'date': 'Tanggal',
        'boolean': 'Ya/Tidak',
        'select': 'Pilihan'
    };
    return labels[type] || type;
}

function loadOptions(options) {
    const optionsList = $('#options-list');
    optionsList.empty();
    
    options.forEach(function(option) {
        addOptionRow(option.value, option.label);
    });
    
    $('#options-container').show();
}

function addOptionRow(value = '', label = '') {
    const index = Date.now();
    const row = `
        <div class="option-row input-group input-group-sm mb-2">
            <input type="text" 
                   name="options[${index}][value]" 
                   class="form-control" 
                   placeholder="Value"
                   value="${value}"
                   required>
            <span class="input-group-text">→</span>
            <input type="text" 
                   name="options[${index}][label]" 
                   class="form-control" 
                   placeholder="Label"
                   value="${label}"
                   required>
            <button type="button" class="btn btn-outline-danger remove-option">
                <i class="bx bx-x"></i>
            </button>
        </div>
    `;
    $('#options-list').append(row);
}
</script>
@endpush