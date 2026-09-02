<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\StockOpname\DTOs\CreateStockMovementDTO;
use App\Base\Repository;
use App\Domain\StockOpname\Entities\StockMovementEntity;
use App\Domain\StockOpname\Enums\TypeStockMovementEnum;
use App\Domain\StockOpname\Repositories\StockMovementRepositoryInterface;
use App\Domain\StockOpname\ValueObjects\ProductStockValue;
use App\Domain\StockOpname\ValueObjects\UserStockValue;
use App\Models\StockMovement;
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
     * Pemetaan model ke StockMovementEntity.
     */
    private function mapModelToStockMovementEntity(StockMovement $model): StockMovementEntity
    {
        return new StockMovementEntity(
            id: $model->id,
            user: new UserStockValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
                isActive: $model->user->is_active === 1,
            ),
            product: new ProductStockValue(
                id: $model->product->id,
                code: $model->product->code,
                barcode: $model->product->barcode,
                name: $model->product->name,
                image: $model->product->image
            ),
            type: TypeStockMovementEnum::from($model->type),
            referenceId: $model->references_id,
            quantity: $model->quantity,
            stockBefore: $model->stock_before,
            stockAfter: $model->stock_after,
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }

    /**
     * Membuat (insert) data pergerak stock.
     *
     * @param CreateStockMovementDTO $dto
     * @return StockMovementEntity
     */
    #[Override]
    public function create(CreateStockMovementDTO $dto): StockMovementEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToStockMovementEntity(
                model: $data->load('user', 'product'),
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
