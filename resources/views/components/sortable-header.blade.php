@props(['column', 'label', 'current' => null, 'direction' => null])

<a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $current === $column && $direction === 'asc' ? 'desc' : 'asc']) }}" 
   class="text-decoration-none text-dark d-flex align-items-center gap-1 sortable-header">
    {{ $label }}
    @if($current === $column)
        <i class="bi bi-sort-{{ $direction === 'asc' ? 'up' : 'down' }} text-primary"></i>
    @else
        <i class="bi bi-arrow-down-up text-muted opacity-25"></i>
    @endif
</a>