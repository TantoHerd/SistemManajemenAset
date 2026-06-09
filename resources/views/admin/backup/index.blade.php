@extends('admin.layouts.app')

@section('title', 'Backup Database')
@section('page-title', 'Manajemen Backup Database')

@section('breadcrumb')
    <li class="breadcrumb-item active">Backup Database</li>
@endsection

@section('header-actions')
    <div class="btn-group">
        <button type="button" class="btn btn-success" id="createBackupBtn">
            <i class="bi bi-plus-lg"></i> Backup Now
        </button>
        <a href="{{ route('admin.backup.schedule') }}" class="btn btn-info">
            <i class="bi bi-clock"></i> Schedule Settings
        </a>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th>Ukuran</th>
                        <th>Tanggal Backup</th>
                        <th>Tipe</th>
                        <th width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $index => $backup)
                    <tr id="backup-row-{{ $index }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <i class="bi bi-file-zip"></i> {{ $backup['name'] }}
                            @if($loop->first)
                                <span class="badge bg-success ms-1">Terbaru</span>
                            @endif
                        </td>
                        <td>{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</td>
                        <td>{{ date('d/m/Y H:i:s', $backup['last_modified']) }}</td>
                        <td>
                            @if($backup['type'] == 'Compressed')
                                <span class="badge bg-info">Compressed</span>
                            @else
                                <span class="badge bg-secondary">SQL</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary btn-download" data-file="{{ $backup['name'] }}">
                                    <i class="bi bi-download"></i> Download
                                </button>
                                <button class="btn btn-sm btn-warning btn-restore" data-file="{{ $backup['name'] }}">
                                    <i class="bi bi-arrow-repeat"></i> Restore
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" data-file="{{ $backup['name'] }}" data-index="{{ $index }}">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2 mb-0">Belum ada backup database</p>
                            <small class="text-muted">Klik tombol "Backup Now" untuk membuat backup pertama</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(count($backups) > 0)
        <div class="mt-3 alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Informasi:</strong>
            <ul class="mb-0 mt-2">
                <li>Total backup: <strong>{{ count($backups) }}</strong> file</li>
                <li>Total storage: <strong>{{ number_format($totalSize / 1024 / 1024, 2) }} MB</strong></li>
                <li>Backup terbaru: <strong>{{ $backups[0]['name'] ?? '-' }}</strong></li>
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Create backup dengan konfirmasi
    $('#createBackupBtn').click(function() {
        Swal.fire({
            title: 'Buat Backup Database?',
            text: "Proses backup akan memakan waktu beberapa saat tergantung ukuran database.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Backup Sekarang!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Sedang memproses...',
                    text: 'Mohon tunggu, backup sedang dibuat.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '{{ route("admin.backup.create") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Backup database berhasil dibuat.',
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat backup.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

    // Download backup (langsung download, tanpa konfirmasi)
    $('.btn-download').click(function() {
        var file = $(this).data('file');
        window.location.href = '{{ route("admin.backup.download", "") }}/' + file;
    });

    // Restore backup dengan konfirmasi
    $('.btn-restore').click(function() {
        var file = $(this).data('file');
        
        Swal.fire({
            title: 'Restore Database?',
            html: `Anda akan merestore database dari file: <strong>${file}</strong><br><br>
                   <span style="color: #ffc107;">⚠️ Peringatan!</span><br>
                   Restore akan mengganti SEMUA data saat ini dengan data dari backup.<br>
                   Proses ini tidak dapat dibatalkan!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Restore!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Sedang restore...',
                    text: 'Mohon tunggu, database sedang direstore.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '{{ route("admin.backup.restore") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        backup_file: file
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Database berhasil direstore.',
                            icon: 'success',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat restore.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });

    // Delete backup dengan konfirmasi (style seperti hapus aset)
    $('.btn-delete').click(function() {
        var file = $(this).data('file');
        var index = $(this).data('index');
        
        Swal.fire({
            title: 'Hapus Backup?',
            text: `Apakah Anda yakin ingin menghapus file backup "${file}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.backup.destroy", "") }}/' + file,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: 'File backup berhasil dihapus.',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message || 'Gagal menghapus backup',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus backup.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush