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

class CctvTemplateSheet implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    public function title(): string
    {
        return 'Template Import';
    }

    public function headings(): array
    {
        return [
            'NAMA CCTV',
            'IP ADDRESS',
            'PORT',
            'USERNAME',
            'PASSWORD',
            'BRAND',
            'MODEL',
            'STREAM URL',
            'SNAPSHOT URL',
            'LOKASI',
        ];
    }

    public function array(): array
    {
        return [
            ['CCTV Lantai 1 - Depan', '192.168.1.100', 80, 'admin', 'password123', 'Hikvision', 'DS-2CD1021-I', 'rtsp://192.168.1.100:554/Streaming/Channels/1', 'http://192.168.1.100/ISAPI/Streaming/channels/1/picture', 'Lantai 1 - Depan'],
            ['CCTV Lantai 1 - Belakang', '192.168.1.101', 80, 'admin', 'password123', 'Dahua', 'IPC-HDW1230T', 'rtsp://192.168.1.101:554/cam/realmonitor?channel=1&subtype=0', 'http://192.168.1.101/cgi-bin/snapshot.cgi?channel=1', 'Lantai 1 - Belakang'],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Example rows
        $sheet->getStyle('A2:J3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
            'font' => ['italic' => true, 'color' => ['rgb' => 'E65100'], 'size' => 10],
        ]);

        // Empty rows border
        $sheet->getStyle('A4:J6')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        // Freeze
        $sheet->freezePane('A2');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                
                // Dropdown Brand (F)
                $brands = '"Hikvision,Dahua,CP Plus,Uniview,Xiaomi,TP-Link,Other"';
                for ($i = 2; $i <= 100; $i++) {
                    $sheet->getCell('F' . $i)->getDataValidation()
                        ->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setAllowBlank(true)
                        ->setShowDropDown(true)
                        ->setFormula1($brands);
                }
            },
        ];
    }
}