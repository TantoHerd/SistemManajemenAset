<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <style>
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        }
        .auth-left {
            flex: 1;
            background: linear-gradient(135deg, #1e1e2f 0%, #2d2d44 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
            animation: rotate 30s linear infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .auth-left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
        }
        .auth-left-content .logo-img {
            width: 100px;
            height: 100px;
            border-radius: 20px;
            background: rgba(255,255,255,0.1);
            padding: 15px;
            margin-bottom: 20px;
            object-fit: contain;
        }
        .auth-left-content h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .auth-left-content .company-name {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-bottom: 20px;
        }
        .auth-left-content .features {
            text-align: left;
            margin-top: 30px;
        }
        .auth-left-content .features li {
            margin-bottom: 12px;
            font-size: 0.9rem;
            opacity: 0.85;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .auth-left-content .features li i {
            color: #4cc9f0;
            font-size: 1.2rem;
        }
        .auth-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: white;
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
        }
        .auth-card .card-header-auth {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-card .card-header-auth h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e1e2f;
            margin-bottom: 5px;
        }
        .auth-card .card-header-auth p {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }
        .auth-card .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #333;
        }
        .auth-card .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e8ecf1;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .auth-card .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }
        .auth-card .btn-primary {
            border-radius: 10px;
            padding: 13px;
            font-weight: 600;
            font-size: 1rem;
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            border: none;
            transition: all 0.3s;
        }
        .auth-card .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(67, 97, 238, 0.3);
        }
        .auth-card .forgot-link {
            color: #4361ee;
            font-weight: 500;
            text-decoration: none;
        }
        .auth-card .forgot-link:hover {
            text-decoration: underline;
        }
        .input-group-merge .input-group-text {
            border-radius: 0 10px 10px 0;
            border: 2px solid #e8ecf1;
            border-left: none;
            background: white;
            cursor: pointer;
        }
        .input-group-merge .form-control {
            border-radius: 10px 0 0 10px;
            border-right: none;
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .auth-left {
                display: none;
            }
            .auth-right {
                padding: 20px;
            }
            .auth-card {
                padding: 30px 25px;
                border-radius: 15px;
            }
            .auth-card .card-header-auth h3 {
                font-size: 1.3rem;
            }
        }
        @media (max-width: 480px) {
            .auth-card {
                padding: 25px 18px;
            }
            .auth-card .btn-primary {
                padding: 11px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <!-- LEFT: Branding -->
        <div class="auth-left">
            <div class="auth-left-content">
                @php
                    $logo = App\Models\Setting::where('key', 'company_logo')->value('value');
                    $companyName = App\Models\Setting::where('key', 'company_name')->value('value') ?? 'PT. NAMA PERUSAHAAN';
                    $systemName = App\Models\Setting::where('key', 'system_name')->value('value') ?? 'SIMASET';
                @endphp
                
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="logo-img">
                @else
                    <div class="logo-img d-flex align-items-center justify-content-center" style="font-size: 2.5rem;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                @endif
                
                <h2>{{ $systemName }}</h2>
                <p class="company-name">{{ $companyName }}</p>
                
                <ul class="features list-unstyled">
                    <li><i class="bi bi-check-circle-fill"></i> Manajemen Aset IT Terintegrasi</li>
                    <li><i class="bi bi-qr-code"></i> QR Code & Tracking Real-time</li>
                    <li><i class="bi bi-graph-up"></i> Dashboard & Laporan Analitik</li>
                    <li><i class="bi bi-shield-check"></i> Keamanan & Multi-level Akses</li>
                </ul>
            </div>
        </div>
        
        <!-- RIGHT: Login Form -->
        <div class="auth-right">
            <div class="auth-card">
                <div class="card-header-auth">
                    <i class="bi bi-person-circle fs-1 text-primary d-block mb-2"></i>
                    <h3>Selamat Datang</h3>
                    <p>Silakan login untuk melanjutkan</p>
                </div>
                
                {{ $slot }}
            </div>
        </div>
    </div>
    
    @include('partials.scripts')
</body>
</html>