<?php

namespace App\Exports;

use App\Models\Maintenance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaintenancesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $assetId;

    public function __construct($assetId = null)
    {
        $this->assetId = $assetId;
    }

    public function collection()
    {
        $query = Maintenance::with('asset');

        if ($this->assetId) {
            $query->where('asset_id', $this->assetId);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE ASET',
            'NAMA ASET',
            'TIPE',
            'TANGGAL JADWAL',
            'TANGGAL SELESAI',
            'TEKNISI',
            'TINDAKAN',
            'BIAYA (Rp)',
            'STATUS',
            'CATATAN',
            'DIBUAT'
        ];
    }

    public function map($maintenance): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $typeLabels = [
            'scheduled' => 'Terjadwal',
            'unscheduled' => 'Tidak Terjadwal',
            'preventive' => 'Preventif'
        ];

        return [
            $rowNumber,
            $maintenance->asset->asset_code ?? '-',
            $maintenance->asset->name ?? '-',
            $typeLabels[$maintenance->type] ?? $maintenance->type,
            $maintenance->scheduled_date->format('d/m/Y'),
            $maintenance->completed_date ? $maintenance->completed_date->format('d/m/Y') : '-',
            $maintenance->technician ?? '-',
            $maintenance->actions_performed,
            $maintenance->cost,
            $maintenance->status_label,
            $maintenance->notes ?? '-',
            $maintenance->created_at->format('d/m/Y H:i'),
        ];
    }
}