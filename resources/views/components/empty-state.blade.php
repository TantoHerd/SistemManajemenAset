<div class="text-center py-5 text-muted fade-in">
    <i class="bi bi-{{ $icon ?? 'inbox' }} fs-1 d-block mb-2"></i>
    <h6>{{ $title ?? 'Tidak ada data' }}</h6>
    <p class="small">{{ $description ?? '' }}</p>
    @if($action ?? false)
        <a href="{{ $action }}" class="btn btn-sm btn-primary mt-2">
            <i class="bi bi-plus-circle me-1"></i>{{ $actionText ?? 'Tambah' }}
        </a>
    @endif
</div>