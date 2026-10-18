<?php

declare(strict_types=1);

namespace App\Exports\Products;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class TemplateUpdatePricesProductExport implements Export, ShouldAutoSize, WithHeadings
{
    /**
     * Header row
     */
    #[Override]
    public function headings(): array
    {
        return [
            [
                'Kolom dengan tanda * wajib diisi',
            ],
            [
                'Kode Produk *',
                'Kode Unit (Satuan) *',
                'Kelipatan Quantity *',
                'Harga Beli *',
                'Harga Jual *',
            ],
        ];
    }
}
