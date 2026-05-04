@extends('admin.layouts.app')

@section('title', 'Detail Peminjaman - ' . $loan->loan_code)
@section('page-title', 'Detail Peminjaman')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.loans.index') }}">Peminjaman</a></li>
    <li class="breadcrumb-item active">{{ $loan->loan_code }}</li>
@endsection

@section('header-actions')
    <div class="d-flex gap-2">
        <a href="{{ route('admin.loans.print', $loan) }}" class="btn btn-outline-secondary" target="_blank">
            <i class="bi bi-printer"></i> Cetak Bukti
        </a>
        
        @if($loan->status === 'pending')
            <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Setujui
                </button>
            </form>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle"></i> Tolak
            </button>
        @endif
        
        @if(in_array($loan->status, ['active', 'overdue']))
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#returnModal">
                <i class="bi bi-box-arrow-in-left"></i> Kembalikan Aset
            </button>
        @endif
        
        @if(in_array($loan->status, ['pending', 'approved']))
            <form action="{{ route('admin.loans.cancel', $loan) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-secondary" onclick="return confirm('Batalkan peminjaman?')">
                    <i class="bi bi-slash-circle"></i> Batalkan
                </button>
            </form>
        @endif
        
        <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="row g-3">
    
    {{-- KOLOM KIRI: Info Peminjaman --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-1"></i>Informasi Peminjaman</h6>
                <span class="badge bg-{{ $loan->status_badge }} fs-6">{{ $loan->status_label }}</span>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Kode Peminjaman</span>
                        <code class="bg-light px-2 py-1 rounded">{{ $loan->loan_code }}</code>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Aset</span>
                        <a href="{{ route('admin.assets.show', $loan->asset) }}" class="fw-semibold text-decoration-none">
                            {{ $loan->asset->name }}
                            <small class="text-muted d-block text-end">{{ $loan->asset->asset_code }}</small>
                        </a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Kategori Aset</span>
                        <span>{{ $loan->asset->category->name ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Peminjam</span>
                        <span class="fw-semibold">
                            <i class="bi bi-person me-1"></i>{{ $loan->user->name }}
                            <small class="text-muted d-block text-end">{{ $loan->user->email }}</small>
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Tanggal Pinjam</span>
                        <span class="fw-semibold">{{ $loan->loan_date->format('d M Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Estimasi Kembali</span>
                        <span class="fw-semibold">{{ $loan->expected_return_date->format('d M Y') }}</span>
                    </li>
                    @if($loan->actual_return_date)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Tanggal Kembali</span>
                        <span class="fw-semibold text-success">
                            {{ $loan->actual_return_date->format('d M Y') }}
                            @if($loan->actual_return_date->gt($loan->expected_return_date))
                                <span class="badge bg-danger ms-1">Terlambat</span>
                            @else
                                <span class="badge bg-success ms-1">Tepat Waktu</span>
                            @endif
                        </span>
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Tujuan</span>
                        <span>{{ $loan->purpose ?? '-' }}</span>
                    </li>
                    <li class="list-group-item">
                        <span class="text-muted d-block mb-1">Kondisi Sebelum Pinjam</span>
                        <p class="mb-0">{{ $loan->condition_before ?? '-' }}</p>
                    </li>
                    @if($loan->condition_after)
                    <li class="list-group-item">
                        <span class="text-muted d-block mb-1">Kondisi Setelah Kembali</span>
                        <p class="mb-0">{{ $loan->condition_after }}</p>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    
    {{-- KOLOM KANAN: Status & Denda --}}
    <div class="col-lg-4">
        
        {{-- Approval Info --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="bi bi-check-circle text-primary me-1"></i>Approval</h6>
            </div>
            <div class="card-body">
                @if($loan->approver)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar bg-success bg-opacity-10 text-success">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $loan->approver->name }}</div>
                            <small class="text-muted">{{ $loan->approver->email }}</small>
                        </div>
                    </div>
                    <small class="text-muted">Disetujui pada {{ $loan->updated_at->format('d M Y H:i') }}</small>
                @elseif($loan->status === 'pending')
                    <div class="text-center py-2 text-warning">
                        <i class="bi bi-hourglass-split fs-3 d-block mb-2"></i>
                        <p class="mb-0">Menunggu approval</p>
                    </div>
                @else
                    <div class="text-center py-2 text-muted">
                        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                        <p class="mb-0">-</p>
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Timeline Status --}}
        <div class="card mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-1"></i>Timeline</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled timeline mb-0">
                    <li class="mb-3 d-flex gap-2">
                        <div class="bg-primary rounded-circle p-1" style="width: 24px; height: 24px; text-align: center; line-height: 16px;">
                            <i class="bi bi-plus text-white" style="font-size: 12px;"></i>
                        </div>
                        <div>
                            <small class="fw-semibold">Dibuat</small>
                            <br><small class="text-muted">{{ $loan->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </li>
                    
                    @if($loan->status !== 'pending')
                    <li class="mb-3 d-flex gap-2">
                        <div class="bg-{{ $loan->status === 'rejected' ? 'danger' : 'success' }} rounded-circle p-1" style="width: 24px; height: 24px; text-align: center; line-height: 16px;">
                            <i class="bi bi-{{ $loan->status === 'rejected' ? 'x' : 'check' }} text-white" style="font-size: 12px;"></i>
                        </div>
                        <div>
                            <small class="fw-semibold">{{ $loan->status === 'rejected' ? 'Ditolak' : 'Disetujui' }}</small>
                            <br><small class="text-muted">{{ $loan->updated_at->format('d M Y H:i') }}</small>
                        </div>
                    </li>
                    @endif
                    
                    @if($loan->actual_return_date)
                    <li class="d-flex gap-2">
                        <div class="bg-success rounded-circle p-1" style="width: 24px; height: 24px; text-align: center; line-height: 16px;">
                            <i class="bi bi-box-arrow-in-left text-white" style="font-size: 12px;"></i>
                        </div>
                        <div>
                            <small class="fw-semibold">Dikembalikan</small>
                            <br><small class="text-muted">{{ $loan->actual_return_date->format('d M Y') }}</small>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        
        {{-- Denda --}}
        @if($loan->fine_amount > 0)
        <div class="card border-danger">
            <div class="card-header bg-danger bg-opacity-10 text-danger">
                <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-1"></i>Denda Keterlambatan</h6>
            </div>
            <div class="card-body text-center">
                <h4 class="text-danger fw-bold mb-1">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</h4>
                <small class="text-muted">
                    @php $daysLate = $loan->actual_return_date->diffInDays($loan->expected_return_date) @endphp
                    {{ $daysLate }} hari keterlambatan × Rp 10.000
                </small>
                <div class="mt-2">
                    @if($loan->fine_paid)
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Sudah Dibayar</span>
                    @else
                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Belum Dibayar</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        {{-- Overdue Warning --}}
        @if($loan->isOverdue())
        <div class="card border-danger mt-3">
            <div class="card-body text-center text-danger">
                <i class="bi bi-exclamation-triangle fs-2 d-block mb-2"></i>
                <strong>Aset terlambat dikembalikan!</strong>
                <p class="mb-0 small">{{ now()->diffInDays($loan->expected_return_date) }} hari melebihi batas</p>
            </div>
        </div>
        @endif
        
        {{-- Catatan --}}
        @if($loan->notes)
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h6 class="mb-0 fw-bold"><i class="bi bi-sticky me-1"></i>Catatan</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $loan->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- MODAL REJECT --}}
@if($loan->status === 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.loans.reject', $loan) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-1"></i>Tolak Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menolak peminjaman <strong>{{ $loan->loan_code }}</strong>?</p>
                    <label class="form-label">Alasan Penolakan</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Tulis alasan penolakan..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- MODAL RETURN --}}
@if(in_array($loan->status, ['active', 'overdue']))
@include('admin.loans._return_modal')
@endif

@endsection

@push('styles')
<style>
.timeline { position: relative; }
.timeline::before {
    content: '';
    position: absolute;
    left: 11px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}
.timeline li { position: relative; z-index: 1; }
</style>
@endpush