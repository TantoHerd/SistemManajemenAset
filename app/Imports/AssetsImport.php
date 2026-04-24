<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AssetsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithProgressBar
{
    use Importable;
    
    protected $rowCount = 0;
    protected $successCount = 0;
    protected $failures = [];
    protected $columnMap = [];

    public function __construct()
    {
        // Mapping kolom yang mungkin berbeda
        $this->columnMap = [
            'kode_aset' => ['kode_aset', 'kode aset', 'asset_code', 'asset code', 'kode'],
            'nama_aset' => ['nama_aset', 'nama aset', 'asset_name', 'asset name', 'name', 'nama', 'aset'],
            'serial_number' => ['serial_number', 'serial number', 'sn', 'no_seri', 'serial'],
            'model' => ['model'],
            'brand' => ['brand', 'merk'],
            'kode_kategori' => ['kode_kategori', 'kode kategori', 'category_code', 'kategori'],
            'kode_lokasi' => ['kode_lokasi', 'kode lokasi', 'location_code', 'lokasi'],
            'status' => ['status'],
            'tanggal_beli' => ['tanggal_beli', 'tanggal beli', 'purchase_date', 'tgl_beli'],
            'harga_beli' => ['harga_beli', 'harga beli', 'purchase_price', 'price', 'harga'],
            'nilai_residu' => ['nilai_residu', 'nilai residu', 'residual_value', 'residu'],
            'masa_manfaat' => ['masa_manfaat', 'masa manfaat', 'useful_life', 'umur_ekonomis'],
            'garansi_berakhir' => ['garansi_berakhir', 'garansi berakhir', 'warranty_expiry', 'garansi'],
            'catatan' => ['catatan', 'notes', 'keterangan'],
        ];
    }

    public function model(array $row)
    {
        $this->rowCount++;

        Log::info('Row ' . $this->rowCount . ': ' . json_encode($row));

        // Map kolom dengan fleksibel
        $mappedRow = $this->mapColumns($row);

        // Validasi required fields
        if (empty($mappedRow['nama_aset'])) {
            $this->failures[] = "Baris {$this->rowCount}: Nama aset tidak boleh kosong";
            return null;
        }

        if (empty($mappedRow['kode_kategori'])) {
            $this->failures[] = "Baris {$this->rowCount}: Kode kategori tidak boleh kosong";
            return null;
        }

        if (empty($mappedRow['kode_lokasi'])) {
            $this->failures[] = "Baris {$this->rowCount}: Kode lokasi tidak boleh kosong";
            return null;
        }

        if (empty($mappedRow['harga_beli']) || $mappedRow['harga_beli'] <= 0) {
            $this->failures[] = "Baris {$this->rowCount}: Harga beli harus diisi dan lebih dari 0";
            return null;
        }

        if (empty($mappedRow['tanggal_beli'])) {
            $this->failures[] = "Baris {$this->rowCount}: Tanggal beli tidak boleh kosong";
            return null;
        }

        // Cari category
        $category = Category::where('code', $mappedRow['kode_kategori'])->first();
        if (!$category) {
            $this->failures[] = "Baris {$this->rowCount}: Kode kategori '{$mappedRow['kode_kategori']}' tidak ditemukan";
            return null;
        }

        // Cari location
        $location = Location::where('code', $mappedRow['kode_lokasi'])->first();
        if (!$location) {
            $this->failures[] = "Baris {$this->rowCount}: Kode lokasi '{$mappedRow['kode_lokasi']}' tidak ditemukan";
            return null;
        }

        // Format tanggal
        $purchaseDate = $this->parseDate($mappedRow['tanggal_beli']);
        $warrantyExpiry = !empty($mappedRow['garansi_berakhir']) ? $this->parseDate($mappedRow['garansi_berakhir']) : null;

        // Generate asset code if empty
        $assetCode = !empty($mappedRow['kode_aset']) ? $mappedRow['kode_aset'] : $this->generateAssetCode();
        
        // Check duplicate asset code
        if (Asset::where('asset_code', $assetCode)->exists()) {
            $this->failures[] = "Baris {$this->rowCount}: Kode aset '{$assetCode}' sudah ada";
            return null;
        }

        $this->successCount++;

        return new Asset([
            'asset_code' => $assetCode,
            'name' => $mappedRow['nama_aset'],
            'serial_number' => $mappedRow['serial_number'] ?? null,
            'model' => $mappedRow['model'] ?? null,
            'brand' => $mappedRow['brand'] ?? null,
            'category_id' => $category->id,
            'location_id' => $location->id,
            'assigned_to' => null,
            'status' => $this->mapStatus($mappedRow['status'] ?? 'available'),
            'purchase_date' => $purchaseDate,
            'purchase_price' => $mappedRow['harga_beli'],
            'residual_value' => $mappedRow['nilai_residu'] ?? ($mappedRow['harga_beli'] * 0.1),
            'useful_life_months' => $mappedRow['masa_manfaat'] ?? $category->useful_life_months,
            'current_value' => $mappedRow['harga_beli'],
            'notes' => $mappedRow['catatan'] ?? null,
            'warranty_expiry' => $warrantyExpiry,
        ]);
    }

    /**
     * Map columns with flexible naming
     */
    private function mapColumns(array $row)
    {
        $mapped = [];
        
        // Debug: Log semua key yang ada
        Log::info('Available keys: ' . json_encode(array_keys($row)));
        
        foreach ($this->columnMap as $target => $possibleKeys) {
            foreach ($possibleKeys as $key) {
                // Cek case insensitive
                $foundKey = $this->findKeyCaseInsensitive($row, $key);
                if ($foundKey) {
                    $mapped[$target] = $row[$foundKey];
                    break;
                }
            }
        }
        
        return $mapped;
    }

    private function findKeyCaseInsensitive($array, $search)
    {
        foreach (array_keys($array) as $key) {
            if (strtolower($key) === strtolower($search)) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }
        
        // Jika sudah dalam format Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }
        
        // Jika dalam format d/m/Y
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            $parts = explode('/', $date);
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        
        // Jika dalam format timestamp Excel
        if (is_numeric($date)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
        }
        
        // Coba parse dengan strtotime
        $timestamp = strtotime($date);
        if ($timestamp) {
            return date('Y-m-d', $timestamp);
        }
        
        return null;
    }

    public function rules(): array
    {
        return [
            // Rules akan divalidasi manual di atas
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    private function mapStatus($status)
    {
        $statusMap = [
            'tersedia' => 'available',
            'available' => 'available',
            'dipakai' => 'in_use',
            'in_use' => 'in_use',
            'digunakan' => 'in_use',
            'maintenance' => 'maintenance',
            'perbaikan' => 'maintenance',
            'rusak' => 'damaged',
            'damaged' => 'damaged',
            'dihapus' => 'disposed',
            'disposed' => 'disposed',
        ];
        
        $status = strtolower(trim($status));
        return $statusMap[$status] ?? 'available';
    }

    private function generateAssetCode(): string
    {
        $prefix = 'AST';
        $code = $prefix . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        
        while (Asset::where('asset_code', $code)->exists()) {
            $code = $prefix . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        }
        
        return $code;
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailures()
    {
        return $this->failures;
    }
}