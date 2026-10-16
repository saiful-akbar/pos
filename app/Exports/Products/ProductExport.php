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
     * Header row
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Kode Produck',
            'Nama (Deskripsi)',
            'Kode Kategori',
            'Nama Kategori',
            'Status',
            'Diskon (%)',
            'Minimum Stok',
            'Stok Tersedia',
            'Waktu Dibuat',
            'Waktu DIperbarui',
        ];
    }

    /**
     * Export from collection
     */
    #[Override]
    public function collection(): Enumerable
    {
        $columns = [
            DB::raw('products.code AS code'),
            DB::raw('products.name AS name'),
            DB::raw('categories.code AS category_code'),
            DB::raw('categories.name AS category_name'),
            DB::raw("CASE WHEN products.is_active = 1 THEN 'Aktif' ELSE 'Tidak Aktif' END AS status"),
            DB::raw('products.discount AS discount'),
            DB::raw('products.minimum_stock AS minimum_stock'),
            DB::raw('products.current_stock AS current_stock'),
            DB::raw("DATE_FORMAT(products.created_at, '%Y-%m-%d %H:%i:%s') AS created"),
            DB::raw("DATE_FORMAT(products.updated_at, '%Y-%m-%d %H:%i:%s') AS updated"),
        ];

        return Product::query()
            ->select($columns)
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->orderBy('products.code', 'asc')
            ->get();
    }
}
