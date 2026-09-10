<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\CreateSaleDTO;
use App\Base\Repository;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Sale\ValueObjects\CustomerSaleValue;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\Sale;
use Override;

class SaleRepository extends Repository implements SaleRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Sale());
    }

    /**
     * Membuat invoice number.
     */
    #[Override]
    public function generateInvoiceNumber(): string
    {
        try {
            $sale = $this->model->query()
                ->select('invoice_number')
                ->whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->orderBy('created_at', 'desc')
                ->lockForUpdate()
                ->first();

            if ($sale === null) {
                return 'INV'.date('y').date('m').'-00001';
            }

            $invoice = explode('-', $sale->invoice_number);
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
     * Menyimpan (insert) data penjualan.
     */
    #[Override]
    public function create(CreateSaleDTO $dto): SaleEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleEntity(
                model: $data->load('user', 'customer'),
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
     * Pemetaan class model ke class SaleEntity
     */
    private function mapModelToSaleEntity(Sale $model): SaleEntity
    {
        return new SaleEntity(
            id: $model->id,
            user: new UserSaleValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo,
            ),
            customer: $model->customer === null ? null : new CustomerSaleValue(
                id: $model->customer->id,
                name: $model->customer->name,
                phoneNumber: $model->customer->phone_number,
                address: $model->customer->address
            ),
            invoiceNumber: $model->invoice_number,
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalQuantity: $model->total_quantity,
            totalAmount: $model->total_amount,
            paidAmount: $model->paid_amount,
            changeAmount: $model->change_amount,
            paymentMethod: PaymentMethodEnum::from($model->payment_method),
            notes: $model->notes,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
