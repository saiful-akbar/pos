<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnCartDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleReturnCartDetailEntity;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\ValueObjects\CategoryProductSaleValue;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use App\Domain\Sale\ValueObjects\UnitProductSaleValue;
use App\Models\SaleReturnCartDetail;
use Illuminate\Database\Eloquent\Builder;
use Override;

class SaleReturnCartDetailRepository extends Repository implements SaleReturnCartDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturnCartDetail());
    }

    /**
     * Menambah (insert) data produk pengembalian penjualan.
     */
    #[Override]
    public function create(SaleReturnCartDetailDTO $dto): SaleReturnCartDetailEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleReturnCartDetailEntity(
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
     * Memperbarui data produk pengembalian penjualan.
     */
    #[Override]
    public function update(string $id, SaleReturnCartDetailDTO $dto): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->update($dto->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengahpus berdasarkan id.
     */
    #[Override]
    public function deleteById(string $id): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->forceDelete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan id cart dan id product.
     */
    #[Override]
    public function findByCartIdAndProductId(string $cartId, string $productId): ?SaleReturnCartDetailEntity
    {
        try {
            $data = $this->model->query()
                ->with(['product', 'product.unit', 'product.category'])
                ->where('sale_return_cart_id', $cartId)
                ->where('product_id', $productId)
                ->first();

            if ($data === null) {
                return null;
            }

            return $this->mapModelToSaleReturnCartDetailEntity(
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
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?SaleReturnCartDetailEntity
    {
        try {
            $data = $this->model->query()
                ->with(['product', 'product.unit', 'product.category'])
                ->where('id', $id)
                ->first();

            if ($data === null) {
                return null;
            }

            return $this->mapModelToSaleReturnCartDetailEntity(
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
     * Mengambil daftar data berdasarkan cart dalam fomat query builder.
     */
    #[Override]
    public function getByCartIdQuery(string $cartId): Builder
    {
        try {
            return $this->model->query()
                ->with(['product', 'product.unit', 'product.category'])
                ->where('sale_return_cart_id', $cartId);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menhitung total quantity berdasarkan id cart.
     */
    #[Override]
    public function sumQuantityByCartId(string $cartId): int|float
    {
        try {
            $result = $this->model->query()
                ->where('sale_return_cart_id', $cartId)
                ->sum('quantity');

            return parse_float($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total amoun (subtotal) berdasarkan id cart.
     */
    #[Override]
    public function sumSubtotalByCartId(string $cartId): int|float
    {
        try {
            $result = $this->model->query()
                ->where('sale_return_cart_id', $cartId)
                ->sum('subtotal');

            return parse_float($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil daftar berdasarkan return cart id.
     *
     * @return SaleReturnCartDetailEntity[]
     */
    #[Override]
    public function getByCartId(string $cartId): array
    {
        try {
            return $this->model->query()
                ->with(['product', 'product.unit', 'product.category'])
                ->where('sale_return_cart_id', $cartId)
                ->get()
                ->map(fn ($data) => $this->mapModelToSaleReturnCartDetailEntity($data))
                ->toArray();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan class model ke class SaleReturnCartDetailEntity
     */
    private function mapModelToSaleReturnCartDetailEntity(SaleReturnCartDetail $model): SaleReturnCartDetailEntity
    {
        return new SaleReturnCartDetailEntity(
            id: $model->id,
            saleReturnCartId: $model->sale_return_cart_id,
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
