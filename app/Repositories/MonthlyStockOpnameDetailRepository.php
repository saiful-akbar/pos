<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\StockOpname\DTOs\MonthlyStockOpnameDetailDTO;
use App\Base\Repository;
use App\Domain\StockOpname\Entities\MonthlyStockOpnameDetailEntity;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\ValueObjects\CategoryProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\ProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\UnitProductStockOpnameValue;
use App\Models\Category;
use App\Models\MonthlyStockOpnameDetail;
use App\Models\Product;
use App\Models\Unit;
use Override;

class MonthlyStockOpnameDetailRepository extends Repository implements MonthlyStockOpnameDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new MonthlyStockOpnameDetail());
    }

    /**
     * Menyimpan (insert) data.
     */
    #[Override]
    public function create(MonthlyStockOpnameDetailDTO $value): MonthlyStockOpnameDetailEntity
    {
        try {
            $result = $this->model->query()->create($value->toArray());

            return $this->mapModelToMonthlyStockOpnameDetailEntity(
                model: $result->load(['product.category', 'product.unit']),
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan class Model ke class CategoryProductStockOpnameValue
     */
    private function mapModelToCategoryProductStockOpnameValue(Category $model): CategoryProductStockOpnameValue
    {
        return new CategoryProductStockOpnameValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class model ke class UnitProductStockOpnameValue.
     */
    private function mapModelToUnitProductStockOpnameValue(Unit $model): UnitProductStockOpnameValue
    {
        return new UnitProductStockOpnameValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class ProductStockOpnameValue
     */
    private function mapModelToProductStockOpnameValue(Product $model): ProductStockOpnameValue
    {
        return new ProductStockOpnameValue(
            id: $model->id,
            category: $this->mapModelToCategoryProductStockOpnameValue($model->category),
            unit: $this->mapModelToUnitProductStockOpnameValue($model->unit),
            code: $model->code,
            barcode: $model->barcode,
            name: $model->name,
            image: $model->image,
            purchasePrice: parse_float($model->purchase_price),
            sellingPrice: parse_float($model->selling_price),
            discount: parse_float($model->discount),
            minimumStock: parse_float($model->minimum_stock),
            currentStock: parse_float($model->current_stock),
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class MonthlyStockOpnameDetailEntity.
     */
    private function mapModelToMonthlyStockOpnameDetailEntity(MonthlyStockOpnameDetail $model): MonthlyStockOpnameDetailEntity
    {
        return new MonthlyStockOpnameDetailEntity(
            id: $model->id,
            monthlyStockOpnameId: $model->monthly_stock_opname_id,
            product: $this->mapModelToProductStockOpnameValue($model->product),
            systemStock: parse_float($model->system_stock),
            physicalStock: parse_float($model->physical_stock),
            difference: parse_float($model->difference),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
