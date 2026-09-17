<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Base\Repository;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use App\Domain\Sale\ValueObjects\CustomerSaleValue;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\SaleReturn;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Override;

class SaleReturnRepository extends Repository implements SaleReturnRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturn());
    }

    /**
     * Menyimpan data pengembalian penjualan.
     */
    #[Override]
    public function create(SaleReturnDTO $dto): SaleReturnEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleReturnEntity(
                model: $data->load(['user', 'sale.user', 'sale.customer']),
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
     * Mengambil daftar berdasarkan tanggal.
     *
     * @return SaleReturnEntity[]
     */
    #[Override]
    public function getByDate(DateTimeInterface $fromDate, DateTimeInterface $toDate): array
    {
        try {
            return $this->model->query()
                ->with(['user', 'sale.user', 'sale.customer'])
                ->whereBetween('transaction_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                ->orderBy('transaction_date', 'asc')
                ->get()
                ->map(fn ($data) => $this->mapModelToSaleReturnEntity($data))
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
     * Pemetaan class model ke class SaleReturnEntity
     */
    private function mapModelToSaleReturnEntity(SaleReturn $model): SaleReturnEntity
    {
        return new SaleReturnEntity(
            id: $model->id,
            sale: $this->mapModelToSaleEntity($model->sale),
            user: $this->mapModelToUserSaleValue($model->user),
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalQuantity: parse_float($model->total_quantity),
            totalAmount: parse_float($model->total_amount),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }

    /**
     * Pemetaan class model ke class CustomerSaleValue.
     */
    private function mapModelToCustomerSaleValue(?Model $model): ?CustomerSaleValue
    {
        if ($model === null) {
            return null;
        }

        return new CustomerSaleValue(
            id: $model->customer->id,
            name: $model->customer->name,
            phoneNumber: $model->customer->phone_number,
            address: $model->customer->address
        );
    }

    /**
     * Pemetaan class model ke class UserSaleValue.
     */
    private function mapModelToUserSaleValue(Model $model): UserSaleValue
    {
        return new UserSaleValue(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
            photo: $model->photo,
        );
    }

    /**
     * Pemetaan class model ke class SaleEntity
     */
    private function mapModelToSaleEntity(Model $model): SaleEntity
    {
        return new SaleEntity(
            id: $model->id,
            user: $this->mapModelToUserSaleValue($model->user),
            customer: $this->mapModelToCustomerSaleValue($model->customer),
            invoiceNumber: $model->invoice_number,
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalQuantity: parse_float($model->total_quantity),
            totalAmount: parse_float($model->total_amount),
            paidAmount: parse_float($model->paid_amount),
            changeAmount: parse_float($model->change_amount),
            paymentMethod: PaymentMethodEnum::from($model->payment_method),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
