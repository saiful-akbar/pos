<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleCartDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleCartDetailEntity;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\ValueObjects\CategoryProductSaleValue;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use App\Domain\Sale\ValueObjects\UnitProductSaleValue;
use App\Models\SaleCartDetail;
use Illuminate\Database\Eloquent\Builder;
use Override;

class SaleCartDetailRepository extends Repository implements SaleCartDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleCartDetail());
    }

    /**
     * Menyimpan (insert) data cart detail baru.
     */
    #[Override]
    public function create(SaleCartDetailDTO $dto): ?SaleCartDetailEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleCartDetailEntity(
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
     * Memperbarui data cart detail
     */
    #[Override]
    public function update(string $id, SaleCartDetailDTO $dto): int
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
     * Membuat atau memperbarui data cart detail.
     */
    #[Override]
    public function createOrUpdate(SaleCartDetailDTO $dto): void
    {
        try {
            $cartDetail = $this->model->query()
                ->where('sale_cart_id', $dto->cart->id)
                ->where('product_id', $dto->product->id)
                ->first();

            if ($cartDetail === null) {
                $this->model->query()->create($dto->toArray());

                return;
            }

            $quantity = $cartDetail->quantity + $dto->quantity;
            $totalPrice = $quantity * $dto->product->sellingPrice;
            $discountPrice = ($dto->product->sellingPrice * $dto->product->discount) / 100;
            $subtotal = $totalPrice - $discountPrice;

            $cartDetail->quantity = $quantity;
            $cartDetail->purchase_price = $dto->product->purchasePrice;
            $cartDetail->selling_price = $dto->product->sellingPrice;
            $cartDetail->discount = $dto->product->discount;
            $cartDetail->subtotal = $subtotal;
            $cartDetail->save();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data cart detail berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?SaleCartDetailEntity
    {
        try {
            $data = $this->model->query()
                ->with(['product.category', 'product.unit'])
                ->find($id);

            if ($data === null) {
                return null;
            }

            return $this->mapModelToSaleCartDetailEntity(
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
     * Mengambil daftar cart detail berdasarkan
     * cart id dalam format dalam format query builder.
     */
    #[Override]
    public function getByCartIdQuery(string $cartId): Builder
    {
        try {
            return $this->model->query()
                ->with(['product.category', 'product.unit'])
                ->where('sale_cart_id', '=', $cartId)
                ->orderBy('created_at', 'desc');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total quantity
     */
    #[Override]
    public function sumQuantityByCartId(string $cartId): int
    {
        try {
            return (int) $this->model->query()
                ->where('sale_cart_id', $cartId)
                ->sum('quantity');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghitung total subtotal.
     */
    #[Override]
    public function sumSubtotalByCartId(string $cartId): int
    {
        try {
            return (int) $this->model->query()
                ->where('sale_cart_id', $cartId)
                ->sum('subtotal');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus data cart details
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
     * Mengambil daftar cart detail berdasarkan cart id.
     *
     * @return SaleCartDetailEntity[]
     */
    #[Override]
    public function getByCartId(string $cartId): array
    {
        try {
            return $this->model->query()
                ->with(['product.category', 'product.unit'])
                ->where('sale_cart_id', $cartId)
                ->get()
                ->map(fn ($data) => $this->mapModelToSaleCartDetailEntity($data))
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
     * Pemetaan class model ke SalesCartDetailEntity.
     */
    private function mapModelToSaleCartDetailEntity(SaleCartDetail $model): SaleCartDetailEntity
    {
        return new SaleCartDetailEntity(
            id: $model->id,
            saleCartId: $model->sale_cart_id,
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
