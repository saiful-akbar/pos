<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleReturnDetailEntity;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;
use App\Domain\Sale\ValueObjects\CategoryProductSaleValue;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use App\Domain\Sale\ValueObjects\UnitProductSaleValue;
use App\Models\SaleReturnDetail;
use Override;

class SaleReturnDetailRepository extends Repository implements SaleReturnDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturnDetail());
    }

    /**
     * Menyimpan (insert) data detail (produk) pengembalian penjualan.
     */
    #[Override]
    public function create(SaleReturnDetailDTO $dto): SaleReturnDetailEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleReturnDetailEntity(
                model: $data->load(['product', 'product.unit', 'product.category']),
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
     * Pemetaan class model ke class SaleReturnDetailEntity.
     */
    private function mapModelToSaleReturnDetailEntity(SaleReturnDetail $model): SaleReturnDetailEntity
    {
        return new SaleReturnDetailEntity(
            id: $model->id,
            saleReturnId: $model->sale_return_id,
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
                purchasePrice: parse_float($model->product->purchase_price),
                sellingPrice: parse_float($model->product->selling_price),
                discount: parse_float($model->product->discount),
                currentStock: parse_float($model->product->current_stock),
                minimumStock: parse_float($model->product->minimum_stock),
            ),
            quantity: parse_float($model->quantity),
            purchasePrice: parse_float($model->purchase_price),
            sellingPrice: parse_float($model->selling_price),
            discount: parse_float($model->discount),
            subtotal: parse_float($model->subtotal),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
