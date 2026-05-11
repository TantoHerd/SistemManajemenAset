<?php

namespace App\Imports;

use App\Models\Cctv;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CctvsImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $rowCount = 0;
    protected $successCount = 0;
    protected $failures = [];

    public function model(array $row)
    {
        $this->rowCount++;

        // Mapping kolom Excel ke database
        $name = $row['nama_cctv'] ?? $row['nama cctv'] ?? $row['name'] ?? null;
        $ip = $row['ip_address'] ?? $row['ip address'] ?? $row['ip'] ?? null;

        if (empty($name) || empty($ip)) {
            $this->failures[] = "Baris {$this->rowCount}: Nama CCTV dan IP Address wajib diisi";
            return null;
        }

        $this->successCount++;

        return new Cctv([
            'name' => $name,
            'ip_address' => $ip,
            'port' => $row['port'] ?? 80,
            'username' => $row['username'] ?? null,
            'password' => $row['password'] ?? null,
            'brand' => $row['brand'] ?? null,
            'model' => $row['model'] ?? null,
            'stream_url' => $row['stream_url'] ?? $row['stream url'] ?? null,
            'snapshot_url' => $row['snapshot_url'] ?? $row['snapshot url'] ?? null,
            'location' => $row['lokasi'] ?? $row['location'] ?? null,
            'status' => 'active',
        ]);
    }

    public function rules(): array
    {
        return [];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
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