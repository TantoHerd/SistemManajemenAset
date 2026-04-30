<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class UserTemplateSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Template Import';
    }

    public function headings(): array
    {
        return [
            'NAMA LENGKAP',
            'EMAIL',
            'PASSWORD',
            'TELEPON',
            'ALAMAT',
            'ROLE',
            'STATUS',
        ];
    }

    public function array(): array
    {
        return [
            // Contoh data
            ['Budi Santoso', 'budi@example.com', 'password123', '08123456789', 'Jl. Sudirman No. 1, Jakarta', 'user', 'Aktif'],
            ['Siti Rahayu', 'siti@example.com', 'password123', '08198765432', 'Jl. Thamrin No. 2, Bandung', 'admin', 'Aktif'],
            ['', '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style (Row 1)
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E7D32']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]
            ]
        ]);

        // Example data style (Row 2-3)
        $sheet->getStyle('A2:G3')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF3E0']
            ],
            'font' => ['color' => ['rgb' => 'E65100'], 'italic' => true, 'size' => 10],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]
            ]
        ]);

        // Empty rows style
        $sheet->getStyle('A4:G6')->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]
            ]
        ]);

        // Row height
        $sheet->getRowDimension(1)->setRowHeight(25);
        for ($i = 2; $i <= 6; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(22);
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Freeze header
        $sheet->freezePane('A2');

        // Add data validation for STATUS column (G)
        for ($i = 2; $i <= 100; $i++) {
            $sheet->getCell('G' . $i)->getDataValidation()
                ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                ->setAllowBlank(true)
                ->setShowInputMessage(false)
                ->setShowErrorMessage(true)
                ->setErrorTitle('Status Tidak Valid')
                ->setError('Pilih: Aktif atau Nonaktif')
                ->setFormula1('"Aktif,Nonaktif"');
        }

        // Add data validation for ROLE column (F)
        $roles = \Spatie\Permission\Models\Role::pluck('name')->join(',');
        for ($i = 2; $i <= 100; $i++) {
            $sheet->getCell('F' . $i)->getDataValidation()
                ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP)
                ->setAllowBlank(true)
                ->setShowInputMessage(false)
                ->setShowErrorMessage(true)
                ->setErrorTitle('Role Tidak Valid')
                ->setError('Pilih role yang tersedia')
                ->setFormula1('"' . $roles . '"');
        }

        return [];
    }
}