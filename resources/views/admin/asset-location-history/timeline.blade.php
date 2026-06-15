@extends('admin.layouts.app')

@section('title', 'Timeline - ' . $asset->name)
@section('page-title', 'Timeline Perpindahan: ' . $asset->name)

@section('content')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 40px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }
    .timeline-item {
        position: relative;
        padding-left: 80px;
        margin-bottom: 30px;
    }
    .timeline-badge {
        position: absolute;
        left: 20px;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        border: 2px solid;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }
    .timeline-content {
        background: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-left: 3px solid;
    }
</style>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">{{ $asset->name }}</h5>
                <small class="text-muted">Kode: {{ $asset->asset_code }} | Lokasi Saat Ini: 
                    <strong>{{ $asset->location->name ?? '-' }}</strong>
                </small>
            </div>
            <a href="{{ route('admin.assets.show', $asset) }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($histories->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-map fs-1 text-muted"></i>
                <p class="mt-2">Belum ada riwayat perpindahan untuk aset ini</p>
            </div>
        @else
            <div class="timeline">
                @foreach($histories as $history)
                <div class="timeline-item">
                    <div class="timeline-badge" style="border-color: {{ $loop->first ? '#10b981' : '#3b82f6' }}; background: {{ $loop->first ? '#10b981' : '#3b82f6' }}10;">
                        <i class="bi bi-arrow-repeat" style="color: {{ $loop->first ? '#10b981' : '#3b82f6' }};"></i>
                    </div>
                    <div class="timeline-content" style="border-left-color: {{ $loop->first ? '#10b981' : '#3b82f6' }};">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>{{ $history->created_at->format('d F Y H:i:s') }}</strong>
                            @if($loop->first)
                                <span class="badge bg-success">Perpindahan Terakhir</span>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-5 text-end">
                                <span class="text-muted">Dari:</span>
                                <strong>{{ $history->old_location_name ?? '-' }}</strong>
                            </div>
                            <div class="col-md-2 text-center">
                                <i class="bi bi-arrow-right-short fs-4"></i>
                            </div>
                            <div class="col-md-5">
                                <span class="text-muted">Ke:</span>
                                <strong class="text-success">{{ $history->new_location_name ?? '-' }}</strong>
                            </div>
                        </div>
                        @if($history->reason)
                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted">Alasan: {{ $history->reason }}</small>
                        </div>
                        @endif
                        @if($history->notes)
                        <div class="mt-1">
                            <small class="text-muted">Catatan: {{ $history->notes }}</small>
                        </div>
                        @endif
                        <div class="mt-2">
                            <small class="text-muted">Diubah oleh: {{ $history->changer->name ?? 'System' }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection