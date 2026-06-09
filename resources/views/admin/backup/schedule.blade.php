@extends('admin.layouts.app')

@section('title', 'Schedule Backup')
@section('page-title', 'Pengaturan Backup Otomatis')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.backup.index') }}">Backup Database</a></li>
    <li class="breadcrumb-item active">Schedule</li>
@endsection

@section('header-actions')
    <a href="{{ route('admin.backup.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-calendar-clock"></i> Pengaturan Backup Otomatis
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.backup.schedule.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="is_active" value="1" {{ $settings->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktifkan Backup Otomatis
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="frequency" class="form-label">Frekuensi Backup</label>
                        <select name="frequency" id="frequency" class="form-select" required>
                            <option value="daily" {{ $settings->frequency == 'daily' ? 'selected' : '' }}>Setiap Hari</option>
                            <option value="weekly" {{ $settings->frequency == 'weekly' ? 'selected' : '' }}>Setiap Minggu (Minggu)</option>
                            <option value="monthly" {{ $settings->frequency == 'monthly' ? 'selected' : '' }}>Setiap Bulan (Tanggal 1)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="time" class="form-label">Waktu Backup</label>
                        <input type="time" name="time" id="time" class="form-control" value="{{ $settings->time ?? '00:00' }}" required>
                        <small class="text-muted">Waktu dalam format 24 jam (server time)</small>
                    </div>

                    <div class="mb-3">
                        <label for="keep_backups" class="form-label">Jumlah Backup yang Disimpan</label>
                        <input type="number" name="keep_backups" id="keep_backups" class="form-control" value="{{ $settings->keep_backups ?? 30 }}" min="1" max="100" required>
                        <small class="text-muted">Backup lama akan otomatis dihapus</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Informasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Backup hanya menyimpan database (tanpa file upload)</li>
                            <li>Pastikan server memiliki cron job untuk scheduler Laravel</li>
                            <li>Backup disimpan di storage/app/backups</li>
                        </ul>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-terminal"></i> Setup Cron Job
                </h3>
            </div>
            <div class="card-body">
                <h5>Untuk Server Linux/Unix:</h5>
                <div class="alert alert-secondary">
                    <code class="text-dark">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
                    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="copyToClipboard(this)">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </div>
                
                <h5 class="mt-4">Untuk Shared Hosting (cPanel):</h5>
                <ol>
                    <li>Masuk ke cPanel</li>
                    <li>Cari "Cron Jobs"</li>
                    <li>Set command: <code>php {{ base_path() }}/artisan schedule:run</code></li>
                    <li>Set waktu: Once per minute (* * * * *)</li>
                </ol>

                <div class="alert alert-success mt-3">
                    <i class="bi bi-check-circle"></i>
                    <strong>Catatan:</strong> Schedule backup akan berjalan sesuai pengaturan setelah cron job terpasang.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(button) {
    var code = button.previousElementSibling;
    var text = code.innerText;
    
    navigator.clipboard.writeText(text).then(function() {
        var originalText = button.innerHTML;
        button.innerHTML = '<i class="bi bi-check"></i> Copied!';
        setTimeout(function() {
            button.innerHTML = originalText;
        }, 2000);
    });
}
</script>
@endsection