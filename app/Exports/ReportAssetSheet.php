<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportAssetSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Data Aset';
    }

    public function headings(): array
    {
        return ['Kode', 'Nama', 'Kategori', 'Lokasi', 'Status', 'Harga Beli', 'Nilai Saat Ini', 'Tanggal Beli'];
    }

    public function collection()
    {
        return Asset::with(['category', 'location'])
            ->when($this->filters['category_id'] ?? null, fn($q) => $q->where('category_id', $this->filters['category_id']))
            ->when($this->filters['location_id'] ?? null, fn($q) => $q->where('location_id', $this->filters['location_id']))
            ->latest()->get()
            ->map(fn($a) => [
                $a->asset_code, $a->name, $a->category->name ?? '-', $a->location->name ?? '-',
                $a->status_label, $a->purchase_price, $a->current_value,
                $a->purchase_date?->format('d/m/Y')
            ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2E7D32']],
        ]);
    }
}