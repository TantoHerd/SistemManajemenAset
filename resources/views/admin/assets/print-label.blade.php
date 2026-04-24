<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label - {{ $asset->asset_code ?? 'Aset' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'Inter', 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .label-wrapper {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        .label {
            width: 105mm;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .label-header {
            background: linear-gradient(135deg, #4361ee 0%, #3b2a9f 100%);
            color: white;
            padding: 8px 12px;
            text-align: center;
        }
        
        .label-header h4 {
            font-size: 11pt;
            margin: 0;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .label-header small {
            font-size: 7pt;
            opacity: 0.9;
        }
        
        .label-body {
            padding: 12px;
        }
        
        .label-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        /* Barcode Section - DIBUAT CENTER */
        .barcode-section {
            flex-shrink: 0;
            text-align: center;
            background: #fafafa;
            padding: 10px 8px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            min-width: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .barcode-section svg {
            display: block;
            margin: 0 auto;
        }
        
        .barcode-img {
            display: block;
            margin: 0 auto;
            max-width: 45mm;
            height: auto;
        }
        
        .barcode-code {
            font-size: 8pt;
            font-family: monospace;
            margin-top: 6px;
            color: #555;
            letter-spacing: 0.5px;
            text-align: center;
        }
        
        .info-section {
            flex: 1;
        }
        
        .asset-name {
            font-size: 11pt;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 6px;
            line-height: 1.3;
        }
        
        .asset-details {
            margin-bottom: 8px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 7pt;
            color: #555;
            margin-bottom: 4px;
        }
        
        .detail-item span:first-child {
            min-width: 24px;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 7pt;
            font-weight: 600;
            margin-top: 5px;
        }
        
        .status-available { background: #d4edda; color: #155724; }
        .status-in_use { background: #cce5ff; color: #004085; }
        .status-maintenance { background: #fff3cd; color: #856404; }
        .status-damaged { background: #f8d7da; color: #721c24; }
        .status-disposed { background: #e2e3e5; color: #383d41; }
        
        .label-footer {
            background: #f8f9fc;
            padding: 6px 12px;
            text-align: center;
            border-top: 1px dashed #dee2e6;
        }
        
        .company-name {
            font-size: 6pt;
            color: #6c757d;
            letter-spacing: 0.5px;
        }
        
        .print-actions {
            text-align: center;
            margin-top: 25px;
        }
        
        .btn {
            padding: 10px 24px;
            margin: 0 6px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4361ee, #3b2a9f);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67,97,238,0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .label-wrapper {
                padding: 0;
                box-shadow: none;
            }
            .label {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    @if(isset($asset) && $asset)
    <div>
        <div class="label-wrapper">
            <div class="label">
                <div class="label-header">
                    <h4>🏢 ASSET IDENTIFICATION</h4>
                    <small>Property of IT Asset Management</small>
                </div>
                
                <div class="label-body">
                    <div class="label-content">
                        <div class="barcode-section">
                            @php
                                $url = route('admin.assets.show', $asset);
                                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(80)->margin(0)->generate($url);
                            @endphp
                            <div style="display: flex; justify-content: center; align-items: center;">
                                {!! $qrCode !!}
                            </div>
                            {{-- <div class="barcode-code">{{ $asset->asset_code }}</div> --}}
                        </div>
                        
                        <div class="info-section">
                            <div class="asset-name">{{ $asset->name }}</div>
                            
                            <div class="asset-details">
                                {{-- <div class="detail-item">
                                    <span>🔢</span>
                                    <span><strong>SN:</strong> {{ $asset->serial_number ?? '-' }}</span>
                                </div> --}}
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
                                    <strong>Tgl Beli:</strong>
                                    <span class="text-content">{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-' }}</span>
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
                    <div class="company-name">
                        🏢 PT. IDUKSARANA KEMASINDO | Aset IT
                    </div>
                </div>
            </div>
        </div>
        
        <div class="print-actions">
            <button class="btn btn-primary" onclick="window.print()">
                🖨️ Cetak Label
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                ✖️ Tutup
            </button>
            <button class="btn btn-success" onclick="window.location.href='{{ route('admin.assets.index') }}'">
                ↩️ Kembali
            </button>
        </div>
    </div>
    @else
    <div style="text-align: center; padding: 50px; background: white; border-radius: 12px;">
        <h3>⚠️ Data Aset Tidak Ditemukan</h3>
        <p>Aset yang Anda cari tidak tersedia.</p>
        <button class="btn btn-primary" onclick="window.close()">Tutup</button>
    </div>
    @endif
</body>
</html>