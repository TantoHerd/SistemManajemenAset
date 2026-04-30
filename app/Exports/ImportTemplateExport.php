<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new TemplateDataSheet(),
            new TemplateGuideSheet(),
            new TemplateReferenceSheet(), // BARU
        ];
    }
}