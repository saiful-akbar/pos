<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\CreateSaleDTO;
use App\Application\Sale\DTOs\GetSalesDTO;
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
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Unit;
use App\Models\User;
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
     * Mengambil daftar berdasarkan rendang tanggal transaksi.
     *
     * @return SaleEntity[]
     */
    #[Override]
    public function getByDate(GetSalesDTO $params): array
    {
        try {
            if ($params->allIsNull()) {
                return [];
            }

            $query = $this->model->query()->with(['user', 'customer']);

            if ($params->dateIsNotNull()) {
                $query->whereBetween('transaction_date', [$params->fromDate->format('Y-m-d'), $params->toDate->format('Y-m-d')]);
            }

            if ($params->invoiceNumberIsNotNull()) {
                $query->where('invoice_number', $params->invoiceNumber);
            }

            return $query->orderBy('transaction_date', 'asc')
                ->get()
                ->map(fn (Sale $data) => $this->mapModelToSaleEntity($data))
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
     * Pemetaan class Model ke class UserSaleValue.
     */
    private function mapModelToUserSaleValue(User $model): UserSaleValue
    {
        return new UserSaleValue(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
            photo: $model->photo,
        );
    }

    /**
     * Pemetaan class Model ke class CustomerSaleValue.
     */
    private function mapModelToCustomerSaleValue(?Customer $model): ?CustomerSaleValue
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
     * Pemetaan class model ke class SaleEntity
     */
    private function mapModelToSaleEntity(Sale $model): SaleEntity
    {
        return new SaleEntity(
            id: $model->id,
            user: $this->mapModelToUserSaleValue($model->user),
            customer: $this->mapModelToCustomerSaleValue($model->cutomer),
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
     * Pemetaan class Model ke class CategoryProductSaleValue.
     */
    private function mapModelToCategoryProductSaleValue(Category $model): CategoryProductSaleValue
    {
        return new CategoryProductSaleValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1
        );
    }

    /**
     * Pemetaan class Model ke class UnitProductSaleValue.
     */
    private function mapModelToUnitProductSaleValue(Unit $model): UnitProductSaleValue
    {
        return new UnitProductSaleValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1
        );
    }

    /**
     * Pemetaan class Model ke class ProductSaleValue.
     */
    private function mapModelToProductSaleValue(Product $model): ProductSaleValue
    {
        return new ProductSaleValue(
            id: $model->id,
            category: $this->mapModelToCategoryProductSaleValue($model->category),
            unit: $this->mapModelToUnitProductSaleValue($model->unit),
            code: $model->code,
            barcode: $model->barcode,
            name: $model->name,
            image: $model->image,
            purchasePrice: parse_float($model->purchase_price),
            sellingPrice: parse_float($model->selling_price),
            discount: parse_float($model->discount),
            currentStock: parse_float($model->current_stock),
            minimumStock: parse_float($model->minimum_stock),
        );
    }

    /**
     * Pemetaan class model ke SaleDetailEntity.
     */
    private function mapModelToSaleDetailEntity(SaleDetail $model): SaleDetailEntity
    {
        return new SaleDetailEntity(
            id: $model->id,
            saleId: $model->sale_id,
            product: $this->mapModelToProductSaleValue($model->product),
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

    /**
     * Pemetaan class model ke class SaleWithDetailEntity.
     */
    private function mapModelToSaleWithDetailEntity(Sale $model): SaleWithDetailEntity
    {
        return new SaleWithDetailEntity(
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
            details: array_map(fn ($detail) => $this->mapModelToSaleDetailEntity($detail), $model->details),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }
}
