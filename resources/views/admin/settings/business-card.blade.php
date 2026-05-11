<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Nama - {{ $companyName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .card {
            width: 90mm;
            height: 55mm;
            background: linear-gradient(135deg, #8B0000 0%, #C62828 50%, #D32F2F 100%);
            border-radius: 12px;
            padding: 15px;
            display: flex;
            gap: 12px;
            box-shadow: 0 20px 50px rgba(139, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -30px;
            width: 120px;
            height: 120px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .card::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: -20px;
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .card-left {
            flex: 1;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 1;
        }
        
        .card-left .logo {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            overflow: hidden;
        }
        
        .card-left .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .card-left .company {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
            color: #fff;
        }
        
        .card-left .system {
            font-size: 7pt;
            color: rgba(255,255,255,0.7);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .card-left .info {
            font-size: 7pt;
            color: rgba(255,255,255,0.9);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .card-right {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            position: relative;
            z-index: 1;
        }
        
        .card-right .qr-box {
            background: #fff;
            border-radius: 10px;
            padding: 8px;
            width: 28mm;
            height: 28mm;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .card-right .qr-box svg {
            width: 100%;
            height: 100%;
        }
        
        .card-right .scan-text {
            color: rgba(255,255,255,0.6);
            font-size: 6pt;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        /* Print */
        @media print {
            body { background: white; }
            .card { box-shadow: none; }
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
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-print { background: #8B0000; color: #fff; }
        .btn-print:hover { background: #6B0000; }
        .btn-download { background: #fff; color: #8B0000; border: 2px solid #8B0000; }
        .btn-download:hover { background: #8B0000; color: #fff; }
    </style>
</head>
<body>

    @php
        $mecard = "MECARD:";
        $mecard .= "N:{$companyName};";
        if ($companyPhone) $mecard .= "TEL:{$companyPhone};";
        if ($companyEmail) $mecard .= "EMAIL:{$companyEmail};";
        if ($companyAddress) $mecard .= "ADR:{$companyAddress};";
        $mecard .= "NOTE:{$systemName};";
        $mecard .= ";";
        
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(100)->margin(1)->generate($mecard);
    @endphp

    <div class="card">
        <div class="card-left">
            <div class="logo">
                @php
                    $logoPath = $companyLogo ? public_path('storage/' . $companyLogo) : null;
                    $logoExists = $logoPath && file_exists($logoPath);
                @endphp
                
                @if($logoExists)
                    <img src="{{ asset('storage/' . $companyLogo) }}" alt="Logo">
                @else
                    <span style="color: #fff; font-size: 20px;">🏢</span>
                @endif
            </div>
            <div class="company">{{ $companyName }}</div>
            <div class="system">{{ $systemName }}</div>
            
            @if($companyPhone)
            <div class="info">📞 {{ $companyPhone }}</div>
            @endif
            @if($companyEmail)
            <div class="info">✉️ {{ $companyEmail }}</div>
            @endif
            @if($companyAddress)
            <div class="info">📍 {{ $companyAddress }}</div>
            @endif
        </div>
        
        <div class="card-right">
            <div class="qr-box">{!! $qrCode !!}</div>
            <div class="scan-text">Scan Me</div>
        </div>
    </div>

    <div class="actions">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
        <a href="{{ route('admin.settings.business-card.download') }}" class="btn btn-download">📥 Download JPG</a>
    </div>

</body>
</html>