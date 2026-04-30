<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UserInstructionSheet implements FromArray, WithStyles, WithTitle, WithColumnWidths
{
    public function title(): string
    {
        return 'Petunjuk';
    }

    public function columnWidths(): array
    {
        return ['A' => 80, 'B' => 30];
    }

    public function array(): array
    {
        return [
            ['IMPORT USER - PETUNJUK PENGGUNAAN', ''],
            ['', ''],
            ['LANGKAH-LANGKAH IMPORT:', ''],
            ['1. Buka sheet "Template Import" dan isi data user sesuai kolom yang tersedia', ''],
            ['2. Baris 2-3 adalah CONTOH, silakan dihapus atau ditimpa dengan data baru', ''],
            ['3. Setiap baris mewakili SATU user baru', ''],
            ['4. Simpan file, lalu upload melalui menu User > Import', ''],
            ['', ''],
            ['KETERANGAN KOLOM:', ''],
            ['', ''],
            ['A. NAMA LENGKAP (Wajib)', '✅ REQUIRED'],
            ['   - Isi dengan nama lengkap user', ''],
            ['   - Contoh: Budi Santoso, Siti Rahayu', ''],
            ['   - Maksimal 255 karakter', ''],
            ['', ''],
            ['B. EMAIL (Wajib, Unik)', '✅ REQUIRED'],
            ['   - Isi dengan alamat email yang valid', ''],
            ['   - Contoh: budi@perusahaan.com', ''],
            ['   - Email harus UNIK (tidak boleh duplikat)', ''],
            ['   - Format: nama@domain.com', ''],
            ['', ''],
            ['C. PASSWORD (Opsional)', '⚪ OPSIONAL'],
            ['   - Isi password untuk user baru', ''],
            ['   - Minimal 8 karakter', ''],
            ['   - Jika dikosongkan, password default: password123', ''],
            ['   - User akan diminta ganti password saat login pertama', ''],
            ['', ''],
            ['D. TELEPON (Opsional)', '⚪ OPSIONAL'],
            ['   - Isi nomor telepon user', ''],
            ['   - Contoh: 08123456789', ''],
            ['', ''],
            ['E. ALAMAT (Opsional)', '⚪ OPSIONAL'],
            ['   - Isi alamat lengkap user', ''],
            ['   - Contoh: Jl. Sudirman No. 1, Jakarta', ''],
            ['', ''],
            ['F. ROLE (Wajib)', '✅ REQUIRED'],
            ['   - Pilih salah satu role yang tersedia:', ''],
            ['   • super_admin - Akses penuh ke semua fitur', ''],
            ['   • admin - Kelola aset, maintenance, user', ''],
            ['   • technician - Maintenance & view aset', ''],
            ['   • user - View aset & peminjaman', ''],
            ['   • viewer - Hanya view (read-only)', ''],
            ['   - Gunakan dropdown yang tersedia di sel', ''],
            ['', ''],
            ['G. STATUS (Opsional, Default: Aktif)', '⚪ OPSIONAL'],
            ['   - Pilih: Aktif atau Nonaktif', ''],
            ['   - Jika dikosongkan, default: Aktif', ''],
            ['   - Gunakan dropdown yang tersedia di sel', ''],
            ['', ''],
            ['CATATAN PENTING:', ''],
            ['• Jangan mengubah nama kolom (baris 1)', ''],
            ['• Jangan menambah atau menghapus kolom', ''],
            ['• Hapus baris contoh (baris 2-3) sebelum import', ''],
            ['• Pastikan tidak ada baris kosong di tengah data', ''],
            ['• Format file: .xlsx, .xls, atau .csv', ''],
            ['• Maksimal ukuran file: 10 MB', ''],
            ['• Jika terjadi error, periksa kembali format data', ''],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Title
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E3A5F']],
        ]);
        $sheet->mergeCells('A1:B1');

        // Section headers
        $sectionRows = [3, 8, 42];
        foreach ($sectionRows as $row) {
            $sheet->getStyle('A' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '2E7D32']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']]
            ]);
            $sheet->mergeCells('A' . $row . ':B' . $row);
        }

        // Required/Optional labels
        $sheet->getStyle('B10:B41')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Notes section
        $sheet->getStyle('A44:A51')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => 'FF6F00']]
        ]);

        // Row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        for ($i = 2; $i <= 51; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(18);
        }

        return [];
    }
}