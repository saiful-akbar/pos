<?php

declare(strict_types=1);

namespace App\Exports\StockOpnames;

use App\Models\Product;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class TemplateInputDailyStockOpnameExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * Heading rows
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Kode Produk',
            'Nama (Deskripsi)',
            'Satuan',
            'Quantity (Stok)',
        ];
    }

    /**
     * Collections.
     */
    #[Override]
    public function collection(): Enumerable
    {
        $columns = [
            'products.code as code',
            'products.name as name',
            'units.code as unit_code',
        ];

        return Product::query()
            ->select($columns)
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->where('products.is_active', 1)
            ->orderBy('products.code', 'asc')
            ->get();
    }
}
