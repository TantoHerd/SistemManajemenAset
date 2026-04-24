<?php

namespace App\Exports;

use App\Models\User;
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

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    protected $exportDate;

    public function __construct()
    {
        $this->exportDate = now();
    }

    public function collection()
    {
        return User::with('roles')->get();
    }

    public function title(): string
    {
        return 'Data User';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'EMAIL',
            'TELEPON',
            'ALAMAT',
            'ROLE',
            'STATUS',
            'TERAKHIR LOGIN',
            'BERGABUNG SEJAK',
        ];
    }

    public function map($user): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $user->name,
            $user->email,
            $user->phone ?? '-',
            $user->address ?? '-',
            $user->roles->first()->name ?? 'user',
            $user->status === 'active' ? 'Aktif' : 'Nonaktif',
            $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '-',
            $user->created_at->format('d/m/Y H:i'),
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
                $lastColumn = 'I';
                
                // Title
                $sheet->insertNewRowBefore(1, 4);
                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', 'LAPORAN DATA USER');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A5F']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'Periode: ' . $this->exportDate->format('d F Y H:i:s'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'italic' => true, 'color' => ['rgb' => '666666']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Header
                $headerRow = 4;
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2E7D32']
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                
                // Borders
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
                
                // Footer
                $footerRow = $lastRow + 2;
                $sheet->mergeCells('A' . $footerRow . ':' . $lastColumn . $footerRow);
                $sheet->setCellValue('A' . $footerRow, 'Dicetak pada: ' . $this->exportDate->format('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $footerRow)->applyFromArray([
                    'font' => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '999999']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}