<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label - {{ $asset->asset_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        @page {
            size: 75mm 45mm;
            margin: 0;
        }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: white;
            width: 75mm;
            height: 45mm;
            margin: 0;
            padding: 0;
        }
        
        .label {
            width: 75mm;
            height: 45mm;
            display: flex;
            flex-direction: column;
            border: 1px solid #000;
            background: white;
        }
        
        /* HEADER */
        .label-header {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 1.5mm 3mm;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        /* BODY */
        .label-body {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 2mm 3mm;
            gap: 2mm;
        }
        
        .label-body .details {
            flex: 1;
        }
        
        .label-body .details .name {
            font-size: 8pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 1.5mm;
            text-transform: uppercase;
            line-height: 1.2;
        }
        
        .label-body .details .info {
            font-size: 7pt;
            color: #000;
            margin-bottom: 0.5mm;
            line-height: 1.3;
        }
        
        .label-body .details .info b {
            font-weight: bold;
        }
        
        .label-body .details .status {
            display: inline-block;
            font-size: 7pt;
            font-weight: bold;
            color: #000;
            border: 1px solid #000;
            padding: 0.5mm 2mm;
            margin-top: 1mm;
            text-transform: uppercase;
        }
        
        .label-body .qr {
            width: 16mm;
            height: 16mm;
            flex-shrink: 0;
        }
        
        /* FOOTER */
        .label-footer {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 1.5mm 3mm;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Screen */
        @media screen {
            body {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                background: #e8ecf1;
                width: auto;
                height: auto;
                padding: 20px;
            }
            .label { box-shadow: 0 4px 15px rgba(0,0,0,0.15); margin-bottom: 20px; }
            .actions { display: flex; gap: 10px; }
            .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: bold; }
            .btn-print { background: #000; color: #fff; }
            .btn-close { background: #ccc; color: #000; }
        }
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .actions { display: none !important; }
        }
    </style>
</head>
<body>

    @if(isset($asset) && $asset)
    <div class="label">
        <!-- HEADER: Kode Aset -->
        <div class="label-header">
            {{ $asset->asset_code }}
        </div>
        
        <!-- BODY: Detail + QR -->
        <div class="label-body">
            <div class="details">
                <div class="name">{{ $asset->name }}</div>
                <div class="info"><b>Lokasi:</b> {{ $asset->location->name ?? '-' }}</div>
                <div class="info"><b>User:</b> {{ $asset->assignedTo->name ?? '-' }}</div>
                <div class="info"><b>Tahun:</b> {{ $asset->purchase_date ? $asset->purchase_date->format('Y') : '-' }}</div>
                <div class="info"><b>Model:</b> {{ $asset->brand }} {{ $asset->model }}</div>
                <div class="status">{{ $asset->status_label }}</div>
            </div>
            
            @php
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(55)->margin(0)->generate($asset->asset_code);
            @endphp
            <div class="qr">{!! $qrCode !!}</div>
        </div>
        
        <!-- FOOTER: Nama Perusahaan -->
        <div class="label-footer">
            {{ \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'PT. NAMA PERUSAHAAN' }}
        </div>
    </div>
    
    <div class="actions">
        <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
        <button class="btn btn-close" onclick="window.close()">✖️ Tutup</button>
    </div>
    @endif

    <script>
        window.onload = function() { setTimeout(() => window.print(), 300); };
    </script>
</body>
</html>