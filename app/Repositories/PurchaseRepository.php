<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Purchase\DTOs\CreatePurchaseDTO;
use App\Application\Purchase\DTOs\GetPurchasesDTO;
use App\Application\Purchase\DTOs\UpdatePurchaseDTO;
use App\Base\Repository;
use App\Domain\Purchase\Entities\PurchaseEntity;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use App\Domain\Purchase\ValueObjects\SupplierPurchaseValue;
use App\Domain\Purchase\ValueObjects\UserPurchaseValue;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Override;

class PurchaseRepository extends Repository implements PurchaseRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Purchase());
    }

    /**
     * Membuat (insert) data pembalian.
     */
    #[Override]
    public function create(CreatePurchaseDTO $dto): PurchaseEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToPurchaseEntity(
                model: $data->load('user', 'supplier'),
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
     * Menmabil data penjualan berdasarkan rentang waktu dalam format query builder.
     */
    #[Override]
    public function getByDateRangeQuery(DateTimeInterface $fromDate, DateTimeInterface $toDate, ?PaymentStatusEnum $paymentStatus = null): Builder
    {
        try {
            $paymentQuery = PurchasePayment::query()
                ->select('id', 'purchase_id', DB::raw('coalesce(cast(sum(amount) as signed integer), 0) as total_amount'))
                ->groupBy('id', 'purchase_id');

            $columns = [
                'purchases.id as id',
                'purchases.purchase_date as purchase_date',
                'purchases.invoice_number as invoice_number',
                'purchases.total_quantity as total_quantity',
                'purchases.total_price as total_price',
                'purchases.payment_status as payment_status',
                'suppliers.id as supplier_id',
                'suppliers.name as supplier_name',
                DB::raw('coalesce(payments.total_amount, 0) as payment_total_amount'),
            ];

            return $this->model->query()
                ->select($columns)
                ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->leftJoinSub($paymentQuery, 'payments', fn ($join) => $join->on('payments.purchase_id', '=', 'purchases.id'))
                ->whereBetween('purchases.purchase_date', [$fromDate->format('Y-m-d 00:00:00'), $toDate->format('Y-m-d 23:59:59')])
                ->when($paymentStatus, function (Builder $query, PaymentStatusEnum $paymentStatus): void {
                    $query->where('payment_status', $paymentStatus->value);
                });
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data pembelian berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?PurchaseEntity
    {
        try {
            $data = $this->model->query()
                ->with(['user', 'supplier'])
                ->find($id);

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
     * Memperbarui data pembelian.
     */
    #[Override]
    public function update(UpdatePurchaseDTO $dto): void
    {
        try {
            $this->model->query()
                ->where('id', $dto->id)
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
     * Mengambil daftar penjualan
     */
    #[Override]
    public function get(GetPurchasesDTO $dto): array
    {
        try {
            $paymentQuery = PurchasePayment::query()
                ->select('purchase_id', DB::raw('coalesce(sum(amount), 0) as paid_amount'))
                ->groupBy('purchase_id');

            $relations = ['user', 'supplier'];
            $columns = ['purchases.*', 'payments.paid_amount as paid_amount'];

            $query = $this->model->query()
                ->select($columns)
                ->with($relations)
                ->joinSub($paymentQuery, 'payments', 'purchases.id', '=', 'payments.purchase_id')
                ->whereBetween('purchases.purchase_date', [$dto->fromDate->format('Y-m-d'), $dto->toDate->format('Y-m-d')]);

            if ($dto->paymentStatus !== null) {
                $query->where('purchases.payment_status', $dto->paymentStatus->value);
            }

            return $query->orderBy('purchases.purchase_date', 'asc')
                ->get()
                ->map(fn ($data) => $this->mapModelToPurchaseEntity($data))
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
     * Soft delete
     */
    #[Override]
    public function removeById(string $id): int
    {
        try {
            return $this->model->query()
                ->where('id', $id)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan model ke PurchaseEntity.
     */
    private function mapModelToPurchaseEntity(Purchase $model): PurchaseEntity
    {
        return new PurchaseEntity(
            id: $model->id,
            user: new UserPurchaseValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
                isActive: $model->user->is_active === 1,
            ),
            supplier: new SupplierPurchaseValue(
                id: $model->supplier->id,
                code: $model->supplier->code,
                name: $model->supplier->name,
                phoneNumber: $model->supplier->phone_number,
                address: $model->supplier->address,
                notes: $model->supplier->notes,
                isActive: $model->supplier->is_active === 1
            ),
            purchaseDate: parse_date_time_immutable($model->purchase_date),
            invoiceNumber: $model->invoice_number,
            totalQuantity: parse_float($model->total_quantity),
            totalPrice: parse_float($model->total_price),
            paymentStatus: PaymentStatusEnum::tryFrom($model->payment_status),
            paidAmount: parse_float($model->paid_amount ?? 0),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
