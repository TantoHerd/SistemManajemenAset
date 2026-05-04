<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\Loan;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportSummarySheet implements FromArray, WithTitle, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function title(): string { return 'Ringkasan'; }

    public function array(): array
    {
        $dateFrom = $this->filters['date_from'] ?? now()->subYear()->format('Y-m-d');
        $dateTo = $this->filters['date_to'] ?? now()->format('Y-m-d');
        $company = Setting::where('key', 'company_name')->value('value') ?? 'PT. NAMA';
        $system = Setting::where('key', 'system_name')->value('value') ?? 'SIMASET';

        $totalValue = Asset::sum('current_value');
        $totalPurchase = Asset::sum('purchase_price');
        $totalMtcCost = Maintenance::whereBetween('maintenance_date', [$dateFrom, $dateTo])->sum('cost');
        $totalLoans = Loan::whereBetween('loan_date', [$dateFrom, $dateTo])->count();

        return [
            [$company], ['LAPORAN ASET IT'], ['Periode: ' . $dateFrom . ' s/d ' . $dateTo], [''],
            ['Total Nilai Aset', 'Rp ' . number_format($totalValue, 0, ',', '.')],
            ['Total Pembelian', 'Rp ' . number_format($totalPurchase, 0, ',', '.')],
            ['Total Biaya Maintenance', 'Rp ' . number_format($totalMtcCost, 0, ',', '.')],
            ['Total Peminjaman', $totalLoans],
            [''], ['Dicetak: ' . now()->format('d/m/Y H:i') . ' | ' . $system],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:A2')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
    }
}