<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            size: 90mm 55mm;
            margin: 0;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            width: 90mm;
            height: 55mm;
            overflow: hidden;
        }
        
        .card {
            width: 90mm;
            height: 55mm;
            background: #1e1e2f;
            padding: 15px;
            display: flex;
            gap: 12px;
        }
        
        .card-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .card-left .logo {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: rgba(255,255,255,0.1);
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
            color: #ffffff;
            margin-bottom: 2px;
        }
        
        .card-left .system {
            font-size: 7pt;
            color: #aaaaaa;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .card-left .info {
            font-size: 7pt;
            color: #cccccc;
            margin-bottom: 3px;
        }
        
        .card-right {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .card-right .qr-box {
            background: #ffffff;
            border-radius: 10px;
            padding: 8px;
        }
        
        .card-right .qr-box img {
            width: 26mm;
            height: 26mm;
        }
        
        .card-right .scan-text {
            color: #888888;
            font-size: 6pt;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-left">
            <div class="logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
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
            <div class="qr-box">
                <img src="{{ $qrBase64 }}" alt="QR">
            </div>
            <div class="scan-text">Scan Me</div>
        </div>
    </div>
</body>
</html>