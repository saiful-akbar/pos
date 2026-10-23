<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Stock\DTOs\DailyStockDetailDTO;
use App\Base\Repository;
use App\Domain\Stock\Entities\DailyStockDetailEntity;
use App\Domain\Stock\Repositories\DailyStockDetailRepositoryInterface;
use App\Models\DailyStockDetail;
use Illuminate\Database\Eloquent\Builder;
use Override;

class DailyStockDetailRepository extends Repository implements DailyStockDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new DailyStockDetail());
    }

    /**
     * Mengambil detail stok harian beserta produk dan unit dalam format query builder.
     */
    #[Override]
    public function getQueryByDailyStockId(string $dailyStockId): Builder
    {
        try {
            $columns = [
                'daily_stock_details.id as id',
                'daily_stock_details.daily_stock_id as daily_stock_id',
                'daily_stock_details.quantity as quantity',
                'daily_stock_details.multiples_quantity as multiples_quantity',
                'products.id as product_id',
                'products.code as product_code',
                'products.name as product_name',
                'units.id as unit_id',
                'units.code as unit_code',
                'units.name as unit_name',
            ];

            return $this->model->query()
                ->select($columns)
                ->leftJoin('products', 'daily_stock_details.product_id', '=', 'products.id')
                ->leftJoin('units', 'daily_stock_details.unit_id', '=', 'units.id')
                ->where('daily_stock_details.daily_stock_id', $dailyStockId)
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
     * Membuat (insert) data detail stok harian baru.
     */
    #[Override]
    public function create(DailyStockDetailDTO $value): DailyStockDetailEntity
    {
        try {
            // Simpan detail lalu muat relasi produk (+kategori) dan unit untuk entity.
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['product.category', 'unit']);

            return DailyStockDetailEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
