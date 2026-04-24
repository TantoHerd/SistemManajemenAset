<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $rowCount = 0;
    protected $successCount = 0;
    protected $failures = [];

    public function model(array $row)
    {
        $this->rowCount++;

        // Check if email already exists
        if (User::where('email', $row['email'])->exists()) {
            $this->failures[] = "Baris {$this->rowCount}: Email '{$row['email']}' sudah terdaftar";
            return null;
        }

        // Check if role exists
        $role = Role::where('name', $row['role'])->first();
        if (!$role) {
            $this->failures[] = "Baris {$this->rowCount}: Role '{$row['role']}' tidak ditemukan";
            return null;
        }

        $this->successCount++;

        $user = new User([
            'name' => $row['nama_lengkap'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'phone' => $row['telepon'] ?? null,
            'address' => $row['alamat'] ?? null,
            'status' => $this->mapStatus($row['status'] ?? 'active'),
        ]);

        $user->assignRole($role->name);

        return $user;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,name',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'role.required' => 'Role harus diisi',
            'role.exists' => 'Role tidak ditemukan',
        ];
    }

    private function mapStatus($status)
    {
        $statusMap = [
            'aktif' => 'active',
            'active' => 'active',
            'nonaktif' => 'inactive',
            'inactive' => 'inactive',
        ];
        
        $status = strtolower(trim($status));
        return $statusMap[$status] ?? 'active';
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