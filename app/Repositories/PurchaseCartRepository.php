<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseCartDetailDTO;
use App\Application\Purchase\DTOs\CreatePurchaseDTO;
use App\Application\Purchase\DTOs\UpdatePurchaseCartDetailDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Domain\Purchase\ValueObjects\SupplierPurchaseValue;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\PurchaseCart;
use App\Models\PurchaseCartDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;

class PurchaseCartRepository extends Repository implements PurchaseCartRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new PurchaseCart());
    }

    /**
     * Mengambil data purchase cart berdasarkan user id.
     */
    #[Override]
    public function findByUser(string $userId): ?PurchaseEntity
    {
        try {
            $relations = [
                'user',
                'details' => fn(HasMany $query) => $query->orderBy('created_at', 'desc'),
                'details.product',
            ];

            $data = $this->model->query()
                ->with($relations)
                ->where('user_id', $userId)
                ->first();

            return $data === null ? null : $this->mapModelToPurchaseEntity(model: $data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat data purchase cart baru.
     */
    #[Override]
    public function create(CreatePurchaseDTO $dto): PurchaseEntity
    {
        try {
            $data = $this->model->query()->create([
                'user_id' => $dto->userId,
                'purchase_date' => $dto->purchaseDate->format('Y-m-d H:i:s'),
                'total_quantity' => $dto->totalQuantity,
                'total_price' => $dto->totalPrice,
            ]);

            return $this->mapModelToPurchaseEntity(
                model: $data->load('user', 'details'),
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
     * Membuat (insert) data purchase cart detail
     */
    #[Override]
    public function createDetail(string $userId, CreatePurchaseCartDetailDTO $dto): void
    {
        try {
            $cart = $this->model->query()
                ->where('user_id', $userId)
                ->first();

            $detail = PurchaseCartDetail::query()
                ->where('purchase_cart_id', $cart->id)
                ->where('product_id', $dto->productId)
                ->first();

            if ($detail === null) {
                $cart->details()->create($dto->toArray());
            } else {
                $detail->quantity = $detail->quantity + $dto->quantity;
                $detail->purchase_price = $dto->purchasePrice ?: $detail->purchase_price;
                $detail->subtotal_price = $detail->purchase_price * ($detail->quantity + $dto->quantity);
                $detail->save();
            }

            $cart->update([
                'total_quantity' => $this->sumDetailQuantityByCartId($cart->id),
                'total_price' => $this->sumDetailSubtotalByCartId($cart->id),
            ]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update data cart detail.
     */
    #[Override]
    public function updateDetail(UpdatePurchaseCartDetailDTO $dto): void
    {
        try {
            $data = PurchaseCartDetail::query()->findOrFail($dto->id);

            $data->quantity = $dto->quantity;
            $data->purchase_price = $dto->purchasePrice;
            $data->subtotal_price = $dto->quantity * $dto->purchasePrice;
            $data->save();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Hapus cart detail
     */
    #[Override]
    public function deleteDetailById(string $id): void
    {
        try {
            PurchaseCartDetail::query()
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
     *
     * @param string $id
     * @return integer
     */
    #[Override]
    public function sumDetailQuantityByCartId(string $id): int
    {
        try {
            return (int) PurchaseCartDetail::query()
                ->where('purchase_cart_id', $id)
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
     *
     * @param string $id
     * @return integer
     */
    #[Override]
    public function sumDetailSubtotalByCartId(string $id): int
    {
        try {
            return (int) PurchaseCartDetail::query()
                ->where('purchase_cart_id', $id)
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
     * Pemetaan model ke PurchaseDetailEntity.
     */
    private function mapModelToPurchaseDetailEntity(Model $model): PurchaseDetailEntity
    {
        return new PurchaseDetailEntity(
            id: $model->id,
            purchaseId: $model->purchase_cart_id,
            product: new ProductPurchaseValue(
                id: $model->product->id,
                code: $model->product->code,
                barcode: $model->product->barcode,
                name: $model->product->name,
                image: $model->product->image,
            ),
            quantity: $model->quantity,
            purchasePrice: $model->purchase_price,
            subtotalPrice: $model->subtotal_price,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }

    /**
     * Pemetaan model ke PurchaseEntity
     */
    private function mapModelToPurchaseEntity(Model $model): PurchaseEntity
    {
        $details = [];

        foreach ($model->details as $detail) {
            $details[] = $this->mapModelToPurchaseDetailEntity($detail);
        }

        return new PurchaseEntity(
            id: $model->id,
            user: new UserPurchaseValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
                isActive: $model->user->is_active === 1
            ),
            supplier: $model->supplier === null ? null : new SupplierPurchaseValue(
                id: $model->supplier->id,
                code: $model->supplier->code,
                name: $model->supplier->name,
                phoneNumber: $model->supplier->phone_number,
                address: $model->supplier->address,
                notes: $model->supplier->notes,
                isActive: $model->supplier->is_active === 1,
            ),
            purchaseDate: parse_date_time_immutable($model->purchase_date),
            invoiceNumber: $model->invoice_number === null ? null : $model->invoice_number,
            totalQuantity: $model->total_quantity,
            totalPrice: $model->total_price,
            paymentStatus: $model->payment_status === null ? null : PaymentStatusEnum::tryFrom($model->payment_status),
            notes: $model->notes === null ? null : $model->notes,
            details: $details,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
