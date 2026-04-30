<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template Import' => new UserTemplateSheet(),
            'Petunjuk' => new UserInstructionSheet(),
            'Data Referensi' => new UserReferenceSheet(),
        ];
    }
}