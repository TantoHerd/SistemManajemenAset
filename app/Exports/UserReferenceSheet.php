<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UserReferenceSheet implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function title(): string
    {
        return 'Data Referensi';
    }

    public function headings(): array
    {
        return ['ROLE', 'KETERANGAN', 'PERMISSIONS'];
    }

    public function array(): array
    {
        $roles = \Spatie\Permission\Models\Role::with('permissions')->get();
        $data = [];

        foreach ($roles as $role) {
            $permissions = $role->permissions->pluck('name')->join(', ');
            $deskripsi = $this->getRoleDescription($role->name);
            $data[] = [
                $role->name,
                $deskripsi,
                $permissions ?: '-'
            ];
        }

        return $data;
    }

    private function getRoleDescription($name)
    {
        return [
            'super_admin' => 'Akses penuh ke semua fitur sistem',
            'admin' => 'Kelola aset, maintenance, user, laporan',
            'technician' => 'Maintenance & view aset',
            'user' => 'View aset & pengajuan peminjaman',
            'viewer' => 'Hanya melihat data (read-only)',
        ][$name] ?? 'Role kustom';
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Header
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Data
        $sheet->getStyle('A2:C' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Alternating colors
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':C' . $i)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FC']]
                ]);
            }
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(60);

        return [];
    }
}