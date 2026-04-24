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

class TemplateGuideSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            ['', ''],
            ['PANDUAN IMPORT DATA ASET', ''],
            ['', ''],
            ['A. KOLOM YANG HARUS DIISI (WAJIB)', ''],
            ['Kolom', 'Keterangan'],
            ['nama_aset', 'Nama lengkap aset (contoh: Dell XPS 15 Laptop)'],
            ['kode_kategori', 'Kode kategori yang sudah tersedia di sistem'],
            ['kode_lokasi', 'Kode lokasi yang sudah tersedia di sistem'],
            ['tanggal_beli', 'Format: YYYY-MM-DD (contoh: 2024-01-15)'],
            ['harga_beli', 'Angka tanpa titik atau koma (contoh: 25000000)'],
            ['', ''],
            ['B. KOLOM OPSIONAL', ''],
            ['Kolom', 'Keterangan'],
            ['kode_aset', 'Kosongkan untuk generate otomatis'],
            ['serial_number', 'Nomor seri aset'],
            ['model', 'Model aset'],
            ['brand', 'Merek/brand aset'],
            ['nilai_residu', 'Kosongkan untuk 10% dari harga beli'],
            ['masa_manfaat', 'Kosongkan untuk mengikuti kategori'],
            ['garansi_berakhir', 'Format: YYYY-MM-DD'],
            ['catatan', 'Catatan tambahan'],
            ['', ''],
            ['C. STATUS YANG TERSEDIA', ''],
            ['Status', 'Keterangan'],
            ['tersedia', 'Aset tersedia untuk digunakan'],
            ['dipakai', 'Aset sedang digunakan'],
            ['maintenance', 'Aset dalam perbaikan'],
            ['rusak', 'Aset rusak'],
            ['dihapus', 'Aset dihapuskan'],
            ['', ''],
            ['D. KODE KATEGORI (WAJIB ADA DI SISTEM)', ''],
            ['Silakan cek di menu Kategori untuk melihat kode yang tersedia'],
            ['', ''],
            ['E. KODE LOKASI (WAJIB ADA DI SISTEM)', ''],
            ['Silakan cek di menu Lokasi untuk melihat kode yang tersedia'],
            ['', ''],
            ['F. CONTOH PENGISIAN', ''],
            ['', ''],
            ['kode_aset', 'AST-001'],
            ['nama_aset', 'Dell XPS 15 Laptop'],
            ['serial_number', 'SN123456789'],
            ['model', 'XPS 15 9520'],
            ['brand', 'Dell'],
            ['kode_kategori', 'LAP'],
            ['kode_lokasi', 'GED-A-LT1'],
            ['status', 'tersedia'],
            ['tanggal_beli', '2024-01-15'],
            ['harga_beli', '25000000'],
            ['nilai_residu', '2500000'],
            ['masa_manfaat', '48'],
            ['garansi_berakhir', '2026-01-15'],
            ['catatan', 'Laptop untuk tim IT'],
            ['', ''],
            ['G. NOTES', ''],
            ['1. Pastikan kode kategori dan kode lokasi sudah tersedia di sistem'],
            ['2. File maksimal 5MB dengan format .xlsx, .xls, atau .csv'],
            ['3. Data akan divalidasi sebelum disimpan'],
            ['4. Jika ada error, sistem akan menampilkan baris yang bermasalah'],
            ['', ''],
            ['Dibuat oleh: Sistem Manajemen Aset IT'],
            ['Tanggal: ' . date('d/m/Y H:i:s')],
        ];
    }

    public function title(): string
    {
        return 'Panduan';
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for main title
        $sheet->mergeCells('B1:C1');
        $sheet->setCellValue('B1', '');
        
        $sheet->mergeCells('B2:C2');
        $sheet->getStyle('B2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Section headers style
        $sections = ['A4', 'A11', 'A23', 'A27', 'A30', 'A33', 'A48'];
        foreach ($sections as $section) {
            $sheet->getStyle($section)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1565C0']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD']
                ]
            ]);
        }

        // Sub headers style (Kolom | Keterangan)
        $sheet->getStyle('A5:B5')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF3E0']
            ]
        ]);

        $sheet->getStyle('A12:B12')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF3E0']
            ]
        ]);

        $sheet->getStyle('A24:B24')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFF3E0']
            ]
        ]);

        // Status badge style
        $statusRows = [25, 26, 27, 28, 29];
        foreach ($statusRows as $row) {
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true],
            ]);
        }

        // Example data style
        $sheet->getStyle('A34:B47')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ]
        ]);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(50);
    }
}