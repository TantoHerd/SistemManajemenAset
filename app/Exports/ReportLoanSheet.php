<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportLoanSheet implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function title(): string { return 'Peminjaman'; }

    public function headings(): array
    {
        return ['Kode', 'Aset', 'Peminjam', 'Tgl Pinjam', 'Estimasi Kembali', 'Status', 'Denda'];
    }

    public function collection()
    {
        $dateFrom = $this->filters['date_from'] ?? now()->subYear()->format('Y-m-d');
        $dateTo = $this->filters['date_to'] ?? now()->format('Y-m-d');

        return Loan::with(['asset', 'user'])
            ->whereBetween('loan_date', [$dateFrom, $dateTo])
            ->when($this->filters['category_id'] ?? null, fn($q) => $q->whereHas('asset', fn($q) => $q->where('category_id', $this->filters['category_id'])))
            ->latest()->get()
            ->map(fn($l) => [
                $l->loan_code, $l->asset->name ?? '-', $l->user->name ?? '-',
                $l->loan_date?->format('d/m/Y'), $l->expected_return_date?->format('d/m/Y'),
                $l->status_label, $l->fine_amount
            ]);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E65100']],
        ]);
    }
}