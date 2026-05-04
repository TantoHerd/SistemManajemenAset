<?php

namespace App\Exports;

use App\Models\Maintenance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportMaintenanceSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function title(): string { return 'Maintenance'; }

    public function headings(): array
    {
        return ['Tanggal', 'Aset', 'Judul', 'Teknisi', 'Biaya', 'Status'];
    }

    public function collection()
    {
        $dateFrom = $this->filters['date_from'] ?? now()->subYear()->format('Y-m-d');
        $dateTo = $this->filters['date_to'] ?? now()->format('Y-m-d');

        return Maintenance::with('asset')
            ->whereBetween('maintenance_date', [$dateFrom, $dateTo])
            ->when($this->filters['category_id'] ?? null, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $this->filters['category_id'])))
            ->latest()->get()
            ->map(fn($m) => [
                $m->maintenance_date?->format('d/m/Y'), $m->asset->name ?? '-',
                $m->title, $m->technician, $m->cost, $m->status_label
            ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1565C0']],
        ]);
    }
}