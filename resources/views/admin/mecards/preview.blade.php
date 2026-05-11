<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Nama - {{ $mecard->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        @page { size: 90mm 55mm; margin: 0; }
        
        body {
            font-family: 'Segoe UI', 'Georgia', serif;
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .card {
            width: 90mm;
            height: 55mm;
            background: #fff;
            border-radius: 14px;
            display: flex;
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            overflow: visible;
            position: relative;
        }
        
        /* LEFT - Red Panel */
        .card-left {
            width: 34mm;
            background: linear-gradient(160deg, #7F0000 0%, #8B0000 30%, #C62828 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 12px;
            gap: 10px;
            position: relative;
            border-radius: 14px 0 0 14px;
            z-index: 1;
        }
        
        .card-left::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        
        /* CURVE SMOOTH */
        .card-left::after {
            content: '';
            position: absolute;
            right: -10px;
            top: -5px;
            width: 20px;
            height: calc(100% + 10px);
            background: 
                radial-gradient(ellipse at left, #8B0000 60%, transparent 60%) 0 0 / 20px 50%,
                radial-gradient(ellipse at left, #8B0000 60%, transparent 60%) 0 100% / 20px 50%;
            background-repeat: no-repeat;
            z-index: 2;
        }
        
        .card-left .logo-box {
            width: 52px;
            height: 52px;
            background: #fff;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
            z-index: 1;
        }
        
        .card-left .logo-box img {
            width: 80%;
            height: 80%;
            object-fit: contain;
        }
        
        .card-left .qr-box {
            background: #fff;
            border-radius: 10px;
            padding: 6px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.25);
            z-index: 1;
        }
        
        .card-left .qr-box svg {
            width: 22mm;
            height: 22mm;
            display: block;
        }
        
        .card-left .scan-text {
            color: rgba(255,255,255,0.7);
            font-size: 5pt;
            text-transform: uppercase;
            letter-spacing: 3px;
            z-index: 1;
        }
        
        /* RIGHT - Cream Panel */
        .card-right {
            flex: 1;
            padding: 14px 16px 14px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            background: linear-gradient(135deg, #FFFBF5 0%, #FFF8F0 50%, #FFF5EB 100%);
            border-radius: 0 14px 14px 0;
        }
        
        .card-right .name {
            font-size: 11pt;
            font-weight: bold;
            color: #2D1A0E;
            margin-bottom: 1px;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        /* Circle accent di perbatasan */
        .card-left::after {
            content: '';
            position: absolute;
            top: 15px;
            right: -8px;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .card-right .title {
            font-size: 6pt;
            color: #8B0000;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1.5px solid #E8D5C4;
            padding-left: 12px;
        }
        
        .card-right .info-item {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 4px;
            font-size: 7pt;
            color: #5D4037;
        }
        
        .card-right .info-item .icon {
            width: 20px;
            height: 20px;
            background: #FFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #F0E0D0;
        }
        
        /* Company name - kecil di kanan bawah */
        .card-right .company-name {
            position: absolute;
            bottom: 5px;
            right: 7px;
            font-size: 4pt;
            color: #C9B8A8;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        
        /* Pattern dots */
        .card-right::before {
            content: '';
            position: absolute;
            bottom: 20px;
            right: 10px;
            width: 35px;
            height: 35px;
            background-image: radial-gradient(circle, #E8D5C4 1.5px, transparent 1.5px);
            background-size: 7px 7px;
            opacity: 0.4;
        }
        
        /* Print */
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .card { box-shadow: none; border-radius: 0; }
            .actions { display: none !important; }
        }
        
        /* Actions */
        .actions {
            position: fixed;
            bottom: 30px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print { background: #8B0000; color: #fff; }
        .btn-close { background: #ddd; color: #333; }
    </style>
</head>
<body>

    @php
        $logoClean = $companyLogo ? str_replace(['http://10.42.1.15:8080/storage/', asset('storage/')], '', $companyLogo) : null;
        $logoClean = $logoClean ? ltrim($logoClean, '/') : null;
        $logoPath = $logoClean ? storage_path('app/public/' . $logoClean) : null;
        $logoExists = $logoPath && file_exists($logoPath);
    @endphp

    <div class="card">
        <!-- LEFT: Red Panel -->
        <div class="card-left">
            <div class="logo-box">
                @if($logoExists)
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Logo">
                @else
                    <span style="color: #8B0000; font-size: 20px; font-weight: bold;">{{ strtoupper(substr($mecard->name, 0, 1)) }}</span>
                @endif
            </div>
            
            <div class="qr-box">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->margin(1)->generate($mecard->toMeCard()) !!}
            </div>
            <div class="scan-text">Scan Me</div>
        </div>
        
        <!-- RIGHT: Cream Panel -->
        <div class="card-right">
            <div class="name">
                <span class="name-accent"></span>
                {{ $mecard->name }}
            </div>
            @if($mecard->title)
            <div class="title">{{ $mecard->title }}</div>
            @endif
            
            @if($mecard->phone)
            <div class="info-item">
                <div class="icon">📞</div>
                <span>{{ $mecard->phone }}</span>
            </div>
            @endif
            
            @if($mecard->email)
            <div class="info-item">
                <div class="icon">✉️</div>
                <span>{{ $mecard->email }}</span>
            </div>
            @endif
            
            @if($mecard->company)
            <div class="info-item">
                <div class="icon">🏢</div>
                <span>{{ $mecard->company }}</span>
            </div>
            @endif
            
            @if($mecard->address)
            <div class="info-item">
                <div class="icon">📍</div>
                <span>{{ $mecard->address }}</span>
            </div>
            @endif
            
            <div class="company-name">{{ $companyName }}</div>
        </div>
    </div>

    {{-- <div class="actions">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
        <button class="btn btn-close" onclick="window.close()">✖️ Tutup</button>
    </div> --}}

</body>
</html>