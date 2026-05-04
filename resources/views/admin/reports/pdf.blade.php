<!DOCTYPE html>
<html><head>
    <meta charset="utf-8">
    <title>Laporan Aset</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; font-size: 16px; margin-bottom: 0; }
        h3 { text-align: center; font-size: 13px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #2E7D32; color: white; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border: 1px solid #ddd; }
        .summary { margin-bottom: 20px; }
        .summary strong { display: inline-block; width: 200px; }
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 30px; }
    </style>
</head><body>
    <h1>{{ $companyName }}</h1>
    <h3>Laporan Aset IT | Periode: {{ $dateFrom }} s/d {{ $dateTo }}</h3>
    
    <div class="summary">
        <p><strong>Total Nilai Aset:</strong> Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
        <p><strong>Total Pembelian:</strong> Rp {{ number_format($totalPurchaseValue, 0, ',', '.') }}</p>
        <p><strong>Biaya Maintenance:</strong> Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}</p>
    </div>
    
    <h4>Data Aset ({{ $assets->count() }})</h4>
    <table>
        <tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Lokasi</th><th>Status</th><th>Nilai</th></tr>
        @foreach($assets->take(50) as $a)
        <tr>
            <td>{{ $a->asset_code }}</td><td>{{ $a->name }}</td>
            <td>{{ $a->category->name ?? '-' }}</td><td>{{ $a->location->name ?? '-' }}</td>
            <td>{{ $a->status_label }}</td><td>{{ number_format($a->current_value, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>
    
    <div class="footer">Dicetak: {{ now()->format('d/m/Y H:i') }} | {{ $systemName }}</div>
</body></html>