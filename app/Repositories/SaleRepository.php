<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\CreateSaleDTO;
use App\Base\Repository;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Entities\SaleWithDetailEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Sale\ValueObjects\CategoryProductSaleValue;
use App\Domain\Sale\ValueObjects\CustomerSaleValue;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use App\Domain\Sale\ValueObjects\UnitProductSaleValue;
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
     * Mengambil data sales berdsarkan id.
     */
    #[Override]
    public function findById(string $id, string $responseType = SaleWithDetailEntity::class): SaleWithDetailEntity|SaleEntity|null
    {
        try {
            $data = $this->model->query()
                ->with(['user', 'customer', 'details.product.category', 'details.product.unit'])
                ->find($id);

            if ($data === null) {
                return null;
            }

            if ($responseType === SaleEntity::class) {
                return $this->mapModelToSaleEntity(
                    model: $data,
                );
            }

            return $this->mapModelToSaleWithDetailEntity(
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
     * Mengambil data penjualan berdasarkan nomor faktur.
     */
    #[Override]
    public function findByInvoice(string $invoice): ?SaleEntity
    {
        try {
            $data = $this->model->query()
                ->with(['user', 'customer'])
                ->where('invoice_number', $invoice)
                ->first();

            if ($data === null) {
                return null;
            }

            return $this->mapModelToSaleEntity(
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
     * Pemetaan class model ke class SaleWithDetailEntity.
     */
    private function mapModelToSaleWithDetailEntity(Sale $model): SaleWithDetailEntity
    {
        $details = [];

        foreach ($model->details as $detail) {
            $details[] = new SaleDetailEntity(
                id: $detail->id,
                saleId: $detail->sale_id,
                product: new ProductSaleValue(
                    id: $detail->product->id,
                    unit: new UnitProductSaleValue(
                        id: $detail->product->unit->id,
                        code: $detail->product->unit->code,
                        name: $detail->product->unit->name,
                        isActive: $detail->product->unit->is_active === 1
                    ),
                    category: new CategoryProductSaleValue(
                        id: $detail->product->unit->id,
                        code: $detail->product->unit->code,
                        name: $detail->product->unit->name,
                        isActive: $detail->product->unit->is_active === 1
                    ),
                    code: $detail->product->code,
                    barcode: $detail->product->barcode,
                    name: $detail->product->name,
                    image: $detail->product->image,
                    purchasePrice: parse_float($detail->product->purchase_price),
                    sellingPrice: parse_float($detail->product->selling_price),
                    discount: parse_float($detail->product->discount),
                    currentStock: parse_float($detail->product->current_stock),
                    minimumStock: parse_float($detail->product->minimum_stock),
                ),
                quantity: parse_float($detail->quantity),
                purchasePrice: parse_float($detail->purchase_price),
                sellingPrice: parse_float($detail->selling_price),
                discount: parse_float($detail->discount),
                subtotal: parse_float($detail->subtotal),
                createdAt: $detail->created_at,
                updatedAt: $detail->updated_at,
                deletedAt: $detail->deleted_at,
            );
        }

        return new SaleWithDetailEntity(
            id: $model->id,
            user: new UserSaleValue(
                id: $model->user->id,
                username: $model->user->username,
                fullName: $model->user->full_name,
                photo: $model->user->photo
            ),
            customer: $model->customer === null ? null : new CustomerSaleValue(
                id: $model->customer->id,
                name: $model->customer->name,
                phoneNumber: $model->customer->phone_number,
                address: $model->customer->address,
            ),
            invoiceNumber: $model->invoice_number,
            transactionDate: parse_date_time_immutable($model->transaction_date),
            totalQuantity: parse_float($model->total_quantity),
            totalAmount: parse_float($model->total_amount),
            paidAmount: parse_float($model->paid_amount),
            changeAmount: parse_float($model->change_amount),
            paymentMethod: PaymentMethodEnum::from($model->payment_method),
            notes: $model->notes,
            details: $details,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
