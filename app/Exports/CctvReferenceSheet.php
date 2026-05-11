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

class CctvReferenceSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function title(): string
    {
        return 'Referensi';
    }

    public function array(): array
    {
        return [
            ['DAFTAR BRAND CCTV & KONFIGURASI UMUM', '', '', '', ''],
            ['', '', '', '', ''],
            ['BRAND', 'DEFAULT PORT', 'STREAM PROTOCOL', 'SNAPSHOT URL', 'CATATAN'],
            ['Hikvision', '80/554', 'RTSP', '/ISAPI/Streaming/channels/1/picture', 'Digest Authentication'],
            ['Dahua', '80/554', 'RTSP', '/cgi-bin/snapshot.cgi?channel=1', 'Basic/Digest Auth'],
            ['CP Plus', '80/554', 'RTSP', '/webcapture.jpg?command=snap', 'Basic Authentication'],
            ['Uniview', '80/554', 'RTSP', '/snapshot.jpg', 'Basic Authentication'],
            ['Xiaomi', '80', 'RTSP', '', 'Beberapa model tidak support snapshot'],
            ['TP-Link', '80/554', 'RTSP', '/snapshot.jpg', 'Basic Authentication'],
            ['', '', '', '', ''],
            ['KONFIGURASI JARINGAN', '', '', '', ''],
            ['Pastikan CCTV dan server dalam 1 jaringan (LAN)', '', '', '', ''],
            ['IP CCTV harus static (tidak berubah)', '', '', '', ''],
            ['Port default HTTP: 80, RTSP: 554', '', '', '', ''],
            ['Gunakan username & password yang benar', '', '', '', ''],
            ['', '', '', '', ''],
            ['TIPS TROUBLESHOOTING', '', '', '', ''],
            ['1. Test ping ke IP CCTV: ping 192.168.1.100', '', '', '', ''],
            ['2. Buka snapshot URL di browser untuk test', '', '', '', ''],
            ['3. Pastikan firewall tidak memblokir port', '', '', '', ''],
            ['4. Cek kabel dan koneksi jaringan', '', '', '', ''],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Title
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
        ]);
        $sheet->mergeCells('A1:E1');

        // Table header
        $sheet->getStyle('A3:E3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Data rows
        $sheet->getStyle('A4:E9')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Section headers
        $sheet->getStyle('A11')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1E3A5F']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
        ]);
        $sheet->mergeCells('A11:E11');

        $sheet->getStyle('A17')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1E3A5F']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
        ]);
        $sheet->mergeCells('A17:E17');

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(45);
        $sheet->getColumnDimension('E')->setWidth(30);
    }
}