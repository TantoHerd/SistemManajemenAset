@extends('admin.layouts.app')

@section('title', 'Konfigurasi Sistem')
@section('page-title', 'Konfigurasi Sistem')

@section('breadcrumb')
    <li class="breadcrumb-item active">Konfigurasi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#general">
                    <i class="bi bi-building"></i> Umum
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#logo">
                    <i class="bi bi-image"></i> Logo & Favicon
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#preferences">
                    <i class="bi bi-gear"></i> Preferensi
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Tab Umum -->
            <div class="tab-pane fade show active" id="general">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Pengaturan Umum</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.general') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Perusahaan</label>
                                    <input type="text" name="company_name" class="form-control" 
                                           value="{{ $settings['company_name'] }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Sistem</label>
                                    <input type="text" name="system_name" class="form-control" 
                                           value="{{ $settings['system_name'] }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Perusahaan</label>
                                    <input type="email" name="company_email" class="form-control" 
                                           value="{{ $settings['company_email'] }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" name="company_phone" class="form-control" 
                                           value="{{ $settings['company_phone'] }}">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="company_address" rows="2" class="form-control">{{ $settings['company_address'] }}</textarea>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Pengaturan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab Logo & Favicon -->
            <div class="tab-pane fade" id="logo">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Logo & Favicon</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            
                            <div class="row">
                                <!-- Logo Section -->
                                <div class="col-md-6 text-center mb-3">
                                    <label class="form-label fw-bold">Logo Perusahaan</label>
                                    <div class="border p-3 rounded mb-2 bg-light">
                                        @php
                                            $logoPath = $settings['company_logo'];
                                            $logoUrl = $logoPath && file_exists(public_path('storage/' . $logoPath)) 
                                                ? asset('storage/' . $logoPath) 
                                                : null;
                                        @endphp
                                        @if($logoUrl)
                                            <img src="{{ $logoUrl }}" 
                                                 alt="Logo" 
                                                 style="max-width: 200px; max-height: 100px;">
                                        @else
                                            <div class="text-muted py-4">
                                                <i class="bi bi-image display-1"></i>
                                                <p>Belum ada logo</p>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="company_logo" class="form-control" accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>
                                    @if($logoUrl)
                                        <div class="mt-2">
                                            <a href="{{ route('admin.settings.logo.remove') }}" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Hapus logo?')">
                                                <i class="bi bi-trash"></i> Hapus Logo
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Favicon Section -->
                                <div class="col-md-6 text-center mb-3">
                                    <label class="form-label fw-bold">Favicon</label>
                                    <div class="border p-3 rounded mb-2 bg-light">
                                        @php
                                            $faviconPath = $settings['system_favicon'];
                                            $faviconUrl = $faviconPath && file_exists(storage_path('app/public/' . $faviconPath)) 
                                                ? asset('storage/' . $faviconPath) 
                                                : null;
                                        @endphp
                                        @if($faviconUrl)
                                            <img src="{{ $faviconUrl }}" 
                                                 alt="Favicon" 
                                                 style="width: 64px; height: 64px;">
                                        @else
                                            <div class="text-muted py-4">
                                                <i class="bi bi-file-image display-1"></i>
                                                <p>Belum ada favicon</p>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="system_favicon" class="form-control" accept="image/*">
                                    <small class="text-muted">Format: ICO, PNG (Max 512KB)</small>
                                    @if($faviconUrl)
                                        <div class="mt-2">
                                            <a href="{{ route('admin.settings.favicon.remove') }}" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Hapus favicon?')">
                                                <i class="bi bi-trash"></i> Hapus Favicon
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Upload Logo/Favicon
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tab Preferensi -->
            <div class="tab-pane fade" id="preferences">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Preferensi Sistem</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.preferences') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pengingat Maintenance (Hari)</label>
                                    <input type="number" name="maintenance_reminder_days" class="form-control" 
                                           value="{{ $settings['maintenance_reminder_days'] }}" required>
                                    <small class="text-muted">Pengingat akan dikirim H-{value} sebelum jadwal</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Format Tanggal</label>
                                    <select name="date_format" class="form-select">
                                        <option value="d/m/Y" {{ $settings['date_format'] == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                        <option value="m/d/Y" {{ $settings['date_format'] == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                        <option value="Y-m-d" {{ $settings['date_format'] == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Simbol Mata Uang</label>
                                    <input type="text" name="currency_symbol" class="form-control" 
                                           value="{{ $settings['currency_symbol'] }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Zona Waktu</label>
                                    <select name="timezone" class="form-select">
                                        @foreach([
                                            'Asia/Jakarta' => 'WIB - Jakarta (GMT+7)',
                                            'Asia/Makassar' => 'WITA - Makassar (GMT+8)',
                                            'Asia/Jayapura' => 'WIT - Jayapura (GMT+9)',
                                            'Asia/Singapore' => 'Singapore (GMT+8)',
                                            'Asia/Kuala_Lumpur' => 'Kuala Lumpur (GMT+8)',
                                        ] as $tz => $label)
                                            <option value="{{ $tz }}" {{ ($settings['timezone'] ?? 'Asia/Jakarta') == $tz ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Preferensi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- VCard QR Code -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-qr-code me-1"></i>QR Code Kontak (VCard)</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted small mb-3">
                        Scan QR ini dengan kamera HP untuk langsung menyimpan kontak perusahaan.<br>
                        Data diambil dari pengaturan perusahaan di atas.
                    </p>
                    
                    <div class="bg-light d-inline-block p-3 rounded-3 mb-3">
                        <img src="{{ route('admin.settings.vcard-qr') }}" alt="VCard QR" style="width: 180px; height: 180px;">
                    </div>
                    
                    <div>
                        <a href="{{ route('admin.settings.vcard-qr') }}" class="btn btn-primary btn-sm" download="vcard-qr.svg">
                            <i class="bi bi-download me-1"></i>Download QR Code
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <a href="{{ route('admin.settings.business-card') }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-credit-card me-1"></i>Lihat Kartu Nama Digital
                </a>
            </div>
        </div>
    </div>
</div>
@endsection