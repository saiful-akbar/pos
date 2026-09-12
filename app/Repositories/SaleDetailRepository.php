<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\CreateSaleDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\ValueObjects\CategoryProductSaleValue;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use App\Domain\Sale\ValueObjects\UnitProductSaleValue;
use App\Models\SaleDetail;
use Override;

class SaleDetailRepository extends Repository implements SaleDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleDetail());
    }

    /**
     * Menyimpan (insert) data detail penjualan.
     */
    #[Override]
    public function create(CreateSaleDetailDTO $dto): SaleDetailEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleDetailEntity(
                model: $data->load(['product.category', 'product.unit']),
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
     * Pemetaan class model ke SaleDetailEntity.
     */
    private function mapModelToSaleDetailEntity(SaleDetail $model): SaleDetailEntity
    {
        return new SaleDetailEntity(
            id: $model->id,
            saleId: $model->sale_id,
            product: new ProductSaleValue(
                id: $model->product->id,
                unit: new UnitProductSaleValue(
                    id: $model->product->unit->id,
                    code: $model->product->unit->code,
                    name: $model->product->unit->name,
                    isActive: $model->product->unit->is_active === 1
                ),
                category: new CategoryProductSaleValue(
                    id: $model->product->unit->id,
                    code: $model->product->unit->code,
                    name: $model->product->unit->name,
                    isActive: $model->product->unit->is_active === 1
                ),
                code: $model->product->code,
                barcode: $model->product->barcode,
                name: $model->product->name,
                image: $model->product->image,
                purchasePrice: $model->product->purchase_price,
                sellingPrice: $model->product->selling_price,
                discount: $model->product->discount,
                currentStock: $model->product->current_stock,
                minimumStock: $model->product->minimum_stock,
            ),
            quantity: $model->quantity,
            purchasePrice: $model->purchase_price,
            sellingPrice: $model->selling_price,
            discount: $model->discount,
            subtotal: $model->subtotal,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
