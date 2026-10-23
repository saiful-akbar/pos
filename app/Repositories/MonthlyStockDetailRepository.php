<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Stock\DTOs\MonthlyStockDetailDTO;
use App\Base\Repository;
use App\Domain\Stock\Repositories\MonthlyStockDetailRepositoryInterface;
use App\Models\MonthlyStockDetail;
use Illuminate\Database\Eloquent\Builder;
use Override;

class MonthlyStockDetailRepository extends Repository implements MonthlyStockDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new MonthlyStockDetail());
    }

    /**
     * Membuat (insert) banyak data detail stok bulanan sekaligus.
     *
     * @param array<MonthlyStockDetailDTO> $values
     */
    #[Override]
    public function createMany(array $values): int
    {
        try {
            $created = 0;

            // Gunakan create() agar ULID dan timestamp terisi otomatis oleh model.
            foreach ($values as $value) {
                $this->model
                    ->newQuery()
                    ->create($value->toArray());

                $created++;
            }

            return $created;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil detail stok bulanan beserta produk dan unit dalam format query builder.
     */
    #[Override]
    public function getQueryByMonthlyStockId(string $monthlyStockId): Builder
    {
        try {
            $columns = [
                'monthly_stock_details.id as id',
                'monthly_stock_details.monthly_stock_id as monthly_stock_id',
                'monthly_stock_details.quantity as quantity',
                'monthly_stock_details.multiples_quantity as multiples_quantity',
                'products.id as product_id',
                'products.code as product_code',
                'products.name as product_name',
                'units.id as unit_id',
                'units.code as unit_code',
                'units.name as unit_name',
            ];

            return $this->model
                ->newQuery()
                ->select($columns)
                ->leftJoin('products', 'monthly_stock_details.product_id', '=', 'products.id')
                ->leftJoin('units', 'monthly_stock_details.unit_id', '=', 'units.id')
                ->where('monthly_stock_details.monthly_stock_id', $monthlyStockId)
                ->orderBy('products.code', 'asc');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil ringkasan detail stok bulanan (id produk, quantity, kelipatan) berdasarkan id stok bulanan.
     *
     * @return array<int, array{product_id: string, quantity: float, multiples_quantity: float}>
     */
    #[Override]
    public function getByMonthlyStockId(string $monthlyStockId): array
    {
        try {
            return $this->model
                ->newQuery()
                ->where('monthly_stock_id', $monthlyStockId)
                ->get(['product_id', 'quantity', 'multiples_quantity'])
                ->map(fn (MonthlyStockDetail $detail): array => [
                    'product_id' => $detail->product_id,
                    'quantity' => parse_float($detail->quantity),
                    'multiples_quantity' => parse_float($detail->multiples_quantity),
                ])
                ->all();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
