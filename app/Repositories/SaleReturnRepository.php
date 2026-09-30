<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\GetSaleReturnDTO;
use App\Application\Sale\DTOs\SaleReturnDTO;
use App\Base\Repository;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use App\Domain\Sale\ValueObjects\CustomerSaleValue;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\SaleReturn;
use Illuminate\Database\Eloquent\Builder;
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
     * Mengmabil daftar pengembalian penjualan
     *
     * @return SaleReturnEntity[]
     */
    #[Override]
    public function get(GetSaleReturnDTO $params): array
    {
        try {
            if ($params->allAttributesIsNull()) {
                return [];
            }

            $query = $this->model->query()->with([
                'user',
                'sale.user',
                'sale.customer',
            ]);

            if ($params->datesIsNotNull()) {
                $query->whereBetween('transaction_date', [
                    $params->fromDate->format('Y-m-d'),
                    $params->toDate->format('Y-m-d'),
                ]);
            }

            if ($params->returnNumberIsNotNull()) {
                $query->where('return_number', $params->returnNumber);
            }

            if ($params->invoiceNumberIsNotNull()) {
                $query->whereHas('sale', function (Builder $subQuery) use ($params): void {
                    $subQuery->where('invoice_number', $params->invoiceNumber);
                });
            }

            return $query->orderBy('transaction_date', 'asc')
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
     * Generate nomor return.
     */
    #[Override]
    public function generateReturnNumber(): string
    {
        try {
            $return = $this->model->query()
                ->withTrashed()
                ->select('return_number')
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->orderBy('created_at', 'desc')
                ->lockForUpdate()
                ->first();

            if ($return === null) {
                return 'RTN'.date('y').date('m').'-00001';
            }

            $invoice = explode('-', $return->return_number);
            $code = $invoice[0];
            $number = parse_int($invoice[1]) + 1;

            if ($number <= 9) {
                $result = "{$code}-0000{$number}";
            } elseif ($number <= 99) {
                $result = "{$code}-000{$number}";
            } elseif ($number <= 999) {
                $result = "{$code}-00{$number}";
            } elseif ($number <= 9999) {
                $result = "{$code}-0{$number}";
            } else {
                $result = "{$code}-{$number}";
            }

            return $result;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Soft delete by id.
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
     * Mengambil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?SaleReturnEntity
    {
        try {
            $data = $this->model->query()->find($id);

            if ($data === null) {
                return null;
            }

            return $this->mapModelToSaleReturnEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
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
            id: $model->id,
            name: $model->name,
            phoneNumber: $model->phone_number,
            address: $model->address
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

    /**
     * Pemetaan class model ke class SaleReturnEntity
     */
    private function mapModelToSaleReturnEntity(SaleReturn $model): SaleReturnEntity
    {
        return new SaleReturnEntity(
            id: $model->id,
            sale: $this->mapModelToSaleEntity($model->sale),
            user: $this->mapModelToUserSaleValue($model->user),
            returnNumber: $model->return_number,
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalQuantity: parse_float($model->total_quantity),
            totalAmount: parse_float($model->total_amount),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
