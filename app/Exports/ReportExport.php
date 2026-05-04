<?php

namespace App\Exports;

use App\Models\Asset;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            'Data Aset' => new ReportAssetSheet($this->filters),
            'Maintenance' => new ReportMaintenanceSheet($this->filters),
            'Peminjaman' => new ReportLoanSheet($this->filters),
            'Ringkasan' => new ReportSummarySheet($this->filters),
        ];
    }
}