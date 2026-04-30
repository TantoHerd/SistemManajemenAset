<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use App\Models\Category;
use App\Models\Location;
use App\Models\CategorySpecification;

class TemplateDataSheet implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    protected $specKeys;
    protected $specKeysCount; // BARU
    protected $categories;
    protected $locations;
    protected $catCodes;
    protected $locCodes;

    public function __construct()
    {
        $this->categories = Category::orderBy('name')->get();
        $this->locations = Location::orderBy('name')->get();
        
        // Load spesifikasi aktif
        $this->specKeys = CategorySpecification::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['key', 'label']);

        $this->specKeys = CategorySpecification::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['key', 'label']);
        
        $this->specKeysCount = $this->specKeys->count();
    }

    public function title(): string
    {
        return 'Data Aset';
    }

    public function headings(): array
    {
        $base = [
            'kode_aset',
            'nama_aset',
            'serial_number',
            'model',
            'brand',
            'kode_kategori',
            'kode_lokasi',
            'status',
            'tanggal_beli',
            'harga_beli',
            'nilai_residu',
            'masa_manfaat',
            'garansi_berakhir',
            'catatan',
        ];
        
        // Tambah heading spesifikasi
        foreach ($this->specKeys as $spec) {
            $base[] = $spec->label;
        }
        
        return $base;
    }

    public function array(): array
    {
        // Data contoh
        $exampleData = [
            'AST-001', 'Dell XPS 15 Laptop', 'SN123456789', 'XPS 15 9520', 'Dell',
            $this->categories->first()->code ?? 'LAP',
            $this->locations->first()->code ?? 'GED-A-LT1',
            'tersedia', '2024-01-15', '25000000', '2500000', '48', '2026-01-15',
            'Laptop untuk tim IT (CONTOH - hapus baris ini)'
        ];
        
        // Tambah contoh spesifikasi
        foreach ($this->specKeys as $spec) {
            $exampleData[] = $this->getExampleSpecValue($spec->key);
        }
        
        return [
            $exampleData,
            array_fill(0, count($this->headings()), ''), // baris kosong
            array_fill(0, count($this->headings()), ''), // baris kosong
            array_fill(0, count($this->headings()), ''), // baris kosong
        ];
    }

    private function getExampleSpecValue($key)
    {
        $examples = [
            'processor' => 'Intel Core i7-1260P',
            'ram' => '16 GB',
            'storage' => '512 GB SSD',
            'ukuran_layar' => '15.6 inch',
            'os' => 'Windows 11 Pro',
            'tipe_printer' => 'Laser',
            'kecepatan_cetak' => '30 ppm',
            'konektivitas' => 'WiFi, USB',
        ];
        return $examples[$key] ?? 'Contoh ' . $key;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $this->getColumnLetter(count($this->headings()));
        
        // Header style
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
        ]);

        // Highlight spesifikasi columns
        if ($this->specKeysCount > 0) {
            $specStartCol = $this->getColumnLetter(15); // Kolom O (setelah N)
            $sheet->getStyle($specStartCol . '1:' . $lastCol . '1')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1565C0']
                ],
            ]);
        }

        $sheet->getRowDimension(1)->setRowHeight(25);

        // Example row style (baris 2 - kuning)
        $sheet->getStyle('A2:' . $lastCol . '2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF8E1']
            ],
            'font' => ['italic' => true, 'color' => ['rgb' => 'E65100'], 'size' => 10],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'D84315']],
        ]);

        // Borders
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        // Freeze header
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:' . $lastCol . '1');

        // Column widths
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('N')->setWidth(40);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $lastCol = $this->getColumnLetter(count($this->headings()));
                $lastRow = 100; // Apply sampai row 100
                
                // ============================================
                // DROPDOWN VALIDATION: Status (Kolom H)
                // ============================================
                $statusFormula = '"tersedia,dipakai,maintenance,rusak,dihapus"';
                for ($i = 2; $i <= $lastRow; $i++) {
                    $sheet->getCell('H' . $i)->getDataValidation()
                        ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setShowInputMessage(true)
                        ->setPromptTitle('Status Aset')
                        ->setPrompt('Pilih status dari dropdown')
                        ->setShowErrorMessage(true)
                        ->setErrorTitle('Status Tidak Valid')
                        ->setError('Pilih: tersedia, dipakai, maintenance, rusak, atau dihapus')
                        ->setFormula1($statusFormula);
                }
                
                // ============================================
                // DROPDOWN VALIDATION: Kategori (Kolom F)
                // ============================================
                $catCodes = $this->categories->pluck('code')->join(',');
                $catFormula = '"' . $catCodes . '"';
                for ($i = 2; $i <= $lastRow; $i++) {
                    $sheet->getCell('F' . $i)->getDataValidation()
                        ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setShowInputMessage(true)
                        ->setPromptTitle('Kode Kategori')
                        ->setPrompt('Pilih kode kategori (lihat sheet Referensi)')
                        ->setShowErrorMessage(true)
                        ->setErrorTitle('Kode Tidak Valid')
                        ->setError('Gunakan kode yang tersedia di sheet Referensi')
                        ->setFormula1($catFormula);
                }
                
                // ============================================
                // DROPDOWN VALIDATION: Lokasi (Kolom G)
                // ============================================
                $locCodes = $this->locations->pluck('code')->join(',');
                $locFormula = '"' . $locCodes . '"';
                for ($i = 2; $i <= $lastRow; $i++) {
                    $sheet->getCell('G' . $i)->getDataValidation()
                        ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setShowInputMessage(true)
                        ->setPromptTitle('Kode Lokasi')
                        ->setPrompt('Pilih kode lokasi (lihat sheet Referensi)')
                        ->setShowErrorMessage(true)
                        ->setErrorTitle('Kode Tidak Valid')
                        ->setError('Gunakan kode yang tersedia di sheet Referensi')
                        ->setFormula1($locFormula);
                }
                
                // Protect header row
                $sheet->getStyle('A1:' . $lastCol . '1')->getProtection()
                    ->setLocked(Protection::PROTECTION_PROTECTED);
            },
        ];
    }

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