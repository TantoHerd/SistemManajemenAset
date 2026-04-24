<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AmortizationExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    ShouldAutoSize,
    WithTitle,
    WithEvents
{
    protected $exportDate;
    protected $totalPurchaseValue;
    protected $totalCurrentValue;
    protected $totalDepreciation;

    public function __construct()
    {
        $this->exportDate = now();
        
        $assets = Asset::all();
        $this->totalPurchaseValue = $assets->sum('purchase_price');
        $this->totalCurrentValue = $assets->sum('current_value');
        $this->totalDepreciation = $this->totalPurchaseValue - $this->totalCurrentValue;
    }

    public function collection()
    {
        return Asset::with('category')->get();
    }

    public function title(): string
    {
        return 'Laporan Amortisasi';
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE ASET',
            'NAMA ASET',
            'KATEGORI',
            'TANGGAL BELI',
            'HARGA BELI (Rp)',
            'NILAI RESIDU (Rp)',
            'MASA MANFAAT',
            'NILAI SAAT INI (Rp)',
            'TOTAL PENYUSUTAN (Rp)',
            'PERSENTASE (%)',
            'PENYUSUTAN/BULAN (Rp)',
            'BULAN TERPAKAI',
            'SISA BULAN',
        ];
    }

    public function map($asset): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $monthsPassed = $asset->purchase_date->diffInMonths(now());
        $remainingMonths = max(0, $asset->useful_life_months - $monthsPassed);
        $totalDepreciation = $asset->purchase_price - $asset->current_value;

        return [
            $rowNumber,
            $asset->asset_code,
            $asset->name,
            $asset->category->name ?? '-',
            $asset->purchase_date->format('d/m/Y'),
            $asset->purchase_price,
            $asset->residual_value,
            $asset->useful_life_months . ' bulan',
            $asset->current_value,
            $totalDepreciation,
            $asset->depreciation_percentage . '%',
            round($asset->calculateMonthlyDepreciation(), 2),
            $monthsPassed . ' bulan',
            $remainingMonths . ' bulan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                
                // Title
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells('A1:N1');
                $sheet->setCellValue('A1', 'PT. NAMA PERUSAHAAN');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '4361EE']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                $sheet->mergeCells('A2:N2');
                $sheet->setCellValue('A2', 'LAPORAN AMORTISASI ASET');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                $sheet->mergeCells('A3:N3');
                $sheet->setCellValue('A3', 'Periode: ' . $this->exportDate->format('d F Y'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Summary
                $sheet->mergeCells('A4:B4');
                $sheet->setCellValue('A4', 'Total Nilai Pembelian:');
                $sheet->setCellValue('C4', 'Rp ' . number_format($this->totalPurchaseValue, 0, ',', '.'));
                $sheet->mergeCells('E4:F4');
                $sheet->setCellValue('E4', 'Total Nilai Saat Ini:');
                $sheet->setCellValue('G4', 'Rp ' . number_format($this->totalCurrentValue, 0, ',', '.'));
                $sheet->mergeCells('I4:J4');
                $sheet->setCellValue('I4', 'Total Penyusutan:');
                $sheet->setCellValue('K4', 'Rp ' . number_format($this->totalDepreciation, 0, ',', '.'));
                $sheet->getStyle('A4')->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('E4')->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('I4')->applyFromArray(['font' => ['bold' => true]]);
                
                // Header
                $headerRow = 5;
                $headerRange = 'A' . $headerRow . ':' . $lastColumn . $headerRow;
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Borders
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]]
                ]);
                
                // Footer
                $footerRow = $lastRow + 2;
                $sheet->mergeCells('A' . $footerRow . ':' . $lastColumn . $footerRow);
                $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . $this->exportDate->format('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $footerRow)->applyFromArray([
                    'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '6C757D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}