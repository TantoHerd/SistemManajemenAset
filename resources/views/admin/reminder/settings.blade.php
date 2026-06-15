@extends('admin.layouts.app')

@section('title', 'Pengaturan Reminder Maintenance')
@section('page-title', 'Pengaturan Reminder Maintenance')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-bell me-2"></i> Pengaturan Reminder
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.reminder.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" 
                                   {{ $settings->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="isActive">
                                Aktifkan Reminder Maintenance
                            </label>
                        </div>
                        <small class="text-muted">Kirim notifikasi otomatis untuk jadwal maintenance</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Waktu Pengingat</label>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-check">
                                    <input type="checkbox" name="reminder_days[]" value="7" 
                                           class="form-check-input"
                                           {{ in_array(7, $settings->reminder_days ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label">H-7 (Minggu sebelumnya)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input type="checkbox" name="reminder_days[]" value="3" 
                                           class="form-check-input"
                                           {{ in_array(3, $settings->reminder_days ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label">H-3 (3 hari sebelumnya)</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check">
                                    <input type="checkbox" name="reminder_days[]" value="1" 
                                           class="form-check-input"
                                           {{ in_array(1, $settings->reminder_days ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label">H-1 (Sehari sebelumnya)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Waktu Kirim</label>
                        <input type="time" name="send_time" class="form-control" 
                               value="{{ $settings->send_time ?? '08:00' }}">
                        <small class="text-muted">Notifikasi akan dikirim pada jam ini setiap hari</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Metode Notifikasi</label>
                        <div class="form-check mb-2">
                            <input type="checkbox" name="system_notification" class="form-check-input" id="systemNotif"
                                   {{ $settings->system_notification ? 'checked' : '' }}>
                            <label class="form-check-label" for="systemNotif">
                                <i class="bi bi-bell"></i> Notifikasi Sistem (Bell)
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="email_notification" class="form-check-input" id="emailNotif"
                                   {{ $settings->email_notification ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailNotif">
                                <i class="bi bi-envelope"></i> Notifikasi Email
                            </label>
                        </div>
                    </div>

                    <hr>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i> Informasi Reminder
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-lightbulb"></i>
                    <strong>Cara Kerja Reminder:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Reminder akan dikirim otomatis setiap hari jam {{ $settings->send_time ?? '08:00' }}</li>
                        <li>Notifikasi dikirim ke user dengan role <strong>Admin</strong> dan <strong>Technician</strong></li>
                        <li>Reminder hanya untuk maintenance dengan status <strong>Dijadwalkan</strong> atau <strong>Proses</strong></li>
                        <li>Setiap reminder hanya dikirim sekali untuk setiap periode pengingat</li>
                    </ul>
                </div>

                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    <strong>Status Saat Ini:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Status: <strong>{{ $settings->is_active ? 'Aktif' : 'Nonaktif' }}</strong></li>
                        <li>Pengingat: <strong>{{ $settings->formatted_days }}</strong></li>
                        <li>Notifikasi Email: <strong>{{ $settings->email_notification ? 'Aktif' : 'Nonaktif' }}</strong></li>
                        <li>Notifikasi Sistem: <strong>{{ $settings->system_notification ? 'Aktif' : 'Nonaktif' }}</strong></li>
                    </ul>
                </div>

                <div class="alert alert-secondary">
                    <i class="bi bi-terminal"></i>
                    <strong>Setup Cron Job (Server):</strong>
                    <pre class="bg-dark text-white p-2 rounded small mt-2">
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1</pre>
                    <small>Pastikan cron job berjalan untuk reminder otomatis</small>
                </div>

                <hr>

                <form action="{{ route('admin.reminder.test') }}" method="POST" class="mt-3">
                    @csrf
                    <label class="form-label fw-bold">Test Kirim Email</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-send"></i> Test
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Reminder Terkirim -->
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-clock-history me-2"></i> Riwayat Reminder Terkirim
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Maintenance</th>
                        <th>Pengingat</th>
                        <th>Dikirim Ke</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $reminders = \App\Models\MaintenanceReminder::with('maintenance')
                            ->latest()
                            ->take(20)
                            ->get();
                    @endphp
                    @forelse($reminders as $reminder)
                    <tr>
                        <td>{{ $reminder->sent_at ? $reminder->sent_at->format('d/m/Y H:i') : '-' }}</td>
                        <td>{{ $reminder->maintenance->title ?? '-' }}</td>
                        <td>H-{{ $reminder->days_before }}</td>
                        <td>{{ $reminder->sent_to ?? '-' }}</td>
                        <td>
                            @if($reminder->status == 'sent')
                                <span class="badge bg-success">Terkirim</span>
                            @else
                                <span class="badge bg-danger">Gagal</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2">Belum ada reminder yang terkirim</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection