@extends('admin.layouts.app')

@section('title', 'Detail Aset - ' . $asset->asset_code)
@section('page-title', 'Detail Aset')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.assets.index') }}">Aset</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $asset->asset_code }}</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        {{-- <div class="dropdown">
            <button class="btn btn-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-download"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a href="{{ route('admin.maintenances.export', ['asset_id' => $asset->id]) }}" class="dropdown-item">
                        <i class="bi bi-file-earmark-excel"></i> Export Maintenance
                    </a>
                </li>
            </ul>
        </div> --}}
        <a href="{{ route('admin.assets.print-label', $asset) }}" class="btn btn-primary" target="_blank">
            <i class="bi bi-upc-scan"></i> Cetak Label
        </a>
        <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- QR Code Card -->
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-upc-scan"></i> QR Code Aset
                </h6>
            </div>
            <div class="card-body py-4">
                @php
                    $url = route('admin.assets.show', $asset);
                    $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(120)->margin(0)->generate($url);
                @endphp
                <div style="display: flex; justify-content: center; align-items: center; background: #fafafa; padding: 15px; border-radius: 10px;">
                    {!! $qrCode !!}
                </div>
                <div class="mt-3">
                    <code>{{ $asset->asset_code }}</code>
                </div>
                <small class="text-muted">Scan untuk melihat detail aset</small>
            </div>
            <div class="card-footer bg-white">
                <button onclick="generateBarcode({{ $asset->id }})" class="btn btn-sm btn-primary">
                    <i class="bi bi-arrow-repeat"></i> Generate Ulang QR Code
                </button>
            </div>
        </div>
    </div>
    
    <!-- Status Card -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi Status
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Status</strong></td>
                                <td>
                                    <span class="badge {{ $asset->status_badge_class }} px-3 py-2 rounded-pill">
                                        {{ $asset->status_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi</strong></td>
                                <td>
                                    <i class="bi bi-geo-alt text-primary me-1"></i>
                                    {{ $asset->location->full_path ?? $asset->location->name ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>
                                    <i class="bi bi-tag text-info me-1"></i>
                                    {{ $asset->category->name ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Masa Manfaat</strong></td>
                                <td>{{ $asset->useful_life_months }} bulan ({{ round($asset->useful_life_months / 12, 1) }} tahun)</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>Pengguna</strong></td>
                                <td>
                                    @if($asset->assignedTo)
                                        <i class="bi bi-person-check text-success me-1"></i>
                                        {{ $asset->assignedTo->name }}
                                        <br>
                                        <small class="text-muted">{{ $asset->assignedTo->email }}</small>
                                    @else
                                        <span class="text-muted">
                                            <i class="bi bi-person"></i> Belum diassign
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Garansi</strong></td>
                                <td>
                                    @if($asset->warranty_expiry)
                                        @if($asset->is_under_warranty)
                                            <span class="text-success">
                                                <i class="bi bi-shield-check"></i> 
                                                Berlaku hingga {{ $asset->warranty_expiry->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="text-danger">
                                                <i class="bi bi-shield-exclamation"></i> 
                                                Berakhir pada {{ $asset->warranty_expiry->format('d M Y') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">Tidak ada informasi garansi</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat</strong></td>
                                <td>{{ $asset->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Diperbarui</strong></td>
                                <td>{{ $asset->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="mt-3 pt-3 border-top">
                    @if($asset->status === 'available')
                        <button onclick="toggleCheckInOut({{ $asset->id }})" class="btn btn-success">
                            <i class="bi bi-box-arrow-right"></i> Checkout Aset
                        </button>
                    @elseif($asset->status === 'in_use')
                        <button onclick="toggleCheckInOut({{ $asset->id }})" class="btn btn-warning">
                            <i class="bi bi-box-arrow-in-left"></i> Checkin Aset
                        </button>
                    @endif
                    
                    @if($asset->status === 'available' || $asset->status === 'in_use')
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                            <i class="bi bi-wrench"></i> Kirim Maintenance
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informasi Detail -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-laptop"></i> Informasi Aset
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="35%">Kode Aset</th>
                        <td><code>{{ $asset->asset_code }}</code></td>
                    </tr>
                    <tr>
                        <th>Nama Aset</th>
                        <td>{{ $asset->name }}</td>
                    </tr>
                    <tr>
                        <th>Serial Number</th>
                        <td>{{ $asset->serial_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Brand</th>
                        <td>{{ $asset->brand ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Model</th>
                        <td>{{ $asset->model ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $asset->notes ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Informasi Finansial -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-currency-dollar"></i> Informasi Finansial
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th width="45%">Tanggal Pembelian</th>
                        <td>{{ $asset->purchase_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td>{{ $asset->formatted_purchase_price }}</td>
                    </tr>
                    <tr>
                        <th>Nilai Residu</th>
                        <td>{{ $asset->formatted_residual_value ?? 'Rp ' . number_format($asset->residual_value, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Nilai Saat Ini</th>
                        <td><span class="fw-bold text-primary">{{ $asset->formatted_current_value }}</span></td>
                    </tr>
                    <tr>
                        <th>Total Penyusutan</th>
                        <td>
                            Rp {{ number_format($asset->purchase_price - $asset->current_value, 0, ',', '.') }}
                            <small class="text-muted">({{ $asset->depreciation_percentage }}%)</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Penyusutan per Bulan</th>
                        <td>Rp {{ number_format($asset->calculateMonthlyDepreciation(), 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance History -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-wrench"></i> Riwayat Maintenance
                </h6>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                    <i class="bi bi-plus"></i> Tambah Maintenance
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Teknisi</th>
                                <th>Tindakan</th>
                                <th>Biaya</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($asset->maintenances as $maintenance)
                            <tr>
                                <td>{{ $maintenance->maintenance_date ? $maintenance->maintenance_date->format('d M Y') : '-' }}</td>
                                <td>{{ $maintenance->technician ?? '-' }}</td>
                                <td>{{ Str::limit($maintenance->description ?? $maintenance->actions_performed ?? '-', 50) }}</td>
                                <td>{{ $maintenance->formatted_cost ?? 'Rp ' . number_format($maintenance->cost ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusBadges = [
                                            'pending' => 'warning',
                                            'in_progress' => 'info',
                                            'completed' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusBadges[$maintenance->status] ?? 'secondary' }}">
                                        {{ ucfirst($maintenance->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-tools display-6 text-muted"></i>
                                    <p class="text-muted mt-2">Belum ada riwayat maintenance</p>
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

<!-- Audit Trail -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="bi bi-clock-history"></i> Audit Trail
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Aksi</th>
                                <th>User</th>
                                <th>Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditLogs ?? [] as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    @php
                                        $actionIcons = [
                                            'create' => 'plus-circle text-success',
                                            'update' => 'pencil text-warning',
                                            'delete' => 'trash text-danger',
                                            'checkin' => 'box-arrow-in-left text-info',
                                            'checkout' => 'box-arrow-right text-primary',
                                            'scan' => 'upc-scan text-secondary',
                                        ];
                                    @endphp
                                    <i class="bi bi-{{ $actionIcons[$log->action] ?? 'info-circle' }}"></i>
                                    {{ ucfirst($log->action) }}
                                </td>
                                <td>{{ $log->user->name ?? '-' }}</td>
                                <td>
                                    @if($log->action == 'update' && $log->old_values && $log->new_values)
                                        <button class="btn btn-sm btn-link p-0" onclick="showChanges({{ json_encode($log->old_values) }}, {{ json_encode($log->new_values) }})">
                                            <i class="bi bi-eye"></i> Lihat
                                        </button>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">
                                    Belum ada aktivitas
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

<!-- Modal Tambah Maintenance -->
<div class="modal fade" id="maintenanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-wrench"></i> Kirim Maintenance
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.maintenances.store') }}" method="POST">
                @csrf
                <input type="hidden" name="asset_id" value="{{ $asset->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Maintenance <span class="text-danger">*</span></label>
                        <input type="date" name="maintenance_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teknisi</label>
                        <input type="text" name="technician" class="form-control" placeholder="Nama teknisi">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya</label>
                        <input type="number" name="cost" class="form-control" placeholder="0" step="1000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">Dalam Proses</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
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
function toggleCheckInOut(assetId) {
    $.ajax({
        url: '/admin/assets/' + assetId + '/toggle-checkinout',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Terjadi kesalahan');
        }
    });
}

function generateBarcode(assetId) {
    $.ajax({
        url: '/admin/assets/' + assetId + '/generate-barcode',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message);
            }
        }
    });
}

function showChanges(oldValues, newValues) {
    let html = '<table class="table table-sm">';
    for (let key in newValues) {
        if (oldValues[key] !== newValues[key]) {
            html += `
                <tr>
                    <td><strong>${key}</strong></td>
                    <td><span class="text-danger">${oldValues[key] || '-'}</span></td>
                    <td><i class="bi bi-arrow-right"></i></td>
                    <td><span class="text-success">${newValues[key] || '-'}</span></td>
                </tr>
            `;
        }
    }
    html += '</table>';
    
    $('#changesModal .modal-body').html(html);
    $('#changesModal').modal('show');
}
</script>
@endpush

<!-- Changes Modal -->
<div class="modal fade" id="changesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Perubahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>