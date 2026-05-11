<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CctvTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template Import' => new CctvTemplateSheet(),
            'Petunjuk' => new CctvInstructionSheet(),
            'Referensi' => new CctvReferenceSheet(),
        ];
    }
}