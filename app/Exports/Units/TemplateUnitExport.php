<?php

declare(strict_types=1);

namespace App\Exports\Units;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class TemplateUnitExport implements Export, ShouldAutoSize, WithHeadings
{
    #[Override]
    public function headings(): array
    {
        return [
            [
                'Kolom yang ditandai * wajib diisi',
            ],
            [
                'Kode *',
                'Nama',
                'Status (1 = aktif, 0 = tidak aktif)',
            ],
        ];
    }
}
