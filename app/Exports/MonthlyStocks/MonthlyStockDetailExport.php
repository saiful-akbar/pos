<?php

declare(strict_types=1);

namespace App\Exports\MonthlyStocks;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Override;

class MonthlyStockDetailExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Builder $query) {}

    /**
     * Baris header.
     */
    #[Override]
    public function headings(): array
    {
        return [
            'Kode Produk',
            'Nama Produk',
            'Kode Unit',
            'Nama Unit',
            'Qty',
            'Kelipatan Qty',
            'Total Qty (Satuan Dasar)',
        ];
    }

    /**
     * Query detail stok bulanan beserta produk dan unit.
     */
    #[Override]
    public function query(): Builder
    {
        return $this->query;
    }

    /**
     * Memetakan satu baris detail ke baris excel.
     */
    #[Override]
    public function map($row): array
    {
        return [
            $row->product_code,
            $row->product_name,
            $row->unit_code,
            $row->unit_name,
            (float) $row->quantity,
            (float) $row->multiples_quantity,
            (float) $row->quantity * (float) $row->multiples_quantity,
        ];
    }
}
