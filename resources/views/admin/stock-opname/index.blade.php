@extends('admin.layouts.app')

@section('title', 'Stock Opname')
@section('page-title', 'Stock Opname')

@section('header-actions')
    <a href="{{ route('admin.stock-opname.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle"></i> Buat Sesi Baru
    </a>
@endsection

@section('content')
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama Sesi</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Total Aset</th>
                        <th>Dibuat</th>
                        <th width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $session->name }}</strong>
                            @if($session->notes)
                                <br><small class="text-muted">{{ Str::limit($session->notes, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $session->location->name ?? 'Semua Lokasi' }}</td>
                        <td>
                            @if($session->status == 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @elseif($session->status == 'in_progress')
                                <span class="badge bg-primary">Berjalan</span>
                            @elseif($session->status == 'completed')
                                <span class="badge bg-success">Selesai</span>
                            @else
                                <span class="badge bg-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            <div class="progress" style="height: 8px; width: 100px;">
                                <div class="progress-bar" style="width: {{ $session->progress }}%"></div>
                            </div>
                            <small>{{ $session->progress }}%</small>
                        </td>
                        <td>{{ $session->items->count() }} aset</td>
                        <td>{{ $session->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('admin.stock-opname.show', $session) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($session->status == 'draft')
                                    <form action="{{ route('admin.stock-opname.start', $session) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mulai stock opname?')">
                                            <i class="bi bi-play-fill"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($session->status == 'completed')
                                    <a href="{{ route('admin.stock-opname.report', $session) }}" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.stock-opname.destroy', $session) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus sesi ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-box-seam fs-1 text-muted"></i>
                                <p class="mt-2 mb-0">Belum ada sesi stock opname</p>
                                <a href="{{ route('admin.stock-opname.create') }}" class="btn btn-sm btn-primary mt-2">
                                    Buat Sesi Baru
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">
    {{ $sessions->links() }}
</div>
@endsection