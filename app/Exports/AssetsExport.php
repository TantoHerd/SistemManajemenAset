<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AssetsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    ShouldAutoSize,
    WithProperties,
    WithTitle,
    WithEvents
{
    protected $filters;
    protected $totalAssets;
    protected $totalValue;
    protected $totalPurchaseValue;
    protected $exportDate;
    protected $companyName;
    protected $systemName;
    
    // Koleksi spesifikasi dari semua kategori
    protected $allSpecKeys = [];

    public function __construct($filters = [])
    {
        $this->filters = $filters;
        $this->exportDate = now();
        
        try {
            $company = Setting::where('key', 'company_name')->first();
            $this->companyName = $company ? $company->value : 'PT. NAMA PERUSAHAAN';
            
            $system = Setting::where('key', 'system_name')->first();
            $this->systemName = $system ? $system->value : 'Sistem Manajemen Aset IT';
        } catch (\Exception $e) {
            $this->companyName = 'PT. NAMA PERUSAHAAN';
            $this->systemName = 'Sistem Manajemen Aset IT';
        }
        
        // Kumpulkan semua spec key dari semua kategori
        $this->allSpecKeys = \App\Models\CategorySpecification::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('label', 'key')
            ->toArray();
    }

    public function collection()
    {
        $query = Asset::with(['category', 'location', 'assignedTo', 'specifications']);

        if (!empty($this->filters['category'])) {
            $query->where('category_id', $this->filters['category']);
        }

        if (!empty($this->filters['location'])) {
            $locationIds = $this->getLocationIds($this->filters['location']);
            $query->whereIn('location_id', $locationIds);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['search'])) {
            $query->where(function($q) {
                $q->where('asset_code', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('serial_number', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        $assets = $query->get();
        
        $this->totalAssets = $assets->count();
        $this->totalValue = $assets->sum('current_value');
        $this->totalPurchaseValue = $assets->sum('purchase_price');
        
        return $assets;
    }

    private function getLocationIds($parentId)
    {
        $ids = [$parentId];
        $children = \App\Models\Location::where('parent_id', $parentId)->get();
        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getLocationIds($child->id));
        }
        return $ids;
    }

    public function properties(): array
    {
        return [
            'creator'        => $this->systemName,
            'lastModifiedBy' => $this->systemName,
            'title'          => 'Laporan Data Aset',
            'description'    => 'Laporan lengkap data aset IT perusahaan termasuk spesifikasi',
            'subject'        => 'Data Aset',
            'keywords'       => 'aset, inventory, it asset, management, spesifikasi',
            'category'       => 'Laporan',
            'manager'        => 'IT Department',
            'company'        => $this->companyName,
        ];
    }

    public function title(): string
    {
        return 'Data Aset';
    }

    public function headings(): array
    {
        $baseHeadings = [
            'NO',
            'KODE ASET',
            'NAMA ASET',
            'SERIAL NUMBER',
            'MODEL',
            'BRAND',
            'KATEGORI',
            'LOKASI',
            'FULL PATH',
            'STATUS',
            'PENGGUNA',
            'TGL BELI',
            'HARGA BELI',
            'NILAI RESIDU',
            'MASA MANFAAT',
            'NILAI SAAT INI',
            'PENYUSUTAN',
            'GARANSI',
            'CATATAN',
            'UPDATE TERAKHIR',
        ];
        
        // Tambahkan heading spesifikasi
        foreach ($this->allSpecKeys as $key => $label) {
            $baseHeadings[] = strtoupper($label);
        }
        
        return $baseHeadings;
    }

    public function map($asset): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $baseData = [
            $rowNumber,
            $asset->asset_code,
            $asset->name,
            $asset->serial_number ?? '-',
            $asset->model ?? '-',
            $asset->brand ?? '-',
            $asset->category->name ?? '-',
            $asset->location->name ?? '-',
            $asset->location->full_path ?? '-',
            $asset->status_label,
            $asset->assignedTo->name ?? '-',
            $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '-',
            $asset->purchase_price,
            $asset->residual_value,
            $asset->useful_life_months . ' bulan',
            $asset->current_value,
            $asset->depreciation_percentage . '%',
            $asset->is_under_warranty ? 'Aktif' : 'Kadaluarsa',
            $asset->notes ?? '-',
            $asset->updated_at->format('d/m/Y H:i'),
        ];
        
        // Ambil spesifikasi aset
        $assetSpecs = $asset->specifications->pluck('spec_value', 'spec_key');
        
        // Tambahkan nilai spesifikasi sesuai urutan
        foreach ($this->allSpecKeys as $key => $label) {
            $baseData[] = $assetSpecs[$key] ?? '-';
        }
        
        return $baseData;
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
                
                // Hitung jumlah kolom (base + spesifikasi)
                $baseColumns = 20; // A-T
                $specCount = count($this->allSpecKeys);
                $totalColumns = $baseColumns + $specCount;
                $lastColumn = $this->getColumnLetter($totalColumns);
                
                // HEADER (4 baris pertama)
                $sheet->insertNewRowBefore(1, 4);
                
                // Baris 1: Nama Perusahaan
                $sheet->mergeCells('A1:' . $lastColumn . '1');
                $sheet->setCellValue('A1', $this->companyName);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A5F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Baris 2: Judul
                $sheet->mergeCells('A2:' . $lastColumn . '2');
                $sheet->setCellValue('A2', 'LAPORAN DATA ASET IT');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Baris 3: Periode
                $sheet->mergeCells('A3:' . $lastColumn . '3');
                $sheet->setCellValue('A3', 'Periode: ' . $this->exportDate->format('d F Y'));
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Baris 4: Ringkasan
                $sheet->mergeCells('A4:B4');
                $sheet->setCellValue('A4', 'Total Aset:');
                $sheet->setCellValue('C4', $this->totalAssets);
                $sheet->mergeCells('E4:F4');
                $sheet->setCellValue('E4', 'Total Nilai Pembelian:');
                $sheet->setCellValue('G4', 'Rp ' . number_format($this->totalPurchaseValue, 0, ',', '.'));
                $sheet->mergeCells('I4:J4');
                $sheet->setCellValue('I4', 'Total Nilai Saat Ini:');
                $sheet->setCellValue('K4', 'Rp ' . number_format($this->totalValue, 0, ',', '.'));
                $sheet->mergeCells('M4:N4');
                $sheet->setCellValue('M4', 'Total Penyusutan:');
                $sheet->setCellValue('O4', 'Rp ' . number_format($this->totalPurchaseValue - $this->totalValue, 0, ',', '.'));
                $sheet->getStyle('A4')->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('E4')->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('I4')->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('M4')->applyFromArray(['font' => ['bold' => true]]);
                
                // HEADER TABLE (baris 5)
                $headerRow = 5;
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2E7D32']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(22);
                
                // Warnai header spesifikasi beda
                if ($specCount > 0) {
                    $specStartCol = $this->getColumnLetter($baseColumns + 1);
                    $sheet->getStyle($specStartCol . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '1565C0']
                        ],
                    ]);
                }
                
                // DATA ROWS
                $dataStartRow = $headerRow + 1;
                $dataEndRow = $sheet->getHighestRow();
                
                // Alternating colors
                for ($i = $dataStartRow; $i <= $dataEndRow; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A' . $i . ':' . $lastColumn . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8F9FC']
                            ]
                        ]);
                    }
                }
                
                // Borders
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $dataEndRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB']
                        ]
                    ]
                ]);
                
                // Format Rupiah
                $currencyColumns = ['M', 'N', 'P'];
                foreach ($currencyColumns as $col) {
                    $sheet->getStyle($col . $dataStartRow . ':' . $col . $dataEndRow)
                          ->getNumberFormat()
                          ->setFormatCode('#,##0');
                }
                
                // Format Persentase
                $sheet->getStyle('Q' . $dataStartRow . ':Q' . $dataEndRow)
                      ->getNumberFormat()
                      ->setFormatCode('0.00"%";');
                
                // Freeze header
                $sheet->freezePane('A' . ($headerRow + 1));
                
                // Auto filter
                $sheet->setAutoFilter('A' . $headerRow . ':' . $lastColumn . $headerRow);
                
                // FOOTER
                $footerRow = $dataEndRow + 2;
                $sheet->mergeCells('A' . $footerRow . ':' . $lastColumn . $footerRow);
                $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . $this->exportDate->format('d/m/Y H:i:s') . ' | ' . $this->systemName . ' | Total Spesifikasi: ' . $specCount);
                $sheet->getStyle('A' . $footerRow)->applyFromArray([
                    'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '6C757D']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
    
    /**
     * Convert column number to letter (1=A, 2=B, ..., 26=Z, 27=AA, ...)
     */
    private function getColumnLetter($number)
    {
        $letter = '';
        while ($number > 0) {
            $number--;
            $letter = chr(65 + ($number % 26)) . $letter;
            $number = intval($number / 26);
        }
        return $letter;
    }
}