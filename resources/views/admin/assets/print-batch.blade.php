<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Batch - {{ isset($assets) ? count($assets) : 0 }} Aset</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Ukuran untuk label 73mm x 43mm */
        @page {
            size: 73mm 43mm;
            margin: 0;
        }
        
        body {
            font-family: 'Segoe UI', 'Inter', 'Poppins', sans-serif;
            background: white;
            margin: 0;
            padding: 0;
        }
        
        /* Setiap label di halaman terpisah */
        .label-card {
            width: 73mm;
            height: 43mm;
            margin: 0;
            padding: 0;
            background: white;
            border-radius: 3px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            page-break-after: always;
            break-after: page;
        }
        
        /* Header */
        .label-header {
            background: linear-gradient(135deg, #4361ee 0%, #3b2a9f 100%);
            color: white;
            padding: 2px 4px;
            text-align: center;
        }
        
        .label-header h4 {
            font-size: 8pt;
            margin: 0;
            font-weight: 600;
        }
        
        .label-header small {
            font-size: 7pt;
            opacity: 0.9;
        }
        
        /* Body */
        .label-body {
            flex: 1;
            padding: 3px 4px;
        }
        
        .label-content {
            display: flex;
            align-items: center;
            gap: 4px;
            height: 100%;
        }
        
        /* QR Code Section */
        .barcode-section {
            flex-shrink: 0;
            text-align: center;
            background: #fafafa;
            padding: 2px;
            border-radius: 4px;
            border: 0.5px solid #e0e0e0;
            width: 24mm;
        }
        
        .barcode-code {
            font-size: 4.5pt;
            font-family: monospace;
            margin-top: 1px;
            color: #555;
            text-align: center;
        }
        
        /* Info Section */
        .info-section {
            flex: 1;
        }
        
        .asset-name {
            font-size: 8pt;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 2px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 2px;
            font-size: 6pt;
            color: #555;
            margin-bottom: 1px;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding: 1px 3px;
            border-radius: 6px;
            font-size: 6pt;
            font-weight: 600;
            margin-top: 1px;
        }
        
        .status-available { background: #d4edda; color: #155724; }
        .status-in_use { background: #cce5ff; color: #004085; }
        .status-maintenance { background: #fff3cd; color: #856404; }
        .status-damaged { background: #f8d7da; color: #721c24; }
        
        /* Footer */
        .label-footer {
            background: #f8f9fc;
            padding: 2px 3px;
            text-align: center;
            border-top: 0.5px dashed #dee2e6;
        }
        
        .company-name {
            font-size: 6pt;
            color: #6c757d;
        }
        
        /* Info Bar untuk layar */
        .info-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #4361ee, #3b2a9f);
            color: white;
            padding: 8px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        
        .btn {
            padding: 4px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
        }
        
        .btn-primary {
            background: white;
            color: #4361ee;
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .labels-container {
            margin-top: 50px;
            padding: 15px;
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .label-card.screen-view {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        @media print {
            body {
                margin: 0 !important;
                padding: 0 !important;
            }
            .info-bar {
                display: none;
            }
            .labels-container {
                margin: 0;
                padding: 0;
                background: white;
                display: block;
            }
            .label-card {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none;
                border: 0.2px solid #ddd;
                page-break-after: always;
                break-after: page;
            }
        }
    </style>
</head>
<body>
    @php
        $assets = $assets ?? collect();
        $assetCount = $assets->count();
        
        try {
            $company = \App\Models\Setting::where('key', 'company_name')->first();
            $companyName = $company ? $company->value : 'PT. NAMA PERUSAHAAN';
        } catch (\Exception $e) {
            $companyName = 'PT. NAMA PERUSAHAAN';
        }
    @endphp
    
    <div class="info-bar">
        <span>🏷️ Cetak Label Batch ({{ $assetCount }} aset)</span>
        <div>
            <button class="btn btn-primary" onclick="window.print()">🖨️ Cetak</button>
            <button class="btn btn-secondary" onclick="window.close()">✖️ Tutup</button>
        </div>
    </div>
    
    <div class="labels-container">
        @if($assetCount > 0)
            @foreach($assets as $asset)
            <div class="label-card screen-view">
                <div class="label-header">
                    <h4>🏢 ASSET IDENTIFICATION</h4>
                    <small>Property of IT Asset Management</small>
                </div>
                
                <div class="label-body">
                    <div class="label-content">
                        <div class="barcode-section">
                            @php
                                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(70)->margin(0)->generate($asset->asset_code);
                            @endphp
                            <div style="display: flex; justify-content: center; align-items: center;">
                                <div style="width: 70px; height: 70px;">
                                    {!! $qrCode !!}
                                </div>
                            </div>
                            {{-- <div class="barcode-code">{{ $asset->asset_code }}</div> --}}
                        </div>
                        
                        <div class="info-section">
                            <div class="asset-name">{{ $asset->name }}</div>
                            <div class="asset-details">
                                <div class="detail-item">
                                    <span>📍</span>
                                    <span><strong>Lokasi:</strong> {{ $asset->location->name ?? '-' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span>👤</span>
                                    <span><strong>Assign:</strong> {{ $asset->assignedTo->name ?? '-' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span>📅</span>
                                    <span><strong>Tahun Pengadaan:</strong> {{ $asset->purchase_date ? $asset->purchase_date->format('Y') : '-' }}</span>
                                </div>
                                @if($asset->brand || $asset->model)
                                <div class="detail-item">
                                    <span>🏷️</span>
                                    <span><strong>Model:</strong> {{ $asset->brand }} {{ $asset->model }}</span>
                                </div>
                                @endif
                            </div>
                            <div class="status-badge status-{{ $asset->status }}">
                                <span>●</span> {{ $asset->status_label }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="label-footer">
                    <div class="company-name">{{ $companyName }}  | Asset IT</div>
                </div>
            </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 30px; background: white; border-radius: 8px;">
                <p>Tidak ada data aset yang dipilih</p>
            </div>
        @endif
    </div>
</body>
</html>