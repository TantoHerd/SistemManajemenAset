<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label - {{ $asset->asset_code }}</title>
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
            width: 73mm;
            height: 43mm;
        }
        
        .label {
            width: 73mm;
            height: 43mm;
            background: white;
            border-radius: 3px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        /* Header - lebih kecil */
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
            letter-spacing: 0.3px;
        }
        
        .label-header small {
            font-size: 7pt;
            opacity: 0.9;
        }
        
        /* Body - lebih kecil */
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
        
        /* QR Code Section - lebih kecil */
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
        
        /* Info Section - lebih kecil */
        .info-section {
            flex: 1;
        }
        
        .asset-name {
            font-size: 8pt;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 2px;
            line-height: 1.2;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 2px;
            font-size: 6pt;
            color: #555;
            margin-bottom: 1px;
        }
        
        .detail-item span:first-child {
            min-width: 14px;
            font-size: 4.5pt;
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
        
        .print-actions {
            display: none;
        }
        
        @media print {
            body {
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .label {
                box-shadow: none;
                border: 0.2px solid #ddd;
            }
            .print-actions {
                display: none;
            }
        }
        
        @media screen {
            body {
                background: #f0f2f5;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                width: auto;
                height: auto;
                padding: 20px;
            }
            .label {
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                margin-bottom: 20px;
            }
            .print-actions {
                display: block;
                text-align: center;
                margin-top: 20px;
            }
            .btn {
                padding: 8px 16px;
                margin: 0 5px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 12px;
            }
            .btn-primary {
                background: #4361ee;
                color: white;
            }
            .btn-secondary {
                background: #6c757d;
                color: white;
            }
            .btn-success {
                background: #28a745;
                color: white;
            }
        }
    </style>
</head>
<body>
    @if(isset($asset) && $asset)
    <div class="label">
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
            <div class="company-name">
                @php
                    try {
                        $company = \App\Models\Setting::where('key', 'company_name')->first();
                        $companyName = $company ? $company->value : 'PT. NAMA PERUSAHAAN';
                    } catch (\Exception $e) {
                        $companyName = 'PT. NAMA PERUSAHAAN';
                    }
                @endphp
                {{ $companyName }} | Asset IT
            </div>
        </div>
    </div>
    
    <div class="print-actions">
        <button class="btn btn-primary" onclick="window.print()">🖨️ Cetak</button>
        <button class="btn btn-secondary" onclick="window.close()">✖️ Tutup</button>
        <button class="btn btn-success" onclick="window.location.href='{{ route('admin.assets.index') }}'">↩️ Kembali</button>
    </div>
    @else
    <div style="text-align: center; padding: 20px;">
        <h3>⚠️ Data Aset Tidak Ditemukan</h3>
        <button onclick="window.close()">Tutup</button>
    </div>
    @endif
</body>
</html>