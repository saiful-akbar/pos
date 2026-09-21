<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\StockOpname\DTOs\CreateStockMovementDTO;
use App\Base\Repository;
use App\Domain\StockOpname\Entities\StockMovementEntity;
use App\Domain\StockOpname\Enums\TypeStockMovementEnum;
use App\Domain\StockOpname\Repositories\StockMovementRepositoryInterface;
use App\Domain\StockOpname\ValueObjects\CategoryProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\ProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\UnitProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\UserStockOpnameValue;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Override;

class StockMovementRepository extends Repository implements StockMovementRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new StockMovement());
    }

    /**
     * Membuat (insert) data pergerak stock.
     */
    #[Override]
    public function create(CreateStockMovementDTO $dto): StockMovementEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToStockMovementEntity(
                model: $data->load(['user', 'product.category', 'product.unit']),
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
     * Pemetaan model ke StockMovementEntity.
     */
    private function mapModelToStockMovementEntity(StockMovement $model): StockMovementEntity
    {
        return new StockMovementEntity(
            id: $model->id,
            user: $this->mapModelToUserStockOpnameValue($model->user),
            product: $this->mapModelToProductStockOpnameValue($model->product),
            type: TypeStockMovementEnum::from($model->type),
            referenceId: $model->references_id,
            quantity: parse_float($model->quantity),
            stockBefore: parse_float($model->stock_before),
            stockAfter: parse_float($model->stock_after),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }

    /**
     * Pemetaan class Model ke class CategoryProductStockOpnameValue
     */
    private function mapModelToCategoryProductStockOpnameValue(Model $model): CategoryProductStockOpnameValue
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
    private function mapModelToUnitProductStockOpnameValue(Model $model): UnitProductStockOpnameValue
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
    private function mapModelToProductStockOpnameValue(Model $model): ProductStockOpnameValue
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
     * Pemetaan class Model ke class UserStockOpnameValue
     */
    private function mapModelToUserStockOpnameValue(Model $model): UserStockOpnameValue
    {
        return new UserStockOpnameValue(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
            photo: $model->photo,
            isActive: $model->is_active === 1,
        );
    }
}
