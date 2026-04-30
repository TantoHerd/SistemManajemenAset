<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Category;
use App\Models\Location;
use App\Models\CategorySpecification;

class TemplateReferenceSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function title(): string
    {
        return 'Referensi';
    }

    public function array(): array
    {
        $data = [];
        
        // ============================================
        // SECTION 1: KATEGORI
        // ============================================
        $data[] = ['DAFTAR KATEGORI', '', '', ''];
        $data[] = ['KODE', 'NAMA', 'MASA MANFAAT (BULAN)', 'JUMLAH SPESIFIKASI'];
        
        $categories = Category::withCount('specifications')->orderBy('name')->get();
        foreach ($categories as $cat) {
            $data[] = [
                $cat->code,
                $cat->name,
                $cat->useful_life_months,
                $cat->specifications_count
            ];
        }
        
        $data[] = ['', '', '', ''];
        
        // ============================================
        // SECTION 2: LOKASI
        // ============================================
        $data[] = ['DAFTAR LOKASI', '', '', ''];
        $data[] = ['KODE', 'NAMA', 'FULL PATH', 'SUB LOKASI'];
        
        $locations = Location::withCount('children')->orderBy('name')->get();
        foreach ($locations as $loc) {
            $data[] = [
                $loc->code,
                $loc->name,
                $loc->full_path ?? $loc->name,
                $loc->children_count
            ];
        }
        
        $data[] = ['', '', '', ''];
        
        // ============================================
        // SECTION 3: SPESIFIKASI
        // ============================================
        $data[] = ['DAFTAR SPESIFIKASI PER KATEGORI', '', '', ''];
        $data[] = ['KATEGORI', 'LABEL SPESIFIKASI', 'TIPE', 'WAJIB'];
        
        $specs = CategorySpecification::with('category')
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->get();
            
        foreach ($specs as $spec) {
            $data[] = [
                $spec->category->name ?? '-',
                $spec->label,
                $this->getTypeLabel($spec->type),
                $spec->is_required ? '✅ Ya' : '❌ Tidak'
            ];
        }
        
        $data[] = ['', '', '', ''];
        $data[] = ['Tips: Gunakan KODE (bukan nama) saat mengisi template import!', '', '', ''];
        
        return $data;
    }

    private function getTypeLabel($type)
    {
        return [
            'text' => 'Text', 'number' => 'Angka', 'textarea' => 'Teks Panjang',
            'date' => 'Tanggal', 'boolean' => 'Ya/Tidak', 'select' => 'Pilihan'
        ][$type] ?? $type;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Section headers
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->mergeCells('A1:D1');
        
        // Table headers
        $sheet->getStyle('A2:D2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
        ]);
        
        // Find section 2 & 3 headers dynamically
        $catEndRow = Category::count() + 2;
        $locStartRow = $catEndRow + 2;
        $locEndRow = $locStartRow + Location::count() + 1;
        $specStartRow = $locEndRow + 2;
        
        if ($locStartRow <= $lastRow) {
            $sheet->getStyle('A' . $locStartRow . ':D' . $locStartRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->mergeCells('A' . $locStartRow . ':D' . $locStartRow);
            
            $sheet->getStyle('A' . ($locStartRow + 1) . ':D' . ($locStartRow + 1))->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            ]);
        }
        
        if ($specStartRow <= $lastRow) {
            $sheet->getStyle('A' . $specStartRow . ':D' . $specStartRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E65100']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $sheet->mergeCells('A' . $specStartRow . ':D' . $specStartRow);
            
            $sheet->getStyle('A' . ($specStartRow + 1) . ':D' . ($specStartRow + 1))->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            ]);
        }
        
        // Borders
        $sheet->getStyle('A1:D' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
        ]);
        
        // Alternating row colors
        for ($i = 3; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':D' . $i)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FC']],
                ]);
            }
        }
        
        // Column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(20);
    }
}