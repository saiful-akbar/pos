<?php

declare(strict_types=1);

namespace App\Exports\Products;

use App\Models\Product;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class ProductExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * Export from collection.
     */
    #[Override]
    public function collection(): Enumerable
    {
        $columns = [
            'products.code as code',
            'products.barcode as barcode',
            'products.name as name',
            'categories.code as category_code',
            'categories.name as category_name',
            'units.code as unit_code',
            'units.name as unit_name',
            'products.selling_price as selling_price',
            'products.discount as discount',
            'products.current_stock as current_stock',
            'products.minimum_stock as minimum_stock',
            DB::raw("if(products.is_active, 'Aktif', 'Tidak Aktif') as status"),
        ];

        $products = Product::query()
            ->select($columns)
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('units', 'products.unit_id', '=', 'units.id')
            ->get();

        return $products;
    }

    /**
     * Heading rows.
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Kode Produk',
            'Barcode',
            'Nama (Deskripsi)',
            'Kode Kategori',
            'Nama Kategori',
            'Kode Unit',
            'Nama Unit',
            'Harga Jual',
            'Diskon',
            'Stok Tersedia',
            'Stok Minimum',
            'Status',
        ];
    }
}
