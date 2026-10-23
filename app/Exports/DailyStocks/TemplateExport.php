<?php

declare(strict_types=1);

namespace App\Exports\DailyStocks;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class TemplateExport implements Export, ShouldAutoSize, WithHeadings
{
    /**
     * Baris header template import stok harian.
     */
    #[Override]
    public function headings(): array
    {
        return [
            [
                'Kode Produk',
                'Kode Unit (Satuan)',
                'Qty',
            ],
        ];
    }
}
