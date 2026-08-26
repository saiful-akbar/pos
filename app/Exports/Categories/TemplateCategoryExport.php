<?php

declare(strict_types=1);

namespace App\Exports\Categories;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class TemplateCategoryExport implements Export, FromArray, ShouldAutoSize, WithHeadings
{
    #[Override]
    public function array(): array
    {
        return [
            [
                'ABC',
                'Alfabet',
                '1 = Aktif, 0 = Tidak aktif',
            ],
        ];
    }

    #[Override]
    public function headings(): array
    {
        return [
            'Kode',
            'Nama',
            'Aktif Status',
        ];
    }
}
