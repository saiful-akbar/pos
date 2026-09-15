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
use Illuminate\Database\Eloquent\Builder;
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
     * Mengambil daftar detail sales berdasarkan sales id
     * dalam format query builder.
     */
    #[Override]
    public function getBySaleIdQuery(string $saleId): Builder
    {
        try {
            $relations = [
                'product',
                'product.unit',
                'product.category',
            ];

            return $this->model->query()
                ->with($relations)
                ->where('sale_id', $saleId);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan id sales dan id product.
     */
    #[Override]
    public function findBySaleIdAndProductId(string $saleId, string $productId): ?SaleDetailEntity
    {
        try {
            $data = $this->model->query()
                ->with(['product', 'product.unit', 'product.category'])
                ->where('sale_id', $saleId)
                ->where('product_id', $productId)
                ->first();

            if ($data === null) {
                return null;
            }

            return $this->mapModelToSaleDetailEntity(
                model: $data,
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
