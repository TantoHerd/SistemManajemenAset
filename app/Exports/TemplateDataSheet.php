<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TemplateDataSheet implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'AST-001',
                'Dell XPS 15 Laptop',
                'SN123456789',
                'XPS 15 9520',
                'Dell',
                'LAP',
                'GED-A-LT1',
                'tersedia',
                '2024-01-15',
                '25000000',
                '2500000',
                '48',
                '2026-01-15',
                'Laptop untuk tim IT - Spesifikasi tinggi'
            ],
            [
                'AST-002',
                'MacBook Pro 14',
                'SN987654321',
                'M3 Pro',
                'Apple',
                'LAP',
                'GED-A-LT2',
                'dipakai',
                '2024-02-20',
                '30000000',
                '3000000',
                '48',
                '2026-02-20',
                'Laptop untuk tim desain'
            ],
            [
                '',
                'HP EliteBook 840',
                'SN555555555',
                'EliteBook 840 G8',
                'HP',
                'LAP',
                'GED-B-LT1',
                'maintenance',
                '2023-11-10',
                '18000000',
                '1800000',
                '48',
                '2025-11-10',
                'Sedang dalam perbaikan'
            ],
        ];
    }

    public function headings(): array
    {
        return [
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
            'catatan'
        ];
    }

    public function title(): string
    {
        return 'Data Aset';
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
        ]);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Border for all data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:N' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        // Add filter
        $sheet->setAutoFilter('A1:N1');

        // Column widths
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('N')->setWidth(40);
    }
}