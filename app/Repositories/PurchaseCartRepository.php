<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\AddProductPurchaseDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Purchase\ValueObjects\ProductPurchaseValue;
use App\Domain\Purchase\ValueObjects\SupplierPurchaseValue;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\PurchaseCart;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
     * Mengambil data keranjang pembelian.
     */
    #[Override]
    public function find(): ?PurchaseEntity
    {
        try {
            $data = $this->model->query()
                ->with(['user', 'details.product'])
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
     * Mengambil data keranjang pembelian beserta detail proknya.
     */
    #[Override]
    public function findWithDetails(): ?PurchaseDetailEntity
    {
        try {
            $data = $this->model->query()
                ->with(['user', 'details'])
                ->first();

            return $data === null ? null : $this->mapModelToPurchaseDetailEntity(model: $data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat purchase cart baru.
     */
    #[Override]
    public function create(string $userId): PurchaseDetailEntity
    {
        $now = new DateTimeImmutable();

        try {
            $data = $this->model->query()->create([
                'user_id' => $userId,
                'purchase_date' => $now->format('Y-m-d H:i:s'),
            ]);

            return $this->mapModelToPurchaseDetailEntity(
                model: $data->load(['user', 'details']),
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
     * Menambahkan produk ke details
     */
    #[Override]
    public function addProduct(AddProductPurchaseDTO $dto): void
    {
        try {
            $this->model->query()
                ->firstOrFail()
                ->details()
                ->create($dto->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update total amount
     */
    #[Override]
    public function updateTotalAmount(): void
    {
        try {
            $detail = DB::connection(pos_connection())
                ->table('purchase_cart_details')
                ->selectRaw('cast(sum(subtotal) as signed integer) as total')
                ->first();

            $this->model->query()->update([
                'total_amount' => $detail->total ?? 0,
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
     * Pemetaan model ke class ProductPurchaseValue
     */
    public function mapModelToProductPurchaseValue(Model $model): ProductPurchaseValue
    {
        return new ProductPurchaseValue(
            id: $model->product->id,
            code: $model->product->code,
            barcode: $model->product->barcode,
            name: $model->product->name,
            price: $model->price,
            quantity: $model->quantity,
            subtotal: $model->subtotal,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }

    /**
     * Ppemetaan model ke SupplierPurchaseValue
     */
    private function mapModelToSupplierPurchaseValue(Model $model): SupplierPurchaseValue
    {
        return new SupplierPurchaseValue(
            id: $model->id,
            code: $model->code,
            phoneNumber: $model->phone_umber,
            address: $model->address,
            notes: $model->notes,
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            celetedAt: $model->celeted_at,
        );
    }

    /**
     * Pemetaan model ke class UserPurchaseValue
     */
    private function mapModelToUserPurchaseValue(Model $model): UserPurchaseValue
    {
        return new UserPurchaseValue(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
            photo: $model->photo,
            isActive: $model->is_active === 1,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            celetedAt: $model->celeted_at,
        );
    }

    /**
     * Pemetaan model ke class PurchaseDetailEntity
     */
    private function mapModelToPurchaseDetailEntity(Model $model): PurchaseDetailEntity
    {
        $supplier = $model->supplier === null ? null : $this->mapModelToSupplierPurchaseValue($model->supplier);
        $user = $model->user === null ? null : $this->mapModelToUserPurchaseValue($model->user);
        $paymentStatus = $model->payment_status === null ? null : PaymentStatusEnum::tryFrom($model->payment_status);
        $products = $model->details->map(fn($detail) => $this->mapModelToProductPurchaseValue($detail))->toArray();

        return new PurchaseDetailEntity(
            id: $model->id,
            supplier: $supplier,
            user: $user,
            invoiceNumber: $model->invoice_number,
            totalAmount: $model->total_amount ?? 0,
            paymentStatus: $paymentStatus,
            notes: $model->notes,
            products: $products,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }

    /**
     * Pemetaan model ke class PurchaseEntity
     */
    private function mapModelToPurchaseEntity(Model $model): PurchaseEntity
    {
        $supplier = $model->supplier === null ? null : $this->mapModelToSupplierPurchaseValue($model->supplier);
        $user = $model->user === null ? null : $this->mapModelToUserPurchaseValue($model->user);
        $paymentStatus = $model->payment_status === null ? null : PaymentStatusEnum::tryFrom($model->payment_status);

        return new PurchaseEntity(
            id: $model->id,
            supplier: $supplier,
            user: $user,
            invoiceNumber: $model->invoice_number,
            totalAmount: $model->total_amount ?? 0,
            paymentStatus: $paymentStatus,
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
