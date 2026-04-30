<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\Category;
use App\Models\Location;
use App\Models\CategorySpecification;

class TemplateGuideSheet implements FromArray, WithTitle, WithStyles, ShouldAutoSize
{
    protected $categories;
    protected $locations;
    protected $specKeys;

    public function __construct()
    {
        $this->categories = Category::orderBy('name')->get();
        $this->locations = Location::orderBy('name')->get();
        $this->specKeys = CategorySpecification::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['key', 'label']);
    }

    public function title(): string
    {
        return 'Petunjuk';
    }

    public function array(): array
    {
        $data = [
            ['', ''],
            ['📋 PANDUAN IMPORT DATA ASET', ''],
            ['', ''],
            ['⚠️ SEBELUM MENGISI, BACA PETUNJUK INI!', ''],
            ['', ''],
            
            // A. Kolom Wajib
            ['A. KOLOM YANG HARUS DIISI (WAJIB)', ''],
            ['No', 'Kolom | Keterangan'],
            ['1', 'nama_aset - Nama lengkap aset (contoh: Dell XPS 15 Laptop)'],
            ['2', 'kode_kategori - Gunakan dropdown atau lihat sheet REFERENSI'],
            ['3', 'kode_lokasi - Gunakan dropdown atau lihat sheet REFERENSI'],
            ['4', 'tanggal_beli - Format: YYYY-MM-DD (contoh: 2024-01-15)'],
            ['5', 'harga_beli - Angka tanpa titik/koma (contoh: 25000000)'],
            ['', ''],
            
            // B. Kolom Opsional
            ['B. KOLOM OPSIONAL (BOLEH KOSONG)', ''],
            ['No', 'Kolom | Keterangan'],
            ['1', 'kode_aset - Kosongkan untuk generate otomatis (format: AST-YYYYMMDD-XXXXX)'],
            ['2', 'serial_number - Nomor seri fisik aset'],
            ['3', 'model - Model/nama produk'],
            ['4', 'brand - Merek pabrikan (Dell, Apple, HP, dll)'],
            ['5', 'nilai_residu - Kosongkan = 10% dari harga beli'],
            ['6', 'masa_manfaat - Kosongkan = ikut default kategori (dalam BULAN)'],
            ['7', 'garansi_berakhir - Format: YYYY-MM-DD'],
            ['8', 'catatan - Teks bebas'],
            ['', ''],
            
            // C. Kode Kategori
            ['C. DAFTAR KODE KATEGORI (Gunakan kode ini di kolom F)', ''],
            ['KODE', 'NAMA KATEGORI | MASA MANFAAT'],
        ];
        
        foreach ($this->categories as $cat) {
            $data[] = [$cat->code, $cat->name . ' | ' . $cat->useful_life_months . ' bulan'];
        }
        
        $data[] = ['', ''];
        
        // D. Kode Lokasi
        $data[] = ['D. DAFTAR KODE LOKASI (Gunakan kode ini di kolom G)', ''];
        $data[] = ['KODE', 'NAMA LOKASI | FULL PATH'];
        
        foreach ($this->locations as $loc) {
            $data[] = [$loc->code, $loc->name . ' | ' . ($loc->full_path ?? $loc->name)];
        }
        
        $data[] = ['', ''];
        
        // E. Status
        $data[] = ['E. STATUS YANG TERSEDIA (Gunakan dropdown di kolom H)', ''];
        $data[] = ['STATUS', 'KETERANGAN'];
        $data[] = ['tersedia', '✅ Aset siap digunakan (default)'];
        $data[] = ['dipakai', '🔵 Aset sedang digunakan/dipinjam'];
        $data[] = ['maintenance', '🟡 Aset dalam perbaikan/servis'];
        $data[] = ['rusak', '🔴 Aset rusak/tidak bisa dipakai'];
        $data[] = ['dihapus', '⚫ Aset sudah dihapuskan/dibuang'];
        
        $data[] = ['', ''];
        
        // F. Spesifikasi
        if ($this->specKeys->count() > 0) {
            $data[] = ['F. KOLOM SPESIFIKASI (Isi sesuai jenis aset)', ''];
            $data[] = ['LABEL KOLOM', 'CONTOH PENGISIAN'];
            
            foreach ($this->specKeys as $spec) {
                $data[] = [$spec->label, $this->getSpecExample($spec->key)];
            }
            
            $data[] = ['', '⚠️ Tidak semua aset punya spesifikasi yang sama. Isi yang relevan saja.'];
        }
        
        $data[] = ['', ''];
        
        // G. Tips
        $data[] = ['G. TIPS & CATATAN PENTING', ''];
        $data[] = ['✅', 'Hapus baris contoh (baris 2) sebelum import'];
        $data[] = ['✅', 'Gunakan dropdown untuk status, kategori, dan lokasi'];
        $data[] = ['✅', 'File maksimal 10MB: .xlsx, .xls, atau .csv'];
        $data[] = ['✅', 'Jangan mengubah/menghapus kolom header (baris 1)'];
        $data[] = ['✅', 'Pastikan tidak ada baris kosong di tengah data'];
        $data[] = ['⚠️', 'Kode kategori & lokasi HARUS sesuai dengan yang terdaftar'];
        $data[] = ['⚠️', 'Email/notifikasi akan muncul jika ada error'];
        $data[] = ['💡', 'Lihat sheet REFERENSI untuk daftar lengkap'];
        
        $data[] = ['', ''];
        $data[] = ['Dibuat: ' . date('d/m/Y H:i'), 'Sistem Manajemen Aset IT'];
        
        return $data;
    }

    private function getSpecExample($key)
    {
        $examples = [
            'processor' => 'Intel Core i7-1260P',
            'ram' => '16 GB',
            'storage' => '512 GB SSD NVMe',
            'ukuran_layar' => '15.6 inch',
            'os' => 'Windows 11 Pro',
            'tipe_printer' => 'Laser',
            'kecepatan_cetak' => '30 ppm',
            'konektivitas' => 'WiFi, USB, Ethernet',
        ];
        return $examples[$key] ?? '(isi sesuai ' . $key . ')';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Title
        $sheet->mergeCells('B2:C2');
        $sheet->getStyle('B2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Warning
        $sheet->mergeCells('B4:C4');
        $sheet->getStyle('B4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'D84315']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3E0']],
        ]);

        // Section headers
        $sections = ['A7', 'A17', 'A28', 'A38', 'A48'];
        foreach ($sections as $cell) {
            $row = substr($cell, 1);
            $sheet->mergeCells('B' . $row . ':C' . $row);
            $sheet->getStyle('B' . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1565C0']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
            ]);
        }

        // Table headers
        $tableHeaders = [8, 18, 29, 39, 49, 56];
        foreach ($tableHeaders as $row) {
            if ($row <= $lastRow) {
                $sheet->getStyle('B' . $row . ':C' . $row)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                    'borders' => ['bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
            }
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(60);
        
        // Row height
        $sheet->getRowDimension(2)->setRowHeight(25);
    }
}