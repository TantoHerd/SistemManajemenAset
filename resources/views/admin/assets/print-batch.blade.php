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
        
        body {
            font-family: 'Segoe UI', 'Inter', 'Poppins', sans-serif;
            background: #f0f2f5;
            padding: 10px;
        }
        
        .info-bar {
            background: linear-gradient(135deg, #4361ee, #3b2a9f);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .info-bar .title {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-bar .title h5 {
            margin: 0;
            font-size: 14px;
        }
        
        .badge-count {
            background: rgba(255,255,255,0.2);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .btn-group {
            display: flex;
            gap: 6px;
        }
        
        .btn {
            padding: 5px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 500;
        }
        
        .btn-primary {
            background: white;
            color: #4361ee;
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        /* LABEL GRID - 4 KOLOM */
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }
        
        /* LABEL CARD */
        .label-card {
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            break-inside: avoid;
            page-break-inside: avoid;
        }
        
        .label-header {
            background: linear-gradient(135deg, #4361ee, #3b2a9f);
            color: white;
            padding: 4px 6px;
            text-align: center;
        }
        
        .label-header h4 {
            font-size: 7pt;
            margin: 0;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        
        .label-header small {
            font-size: 4.5pt;
            opacity: 0.9;
        }
        
        .label-body {
            padding: 6px;
        }
        
        .label-content {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        /* Barcode Section */
        .barcode-section {
            flex-shrink: 0;
            text-align: center;
            background: #fafafa;
            padding: 5px;
            border-radius: 4px;
            border: 0.5px solid #e0e0e0;
            width: 60px;
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
            max-width: 25mm;
            height: auto;
        }
        
        .barcode-code {
            font-size: 5pt;
            font-family: monospace;
            margin-top: 2px;
            color: #555;
            text-align: center;
            word-break: break-all;
        }
        
        /* Info Section */
        .info-section {
            flex: 1;
            min-width: 0;
        }
        
        .asset-name {
            font-size: 6.5pt;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 3px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .asset-details {
            margin-bottom: 3px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 3px;
            font-size: 7pt;
            color: #555;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .detail-item span:first-child {
            min-width: 18px;
            font-size: 4.5pt;
        }
        
        .detail-item strong {
            font-weight: 600;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            padding: 1px 4px;
            border-radius: 8px;
            font-size: 5pt;
            font-weight: 600;
            margin-top: 2px;
        }
        
        .status-available { background: #d4edda; color: #155724; }
        .status-in_use { background: #cce5ff; color: #004085; }
        .status-maintenance { background: #fff3cd; color: #856404; }
        .status-damaged { background: #f8d7da; color: #721c24; }
        .status-disposed { background: #e2e3e5; color: #383d41; }
        
        /* Footer */
        .label-footer {
            background: #f8f9fc;
            padding: 3px 6px;
            text-align: center;
            border-top: 0.5px dashed #dee2e6;
        }
        
        .company-name {
            font-size: 4.5pt;
            color: #6c757d;
            letter-spacing: 0.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* PRINT STYLES */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .info-bar {
                display: none;
            }
            .labels-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 5px;
                padding: 3px;
            }
            .label-card {
                box-shadow: none;
                border: 0.3px solid #ddd;
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
        
        /* RESPONSIVE */
        @media (max-width: 1000px) {
            .labels-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 700px) {
            .labels-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 500px) {
            .labels-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @php
        $assetCount = isset($assets) && !is_null($assets) ? count($assets) : 0;
    @endphp
    
    <div class="info-bar">
        <div class="title">
            <span style="font-size: 16px;">🏷️</span>
            <h5>Cetak Label Batch</h5>
            <span class="badge-count">{{ $assetCount }} Aset</span>
        </div>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="window.print()">
                🖨️ Cetak Semua ({{ $assetCount }} label)
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                ✖️ Tutup
            </button>
        </div>
    </div>
    
    <div class="labels-grid">
        @if($assetCount > 0)
            @foreach($assets as $asset)
            <div class="label-card">
                <div class="label-header">
                    <h4>🏢 ASSET IDENTIFICATION</h4>
                    <small>Property of IT Asset Management</small>
                </div>
                
                <div class="label-body">
                    <div class="label-content">
                        <div class="barcode-section">
                            @php
                                $url = route('admin.assets.show', $asset);
                                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(50)->margin(0)->generate($url);
                            @endphp
                            <div style="display: flex; justify-content: center; align-items: center;">
                                {!! $qrCode !!}
                            </div>
                            {{-- <div class="barcode-code">{{ $asset->asset_code }}</div> --}}
                        </div>
                        
                        <div class="info-section">
                            <div class="asset-name" title="{{ $asset->name }}">{{ Str::limit($asset->name, 30) }}</div>
                            <div class="asset-details">
                                <!-- SN (Serial Number) -->
                                {{-- <div class="detail-item" title="{{ $asset->serial_number ?? '-' }}">
                                    <span>🔢</span>
                                    <span><strong>SN:</strong> {{ Str::limit($asset->serial_number ?? '-', 20) }}</span>
                                </div> --}}
                                
                                <!-- Lokasi -->
                                <div class="detail-item" title="{{ $asset->location->full_path ?? $asset->location->name ?? '-' }}">
                                    <span>📍</span>
                                    <span><strong>Lokasi:</strong> {{ Str::limit($asset->location->name ?? '-', 20) }}</span>
                                </div>
                                
                                <!-- Assign (Pengguna yang menggunakan aset) -->
                                <div class="detail-item" title="{{ $asset->assignedTo->name ?? '-' }}">
                                    <span>👤</span>
                                    <span><strong>Assign:</strong> {{ Str::limit($asset->assignedTo->name ?? '-', 12) }}</span>
                                </div>

                                <!-- Tanggal Beli (BARU) -->
                                <div class="detail-item">
                                    <span>📅</span>
                                    <strong>Tgl Beli:</strong>
                                    <span class="text-content">{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-' }}</span>
                                </div>
                                
                                <!-- Brand & Model (Opsional) -->
                                @if($asset->brand || $asset->model)
                                <div class="detail-item" title="{{ $asset->brand }} {{ $asset->model }}">
                                    <span>🏷️</span>
                                    <span>{{ Str::limit($asset->brand . ' ' . $asset->model, 30) }}</span>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Status -->
                            <div class="status-badge status-{{ $asset->status }}">
                                <span>●</span> {{ $asset->status_label }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="label-footer">
                    <div class="company-name">
                        🏢 PT. INDUKSARANA KEMASINDO - Aset IT
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <p>Tidak ada data aset yang dipilih</p>
                <button class="btn btn-primary" onclick="window.close()">Tutup</button>
            </div>
        @endif
    </div>
    
    <script>
        // Optional: Auto print
        // setTimeout(function() { window.print(); }, 500);
    </script>
</body>
</html>