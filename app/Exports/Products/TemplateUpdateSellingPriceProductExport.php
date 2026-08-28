<?php

declare(strict_types=1);

namespace App\Exports\Products;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class TemplateUpdateSellingPriceProductExport implements Export, ShouldAutoSize, WithHeadings
{
    /**
     * Header row
     */
    #[Override]
    public function headings(): array
    {
        return [
            ['Kolom yang ditandai * wajib diisi'],
            ['Kode Produk *', 'Harga Jual *'],
        ];
    }
}
