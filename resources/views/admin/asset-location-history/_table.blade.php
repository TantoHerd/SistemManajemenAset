<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Lokasi Lama</th>
                <th>Lokasi Baru</th>
                <th>Alasan</th>
                <th>Diubah Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($histories as $history)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $history->created_at->format('d/m/Y H:i:s') }}</td>
                <td>{{ $history->old_location_name ?? '-' }}</td>
                <td>
                    @if($history->new_location_name)
                        <span class="text-success">{{ $history->new_location_name }}</span>
                    @else
                        <span class="text-danger">-</span>
                    @endif
                </td>
                <td>{{ $history->reason ?? '-' }}</td>
                <td>{{ $history->changer->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-2">Belum ada history perpindahan</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($histories) && method_exists($histories, 'links'))
    <div class="mt-3">
        {{ $histories->links() }}
    </div>
@endif