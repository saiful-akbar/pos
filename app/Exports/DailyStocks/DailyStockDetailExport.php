<?php

declare(strict_types=1);

namespace App\Exports\DailyStocks;

use App\Domain\Stock\Entities\DailyStockDetailEntity;
use App\Domain\Stock\Entities\DailyStockEntity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Override;

class DailyStockDetailExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    /**
     * Create a new class instance.
     */
    public function __construct(private DailyStockEntity $dailyStock) {}

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
     * Data detail stok harian beserta produk dan unit.
     */
    #[Override]
    public function collection(): Collection
    {
        return collect($this->dailyStock->details)
            ->sortBy(fn (DailyStockDetailEntity $detail): string => $detail->product->code)
            ->map(fn (DailyStockDetailEntity $detail): array => [
                $detail->product->code,
                $detail->product->name,
                $detail->unit->code,
                $detail->unit->name,
                $detail->quantity,
                $detail->multiplesQuantity,
                $detail->quantity * $detail->multiplesQuantity,
            ])
            ->values();
    }
}
