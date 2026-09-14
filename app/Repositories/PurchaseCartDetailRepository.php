<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseCartDetailDTO;
use App\Application\Purchase\DTOs\UpdatePurchaseCartDetailDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseCartDetailEntity;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Models\PurchaseCartDetail;
use Override;

class PurchaseCartDetailRepository extends Repository implements PurchaseCartDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new PurchaseCartDetail());
    }

    /**
     * Membuat (insert) atau memperbarui (update) data cart detail
     */
    #[Override]
    public function createOrUpdate(CreatePurchaseCartDetailDTO $dto): PurchaseCartDetailEntity
    {
        try {
            $data = $this->model->query()
                ->where('purchase_cart_id', $dto->cartId)
                ->where('product_id', $dto->productId)
                ->first();

            if ($data === null) {
                return $this->mapModelToPurchaseCartDetailEntity(
                    model: $this->model->query()->create([
                        'purchase_cart_id' => $dto->cartId,
                        'product_id' => $dto->productId,
                        'quantity' => $dto->quantity,
                        'purchase_price' => $dto->purchasePrice,
                        'subtotal_price' => $dto->quantity * $dto->purchasePrice,
                    ]),
                );
            }

            $quantity = $data->quantity + $dto->quantity;
            $subtotal = $quantity * $dto->purchasePrice;

            $data->quantity = $quantity;
            $data->purchase_price = $dto->purchasePrice;
            $data->subtotal_price = $subtotal;
            $data->save();

            return $this->mapModelToPurchaseCartDetailEntity(
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
     * Update data purcahse cart detail.
     */
    #[Override]
    public function update(UpdatePurchaseCartDetailDTO $dto): void
    {
        try {
            $this->model->query()
                ->findOrFail($dto->id)
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
     * Mengahpus purcahse cart detail
     */
    #[Override]
    public function deleteById(string $id): void
    {
        try {
            $this->model->query()
                ->findOrFail($id)
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
     * Menghitung total quantity cart detail berdasarkan id cart.
     */
    #[Override]
    public function sumQuantityByCartId(string $cartId): int
    {
        try {
            return (int) $this->model->query()
                ->where('purchase_cart_id', $cartId)
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
     * Menghitung total subtotal berdasarkan id cart.
     */
    #[Override]
    public function sumSubtotalByCartId(string $cartId): int
    {
        try {
            return (int) $this->model->query()
                ->where('purchase_cart_id', $cartId)
                ->sum('subtotal_price');
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil daftar purchase cart detail berdasarkan id dari purchase cart.
     *
     * @return PurchaseCartDetailEntity[]
     */
    #[Override]
    public function getByCartId(string $cartId): array
    {
        try {
            return $this->model->query()
                ->with('product')
                ->where('purchase_cart_id', $cartId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(fn ($detail) => $this->mapModelToPurchaseCartDetailEntity($detail))
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
     * Pemetaan model ke PurchaseCartDetailEntity.
     */
    private function mapModelToPurchaseCartDetailEntity(PurchaseCartDetail $model): PurchaseCartDetailEntity
    {
        return new PurchaseCartDetailEntity(
            id: $model->id,
            purchaseCartId: $model->purchase_cart_id,
            product: new ProductPurchaseValue(
                id: $model->product->id,
                code: $model->product->code,
                barcode: $model->product->barcode,
                name: $model->product->name,
                image: $model->product->image
            ),
            quantity: parse_float($model->quantity),
            purchasePrice: parse_float($model->purchase_price),
            subtotalPrice: parse_float($model->subtotal_price),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
