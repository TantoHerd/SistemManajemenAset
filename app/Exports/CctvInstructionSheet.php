<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CctvInstructionSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function title(): string
    {
        return 'Petunjuk';
    }

    public function array(): array
    {
        return [
            ['📹 PANDUAN IMPORT DATA CCTV', ''],
            ['', ''],
            ['⚠️ SEBELUM MENGISI, BACA PETUNJUK INI!', ''],
            ['', ''],
            ['A. KOLOM WAJIB (HARUS DIISI)', ''],
            ['KOLOM', 'KETERANGAN'],
            ['NAMA CCTV', 'Nama untuk identifikasi CCTV (contoh: CCTV Lantai 1)'],
            ['IP ADDRESS', 'Alamat IP CCTV di jaringan (contoh: 192.168.1.100)'],
            ['', ''],
            ['B. KOLOM OPSIONAL', ''],
            ['KOLOM', 'KETERANGAN'],
            ['PORT', 'Port HTTP CCTV (default: 80)'],
            ['USERNAME', 'Username login CCTV'],
            ['PASSWORD', 'Password login CCTV'],
            ['BRAND', 'Merek CCTV (dropdown tersedia)'],
            ['MODEL', 'Tipe/model CCTV'],
            ['STREAM URL', 'URL untuk live streaming (RTSP)'],
            ['SNAPSHOT URL', 'URL untuk mengambil gambar (snapshot)'],
            ['LOKASI', 'Lokasi fisik CCTV dipasang'],
            ['', ''],
            ['C. URL SNAPSHOT BERDASARKAN BRAND', ''],
            ['BRAND', 'FORMAT SNAPSHOT URL'],
            ['Hikvision', 'http://IP/ISAPI/Streaming/channels/1/picture'],
            ['Dahua', 'http://IP/cgi-bin/snapshot.cgi?channel=1'],
            ['CP Plus', 'http://IP/webcapture.jpg?command=snap'],
            ['Generic', 'http://IP/snapshot.jpg'],
            ['', ''],
            ['D. URL STREAM BERDASARKAN BRAND', ''],
            ['BRAND', 'FORMAT STREAM URL'],
            ['Hikvision', 'rtsp://USERNAME:PASSWORD@IP:554/Streaming/Channels/1'],
            ['Dahua', 'rtsp://USERNAME:PASSWORD@IP:554/cam/realmonitor?channel=1&subtype=0'],
            ['', ''],
            ['E. TIPS', ''],
            ['✅', 'Hapus baris contoh (baris 2-3) sebelum import'],
            ['✅', 'Gunakan dropdown untuk kolom BRAND'],
            ['✅', 'Test URL snapshot di browser terlebih dahulu'],
            ['✅', 'Jangan mengubah nama kolom header'],
            ['⚠️', 'IP Address harus sesuai dengan jaringan'],
            ['💡', 'Lihat sheet REFERENSI untuk info tambahan'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A5F']],
        ]);
        
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'D84315']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
        ]);

        $sections = ['A6', 'A11', 'A22', 'A28', 'A36'];
        foreach ($sections as $cell) {
            $row = substr($cell, 1);
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1E3A5F']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
            ]);
        }

        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(60);
    }
}