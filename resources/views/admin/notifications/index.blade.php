@extends('admin.layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Semua Notifikasi')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-bell me-1"></i>Notifikasi</h5>
        @if($unreadCount > 0)
            <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn">
                <i class="bi bi-check-all me-1"></i>Tandai Semua Dibaca
            </button>
        @endif
    </div>
    <div class="card-body p-0">
        @forelse($notifications as $notification)
        <a href="{{ $notification->link ?? '#' }}" 
           class="list-group-item list-group-item-action d-flex gap-3 px-3 py-2 {{ $notification->is_read ? '' : 'bg-light' }}"
           data-id="{{ $notification->id }}">
            <div class="bg-{{ $notification->color }} bg-opacity-10 rounded-circle p-2" 
                 style="width: 40px; height: 40px; text-align: center;">
                <i class="bi bi-{{ $notification->icon }} text-{{ $notification->color }}"></i>
            </div>
            <div style="flex: 1;">
                <div class="d-flex justify-content-between">
                    <strong>{{ $notification->title }}</strong>
                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                </div>
                <p class="mb-0 text-muted">{!! $notification->message !!}</p>
            </div>
            @if(!$notification->is_read)
                <span class="badge bg-primary rounded-pill align-self-start">Baru</span>
            @endif
        </a>
        @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
            <h6>Tidak ada notifikasi</h6>
        </div>
        @endforelse
    </div>
</div>

{{ $notifications->links() }}
@endsection

@push('scripts')
<script>
$('#markAllReadBtn').click(function() {
    $.post('{{ route("admin.notifications.read-all") }}', {_token: '{{ csrf_token() }}'}, function() {
        location.reload();
    });
});

$('.list-group-item').click(function(e) {
    const id = $(this).data('id');
    $.post(`/admin/notifications/${id}/read`, {_token: '{{ csrf_token() }}'}, function() {
        // Biarkan link berjalan
    });
});
</script>
@endpush