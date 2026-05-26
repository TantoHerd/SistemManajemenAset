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
            border: 1px solid #ccc;
            background: white;
            display: flex;
            flex-direction: column;
            padding: 2mm;
        }

        /* Header: kode aset + garis bawah tipis */
        .label-header {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            color: #333;
            padding-bottom: 1mm;
            border-bottom: 1px dashed #000000;
            margin-bottom: 2mm;
        }

        /* Body: info kiri + QR kanan */
        .label-body {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 2mm;
        }

        .label-body .details {
            flex: 1;
            font-size: 9pt;
            color: #333;
            line-height: 1.3;
        }

        .label-body .details .name {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 1mm;
            text-transform: uppercase;
        }

        .label-body .qr {
            width: 16mm;
            height: 16mm;
            flex-shrink: 0;
        }

        /* Footer: nama perusahaan + garis atas tipis */
        .label-footer {
            text-align: right;
            font-size: 7pt;
            color: #000000;
            margin-top: auto;
            padding-top: 1mm;
            border-top: 1px dashed #000000;
        }

        /* Screen only */
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
            .label {
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                margin-bottom: 20px;
            }
            .actions { display: flex; gap: 10px; }
            .btn {
                padding: 10px 20px; border: none; border-radius: 6px;
                cursor: pointer; font-size: 13px; font-weight: bold;
            }
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
        <div class="label-header">{{ $asset->asset_code }}</div>

        <div class="label-body">
            <div class="details">
                <div class="name">{{ $asset->name }}</div>
                <div>Lokasi: {{ $asset->location->name ?? '-' }}</div>
                <div>User: {{ $asset->assignedTo->name ?? '-' }}</div>
                <div>Tahun: {{ $asset->purchase_date ? $asset->purchase_date->format('Y') : '-' }}</div>
                <div style="margin-top:1mm; font-weight:bold;">{{ strtoupper($asset->status_label) }}</div>
            </div>

            @php
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(55)->margin(0)->generate($asset->asset_code);
            @endphp
            <div class="qr">{!! $qrCode !!}</div>
        </div>

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