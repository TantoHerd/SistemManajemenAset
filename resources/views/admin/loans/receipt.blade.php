<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Peminjaman - {{ $loan->loan_code }}</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 4mm;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #333;
            font-size: 7px;
            line-height: 1.2;
            width: 100%;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 4px;
            margin-bottom: 5px;
        }
        .header .logo { max-width: 30px; max-height: 30px; }
        .header .company { font-size: 9px; font-weight: bold; }
        .header .subtitle { font-size: 6px; color: #666; }
        
        .receipt-title {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            margin: 4px 0;
            padding: 3px;
            background: #f5f5f5;
        }
        
        .main-content {
            display: flex;
            gap: 10px;
            width: 100%;
        }
        .left-col {
            flex: 0 0 40%;
            border-right: 1px dashed #ddd;
            padding-right: 8px;
        }
        .right-col {
            flex: 1;
        }
        
        .section-title {
            font-size: 7px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            padding-bottom: 1px;
            margin: 4px 0 2px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 1px 0;
        }
        .info-label { font-weight: 600; min-width: 70px; font-size: 6px; }
        .info-value { text-align: right; font-size: 6px; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
        }
        table th {
            background: #1e1e2f;
            color: white;
            padding: 2px 3px;
            font-size: 6px;
        }
        table td {
            padding: 2px 3px;
            border: 1px solid #ddd;
            font-size: 6px;
        }
        .text-center { text-align: center; }
        
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 6px;
            font-size: 6px;
            font-weight: bold;
        }
        .badge-active { background: #cfe2ff; color: #084298; }
        .badge-returned { background: #d1e7dd; color: #0f5132; }
        .badge-overdue { background: #f8d7da; color: #842029; }
        .badge-pending { background: #fff3cd; color: #664d03; }
        
        .fine-box {
            background: #fff3cd;
            padding: 2px 5px;
            margin: 3px 0;
            font-size: 6px;
        }
        .text-danger { color: #842029; }
        
        .terms {
            font-size: 5px;
            color: #888;
            border-top: 1px solid #eee;
            margin-top: 3px;
            padding-top: 2px;
        }
        .terms ol { margin: 1px 0 0 10px; }
        
        /* SIGNATURE - 1 BARIS */
        .signature-section {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            margin-top: 12px;
            width: 100% !important;
        }
        .signature-box {
            text-align: center;
            font-size: 6px;
            width: 30% !important;
            display: inline-block !important;
        }
        .signature-box .line {
            margin-top: 18px;
            border-top: 1px solid #333;
            padding-top: 2px;
            font-weight: 600;
        }
        
        .footer {
            text-align: center;
            font-size: 5px;
            color: #999;
            margin-top: 5px;
            border-top: 1px solid #eee;
            padding-top: 2px;
            clear: both;
        }
    </style>
</head>
<body>

    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
        @endif
        <div class="company">{{ $companyName }}</div>
        <div class="subtitle">Sistem Manajemen Aset IT</div>
    </div>
    
    <div class="receipt-title">BUKTI PEMINJAMAN ASET</div>
    
    <div class="main-content">
        <div class="left-col">
            <div class="section-title">DATA ASET</div>
            <table>
                <tr><th>Kode</th><th>Nama</th></tr>
                <tr>
                    <td class="text-center"><strong>{{ $loan->asset->asset_code }}</strong></td>
                    <td>{{ $loan->asset->name }}</td>
                </tr>
            </table>
            <div class="info-row"><span class="info-label">Kategori</span><span class="info-value">{{ $loan->asset->category->name ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label">S/N</span><span class="info-value">{{ $loan->asset->serial_number ?? '-' }}</span></div>
            <div class="info-row"><span class="info-label">Lokasi</span><span class="info-value">{{ $loan->asset->location->name ?? '-' }}</span></div>
            
            <div class="section-title">PEMINJAM</div>
            <div class="info-row"><span class="info-label">Nama</span><span class="info-value"><strong>{{ $loan->user->name }}</strong></span></div>
            @if($loan->purpose)
            <div class="info-row"><span class="info-label">Tujuan</span><span class="info-value">{{ $loan->purpose }}</span></div>
            @endif
        </div>
        
        <div class="right-col">
            <div class="section-title">DETAIL</div>
            <div class="info-row"><span class="info-label">No.</span><span class="info-value"><strong>{{ $loan->loan_code }}</strong></span></div>
            <div class="info-row"><span class="info-label">Tgl Pinjam</span><span class="info-value">{{ $loan->loan_date->format('d M Y') }}</span></div>
            <div class="info-row"><span class="info-label">Est. Kembali</span><span class="info-value">{{ $loan->expected_return_date->format('d M Y') }}</span></div>
            @if($loan->actual_return_date)
            <div class="info-row"><span class="info-label">Tgl Kembali</span><span class="info-value">{{ $loan->actual_return_date->format('d M Y') }}</span></div>
            @endif
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    <span class="badge badge-{{ $loan->status === 'returned' ? 'returned' : ($loan->status === 'overdue' ? 'overdue' : ($loan->status === 'pending' ? 'pending' : 'active')) }}">
                        {{ $loan->status_label }}
                    </span>
                </span>
            </div>
            
            @if($loan->condition_before)
            <div class="info-row"><span class="info-label">Kondisi Awal</span><span class="info-value">{{ $loan->condition_before }}</span></div>
            @endif
            @if($loan->condition_after)
            <div class="info-row"><span class="info-label">Kondisi Akhir</span><span class="info-value">{{ $loan->condition_after }}</span></div>
            @endif
            
            @if($loan->fine_amount > 0)
            <div class="fine-box">
                <span class="text-danger"><strong>⚠ Denda: Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</strong></span>
                <span style="float:right;">{{ $loan->fine_paid ? '✅ Lunas' : '❌ Belum' }}</span>
            </div>
            @endif
            
            <div class="terms">
                <strong>Ketentuan:</strong> Peminjam bertanggung jawab atas aset. Kerusakan akibat kelalaian ditanggung peminjam. Denda keterlambatan Rp 10.000/hari.
            </div>
            
            <!-- SIGNATURE - PAKSA 1 BARIS -->
            <table style="margin-top: 10px; border: none !important;">
                <tr style="border: none !important;">
                    <td style="width: 33%; text-align: center; border: none !important; font-size: 6px;">
                        <div style="margin-top: 20px; border-top: 1px solid #333; padding-top: 2px; font-weight: 600;">Peminjam</div>
                        <small>({{ $loan->user->name }})</small>
                    </td>
                    <td style="width: 33%; text-align: center; border: none !important; font-size: 6px;">
                        <div style="margin-top: 20px; border-top: 1px solid #333; padding-top: 2px; font-weight: 600;">Petugas</div>
                        <small>({{ $loan->approver->name ?? '............' }})</small>
                    </td>
                    <td style="width: 33%; text-align: center; border: none !important; font-size: 6px;">
                        <div style="margin-top: 20px; border-top: 1px solid #333; padding-top: 2px; font-weight: 600;">Mengetahui</div>
                        <small>(Kepala Dept.)</small>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="footer">
        Dicetak: {{ now()->format('d/m/Y H:i') }} | {{ $systemName }} | {{ $companyName }}
    </div>

</body>
</html>