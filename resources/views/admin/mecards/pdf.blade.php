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
        
        html, body {
            margin: 0;
            padding: 0;
            width: 90mm;
            height: 55mm;
            overflow: hidden;
            page-break-before: avoid;
            page-break-after: avoid;
        }
        
        .card {
            width: 90mm;
            height: 55mm;
            background: #8B0000;
            padding: 6mm;
            page-break-before: avoid;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        
        .logo {
            width: 35px;
            height: 35px;
            border-radius: 6px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .logo img { width: 100%; height: 100%; object-fit: contain; }
        
        .name {
            font-size: 13pt;
            font-weight: bold;
            color: #fff;
        }
        
        .title {
            font-size: 6pt;
            color: rgba(255,255,255,0.7);
        }
        
        .info {
            font-size: 6pt;
            color: rgba(255,255,255,0.85);
            line-height: 1.3;
        }
        
        .qr-box {
            background: #fff;
            border-radius: 6px;
            padding: 4px;
            display: inline-block;
        }
        
        .qr-box img {
            width: 18mm;
            height: 18mm;
            display: block;
        }
    </style>
</head>
<body><div class="card"><table style="width:100%;height:100%;border:none;border-collapse:collapse;"><tr><td style="width:40px;vertical-align:top;padding:0;"><div class="logo">@if($logoBase64)<img src="{{ $logoBase64 }}" alt="Logo">@else<span style="color:#fff;font-size:14px;">🏢</span>@endif</div></td><td style="vertical-align:top;padding:0 0 0 6px;"><div class="name">{{ $mecard->name }}</div>@if($mecard->title)<div class="title">{{ $mecard->title }}</div>@endif</td><td style="width:50px;vertical-align:middle;text-align:right;padding:0;"><div class="qr-box"><img src="{{ $qrBase64 }}" alt="QR"></div></td></tr><tr><td colspan="3" style="padding:4px 0 0 0;"><div class="info">@if($mecard->company)🏢 {{ $mecard->company }}<br>@endif @if($mecard->address)📍 {{ $mecard->address }}<br>@endif</div></td></tr><tr><td colspan="3" style="padding:4px 0 0 0;vertical-align:bottom;"><div class="info">@if($mecard->phone)📞 {{ $mecard->phone }}@endif @if($mecard->phone && $mecard->email)| @endif @if($mecard->email)✉️ {{ $mecard->email }}@endif</div></td></tr></table></div></body>
</html>