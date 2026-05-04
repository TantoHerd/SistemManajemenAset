@extends('admin.layouts.app')

@section('title', 'Detail Aset - ' . $asset->asset_code)
@section('page-title', 'Detail Aset')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.assets.index') }}">Aset</a></li>
    <li class="breadcrumb-item active">{{ $asset->asset_code }}</li>
@endsection

@section('header-actions')
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.assets.print-label', $asset) }}" class="btn btn-primary btn-sm" target="_blank">
            <i class="bi bi-upc-scan"></i> Cetak Label
        </a>
        <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
{{-- Grid utama: di HP jadi 1 kolom, di desktop 2 kolom --}}
<div class="row g-3">
    
    {{-- KOLOM KIRI: QR CODE --}}
    <div class="col-12 col-md-5 col-lg-4">
        <div class="card h-100 text-center">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-upc-scan text-primary"></i> QR Code Aset</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                {{-- Background QR dengan border radius --}}
                <div class="bg-light p-3 rounded-4 mb-3">
                    @php
                        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->margin(0)->generate($asset->asset_code);
                    @endphp
                    <div style="width: 130px; height: 130px;">{!! $qrCode !!}</div>
                </div>
                <code class="small bg-light px-2 py-1 rounded">{{ $asset->asset_code }}</code>
                <button onclick="generateBarcode({{ $asset->id }})" class="btn btn-sm btn-outline-primary mt-3 rounded-pill">
                    <i class="bi bi-arrow-repeat"></i> Generate Ulang
                </button>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: STATUS & QUICK ACTION --}}
    <div class="col-12 col-md-7 col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary"></i> Informasi Status</h6>
            </div>
            <div class="card-body">
                {{-- Grid status card dalam 3 kolom di desktop, 2 kolom di HP --}}
                <div class="row g-3">
                    <div class="col-6 col-sm-4">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Status</div>
                            <span class="badge {{ $asset->status_badge_class }} px-3 py-2 rounded-pill fs-6">
                                {{ $asset->status_label }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Kategori</div>
                            <div class="fw-semibold">{{ $asset->category->name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Masa Manfaat</div>
                            <div class="fw-semibold">{{ $asset->useful_life_months }} bln</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="bg-light rounded-3 p-3">
                            <div class="text-muted small mb-1">Lokasi</div>
                            <div class="fw-semibold">{{ $asset->location->full_path ?? $asset->location->name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="bg-light rounded-3 p-3">
                            <div class="text-muted small mb-1">Pengguna</div>
                            <div class="fw-semibold">
                                @if($asset->assignedTo)
                                    <i class="bi bi-person-check text-success me-1"></i> {{ $asset->assignedTo->name }}
                                @else
                                    <span class="text-muted"><i class="bi bi-person"></i> Belum diassign</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Action Buttons --}}
                <div class="mt-4 pt-2 border-top d-flex flex-wrap gap-2">
                    @if($asset->status === 'available')
                        <button onclick="toggleCheckInOut({{ $asset->id }})" class="btn btn-success flex-grow-1 rounded-pill">
                            <i class="bi bi-box-arrow-right"></i> Checkout Aset
                        </button>
                    @elseif($asset->status === 'in_use')
                        <button onclick="toggleCheckInOut({{ $asset->id }})" class="btn btn-warning flex-grow-1 rounded-pill">
                            <i class="bi bi-box-arrow-in-left"></i> Checkin Aset
                        </button>
                    @endif
                    <button class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                        <i class="bi bi-wrench"></i> Maintenance
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DETAIL INFORMASI ASET & SPESIFIKASI (2 kolom di desktop, 1 kolom di HP) --}}
<div class="row g-3 mt-2">
    
    {{-- INFORMASI ASET --}}
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-laptop text-primary"></i> Informasi Aset</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                        <span class="text-muted">Kode Aset</span>
                        <code class="bg-light px-2 py-1 rounded">{{ $asset->asset_code }}</code>
                    </li>
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                        <span class="text-muted">Nama Aset</span>
                        <span class="fw-semibold">{{ $asset->name }}</span>
                    </li>
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                        <span class="text-muted">Serial Number</span>
                        <span>{{ $asset->serial_number ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                        <span class="text-muted">Brand / Model</span>
                        <span>{{ $asset->brand ?? '-' }} / {{ $asset->model ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                        <span class="text-muted">Garansi</span>
                        @if($asset->warranty_expiry)
                            @if($asset->is_under_warranty)
                                <span class="badge bg-success">Aktif hingga {{ $asset->warranty_expiry->format('d M Y') }}</span>
                            @else
                                <span class="badge bg-secondary">Berakhir {{ $asset->warranty_expiry->format('d M Y') }}</span>
                            @endif
                        @else
                            <span>-</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                        <span class="text-muted">Tahun Pengadaan</span>
                        <span>{{ $asset->purchase_date ? $asset->purchase_date->format('Y') : '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <span class="text-muted d-block mb-1">Catatan</span>
                        <p class="mb-0">{{ $asset->notes ?? '-' }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- SPESIFIKASI ASET --}}
    {{-- ============================================ --}}
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-list-check text-info"></i> Spesifikasi
                </h6>
                @if($asset->specifications->count() > 0)
                    <span class="badge bg-info">{{ $asset->specifications->count() }} spesifikasi</span>
                @endif
            </div>
            <div class="card-body p-0">
                @php
                    // Ambil spesifikasi dari kategori yang terurut
                    $categorySpecs = $asset->category->activeSpecifications ?? collect([]);
                    $assetSpecs = $asset->specifications->pluck('spec_value', 'spec_key');
                @endphp
                
                @if($categorySpecs->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($categorySpecs as $spec)
                            @php
                                $value = $assetSpecs[$spec->key] ?? null;
                            @endphp
                            <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                                <span class="text-muted">
                                    {{ $spec->label }}
                                    @if($spec->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </span>
                                <span>
                                    @if($value === null || $value === '')
                                        <span class="text-muted fst-italic">-</span>
                                    @elseif($spec->type == 'boolean')
                                        @if($value == '1')
                                            <span class="badge bg-success">Ya</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak</span>
                                        @endif
                                    @elseif($spec->type == 'select')
                                        @php
                                            $option = collect($spec->options)->firstWhere('value', $value);
                                        @endphp
                                        <span class="fw-semibold">{{ $option['label'] ?? $value }}</span>
                                    @elseif($spec->type == 'number')
                                        <span class="fw-semibold">{{ is_numeric($value) ? number_format($value, 0, ',', '.') : $value }}</span>
                                    @elseif($spec->type == 'date')
                                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($value)->format('d M Y') }}</span>
                                    @else
                                        <span class="fw-semibold">{{ $value }}</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                        <p class="mb-2">Tidak ada spesifikasi untuk kategori ini</p>
                        <a href="{{ route('admin.categories.specifications.index', $asset->category) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Spesifikasi Kategori
                        </a>
                    </div>
                @endif
            </div>
            @if($categorySpecs->count() > 0)
                <div class="card-footer bg-white text-end">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        Spesifikasi berdasarkan kategori <strong>{{ $asset->category->name ?? '-' }}</strong>
                    </small>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- DOKUMEN PENDUKUNG --}}
<div class="card mt-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-folder2-open text-primary me-1"></i>Dokumen Pendukung ({{ $asset->documents->count() }})
        </h6>
        <div class="d-flex gap-2">
            @if($asset->documents->count() > 0)
            <button class="btn btn-outline-success btn-sm" onclick="downloadAll('{{ route('admin.assets.documents.download-folder', $asset) }}')">
                <i class="bi bi-download me-1"></i>Download Semua
            </button>
            @endif
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                <i class="bi bi-plus-circle me-1"></i>Upload
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @php
            $groupedDocs = $asset->documents->groupBy('folder_path');
        @endphp
        
        @if($groupedDocs->count() > 0)
            @foreach($groupedDocs as $folderPath => $docs)
            <div class="border-bottom">
                <!-- Folder Header -->
                <div class="bg-light px-3 py-2 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-folder-fill text-warning me-1"></i>
                        <strong>{{ $folderPath ?: 'Uncategorized' }}</strong>
                        <span class="badge bg-secondary ms-2">{{ $docs->count() }} file</span>
                    </div>
                    <a href="{{ route('admin.assets.documents.download-folder', ['asset' => $asset, 'folder_path' => $folderPath]) }}" 
                       class="btn btn-sm btn-outline-success" title="Download folder">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
                
                <!-- File List -->
                <div class="list-group list-group-flush">
                    @foreach($docs as $doc)
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex gap-2 align-items-center">
                            <!-- Preview Gambar -->
                            @if($doc->isImage())
                                <img src="{{ $doc->file_url }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;" 
                                     onclick="previewImage('{{ $doc->file_url }}', '{{ $doc->name }}')">
                            @else
                                <div class="bg-light rounded p-2" style="width: 40px; height: 40px; text-align: center;">
                                    <i class="bi {{ $doc->file_icon }} fs-6 text-primary"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold small">{{ $doc->name }}</div>
                                <small class="text-muted">
                                    {{ $doc->file_size_formatted }} • 
                                    <span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($doc->file_type) }}</span>
                                    • {{ $doc->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.documents.download', $doc) }}" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="Download">
                                <i class="bi bi-download"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="confirmDelete('{{ route('admin.documents.destroy', $doc) }}', 'Hapus {{ $doc->name }}?')" 
                                    data-bs-toggle="tooltip" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
            <p class="mb-0">Belum ada dokumen pendukung</p>
        </div>
        @endif
    </div>
</div>

{{-- MODAL UPLOAD --}}
<div class="modal fade" id="uploadDocModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.assets.documents.store', $asset) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-upload me-1"></i>Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" required>
                        <small class="text-muted">Max: 10MB</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Dokumen</label>
                        <input type="text" name="name" class="form-control" placeholder="Kosongkan untuk nama file asli">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipe</label>
                            <select name="file_type" class="form-select">
                                <option value="">-- Pilih --</option>
                                <option value="invoice">Invoice / Nota</option>
                                <option value="photo">Foto</option>
                                <option value="manual">Manual Book</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Folder</label>
                            <input type="text" name="folder_path" class="form-control" 
                                   placeholder="Auto: {{ strtoupper(str_replace(' ', '-', $asset->category->name ?? 'Uncategorized')) }}">
                            <small class="text-muted">Kosongkan untuk auto</small>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Opsional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW GAMBAR --}}
<div class="modal fade" id="previewImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="previewTitle"></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Preview" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

{{-- FINANSIAL (Full Width) --}}
<div class="row g-3 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-currency-dollar text-success"></i> Informasi Finansial</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Harga Beli</div>
                            <div class="fw-bold">{{ $asset->formatted_purchase_price }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Nilai Residu</div>
                            <div class="fw-bold">Rp {{ number_format($asset->residual_value, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Nilai Saat Ini</div>
                            <div class="fw-bold text-primary">{{ $asset->formatted_current_value }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Penyusutan</div>
                            <div class="fw-bold text-danger">{{ $asset->depreciation_percentage }}%</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Total Susut</div>
                            <div class="fw-bold">Rp {{ number_format($asset->purchase_price - $asset->current_value, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-lg-2">
                        <div class="bg-light rounded-3 p-3 text-center h-100">
                            <div class="text-muted small mb-1">Tgl Pembelian</div>
                            <div class="fw-bold">{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- RIWAYAT MAINTENANCE --}}
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h6 class="mb-0 fw-bold"><i class="bi bi-wrench text-primary"></i> Riwayat Maintenance</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Teknisi</th>
                                <th>Biaya</th>
                                <th>Status</th>
                                <th width="60"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asset->maintenances as $maintenance)
                            <tr>
                                <td class="text-nowrap">{{ $maintenance->maintenance_date ? $maintenance->maintenance_date->format('d/m/Y') : '-' }}</td>
                                <td>{{ $maintenance->type_label }}</td>
                                <td>{{ $maintenance->technician ?? '-' }}</td>
                                <td>{{ number_format($maintenance->cost, 0, ',', '.') }}</td>
                                <td><span class="badge bg-{{ $maintenance->status_badge }}">{{ $maintenance->status_label }}</span></td>
                                <td>
                                    <a href="{{ route('admin.maintenances.show', $maintenance) }}" class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-tools fs-4"></i>
                                    <p class="mb-0">Belum ada riwayat maintenance</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL MAINTENANCE (Kirim Maintenance) --}}
<div class="modal fade" id="maintenanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-wrench"></i> Kirim Maintenance</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.maintenances.store') }}" method="POST">
                @csrf
                <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="maintenance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Teknisi</label>
                            <input type="text" name="technician" class="form-control">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateBarcode(assetId) {
    $.ajax({
        url: '/admin/assets/' + assetId + '/generate-barcode',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if(response.success) location.reload();
            else alert(response.message);
        },
        error: function() { alert('Terjadi kesalahan'); }
    });
}

function toggleCheckInOut(assetId) {
    $.ajax({
        url: '/admin/assets/' + assetId + '/toggle-checkinout',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if(response.success) location.reload();
            else alert(response.message);
        },
        error: function() { alert('Terjadi kesalahan'); }
    });
}

// Preview Gambar
function previewImage(url, title) {
    document.getElementById('previewImage').src = url;
    document.getElementById('previewTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('previewImageModal')).show();
}

// Download Semua
function downloadAll(url) {
    window.location.href = url;
}
</script>
@endpush