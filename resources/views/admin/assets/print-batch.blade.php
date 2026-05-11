<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Batch - {{ $assets->count() ?? 0 }} Aset</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        @page { size: 75mm 45mm; margin: 0; }
        
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: white;
            margin: 0;
            padding: 0;
        }
        
        .label {
            width: 75mm;
            height: 45mm;
            display: flex;
            flex-direction: column;
            border: 1px solid #000;
            page-break-after: always;
        }
        .label:last-child { page-break-after: auto; }
        
        .label-header {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 1.5mm 3mm;
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .label-body {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 2mm 3mm;
            gap: 2mm;
        }
        .label-body .details { flex: 1; }
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
        
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        
        @media screen {
            body { background: #e8ecf1; }
            .no-print {
                position: sticky; top: 0; background: #000; color: #fff;
                padding: 10px 20px; display: flex; justify-content: space-between;
                align-items: center; z-index: 1000; font-size: 13px;
            }
            .container { display: flex; flex-direction: column; align-items: center; gap: 15px; padding: 20px; }
            .label { box-shadow: 0 4px 15px rgba(0,0,0,0.15); background: white; }
            .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: bold; }
            .btn-print { background: #fff; color: #000; }
            .btn-close { background: #444; color: #fff; }
        }
    </style>
</head>
<body>

    @php
        $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?? 'PT. NAMA PERUSAHAAN';
    @endphp

    <div class="no-print">
        <span>🖨️ {{ $assets->count() }} Label</span>
        <div style="display: flex; gap: 8px;">
            <button class="btn btn-print" onclick="window.print()">Cetak Semua</button>
            <button class="btn btn-close" onclick="window.close()">Tutup</button>
        </div>
    </div>

    <div class="container">
        @forelse($assets as $asset)
        <div class="label">
            <div class="label-header">{{ $asset->asset_code }}</div>
            <div class="label-body">
                <div class="details">
                    <div class="name">{{ $asset->name }}</div>
                    <div class="info"><b>Lokasi:</b> {{ $asset->location->name ?? '-' }}</div>
                    <div class="info"><b>User:</b> {{ $asset->assignedTo->name ?? '-' }}</div>
                    <div class="info"><b>Tahun:</b> {{ $asset->purchase_date ? $asset->purchase_date->format('Y') : '-' }}</div>
                    <div class="info"><b>Model:</b> {{ $asset->brand }} {{ $asset->model }}</div>
                    <div class="status">{{ strtoupper($asset->status_label) }}</div>
                </div>
                @php
                    $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(55)->margin(0)->generate($asset->asset_code);
                @endphp
                <div class="qr">{!! $qrCode !!}</div>
            </div>
            <div class="label-footer">{{ $companyName }}</div>
        </div>
        @empty
        <div style="text-align: center; padding: 40px; background: white;">Tidak ada aset</div>
        @endforelse
    </div>

    <script>
        window.onload = function() { setTimeout(() => window.print(), 300); };
    </script>
</body>
</html>