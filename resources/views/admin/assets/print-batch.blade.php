<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Batch - {{ $assets->count() ?? 0 }} Aset</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: 75mm 45mm;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: white;
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
            page-break-after: always;
        }
        .label:last-child { page-break-after: auto; }

        .label-header {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            color: #333;
            padding-bottom: 1mm;
            border-bottom: 1px dashed #000000;
            margin-bottom: 2mm;
        }

        .label-body {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 2mm;
        }

        .label-body .details {
            flex: 1;
            font-size: 9pt;
            color: #000000;
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

        .label-footer {
            text-align: right;
            font-size: 7pt;
            color: #000000;
            margin-top: auto;
            padding-top: 1mm;
            border-top: 1px dashed #000000;
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
            <div class="label-footer">{{ $companyName }}</div>
        </div>
        @empty
        <div style="text-align:center; padding:40px; background:white;">Tidak ada aset</div>
        @endforelse
    </div>

    <script>
        window.onload = function() { setTimeout(() => window.print(), 300); };
    </script>
</body>
</html>