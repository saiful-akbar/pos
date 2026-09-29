<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\CreateSaleDetailDTO;
use App\Application\Sale\DTOs\GetSalesDTO;
use App\Base\Repository;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
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
use Illuminate\Database\Eloquent\Builder;
use Override;

class SaleDetailRepository extends Repository implements SaleDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleDetail());
    }

    /**
     * Menyimpan (insert) data detail penjualan.
     */
    #[Override]
    public function create(CreateSaleDetailDTO $dto): SaleDetailEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleDetailEntity(
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
     * Mengambil daftar detail sales berdasarkan sales id
     * dalam format query builder.
     */
    #[Override]
    public function getBySaleIdQuery(string $saleId): Builder
    {
        try {
            $relations = [
                'product',
                'product.unit',
                'product.category',
            ];

            return $this->model->query()
                ->with($relations)
                ->where('sale_id', $saleId);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Mengambil data berdasarkan id sales dan id product.
     */
    #[Override]
    public function findBySaleIdAndProductId(string $saleId, string $productId): ?SaleDetailEntity
    {
        try {
            $data = $this->model->query()
                ->with(['product', 'product.unit', 'product.category'])
                ->where('sale_id', $saleId)
                ->where('product_id', $productId)
                ->first();

            if ($data === null) {
                return null;
            }

            return $this->mapModelToSaleDetailEntity(
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
     * Mengambil daftar detail penjualan.
     *
     * @return SaleDetailEntity[]
     */
    #[Override]
    public function get(GetSalesDTO $params): array
    {
        try {
            if ($params->allIsNull()) {
                return [];
            }

            return $this->model->query()
                ->with(['product.category', 'product.unit', 'sale.user', 'sale.customer'])
                ->whereHas('sale', function (Builder $query) use ($params) {
                    if ($params->dateIsNotNull()) {
                        $fromDate = $params->fromDate->format('Y-m-d');
                        $toDate = $params->toDate->format('Y-m-d');

                        $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                    }

                    if ($params->invoiceNumberIsNotNull()) {
                        $query->where('invoice_number', $params->invoiceNumber);
                    }
                })
                ->get()
                ->map(fn ($data) => $this->mapModelToSaleDetailEntity($data))
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
    private function mapModelToSaleEntity(?Sale $model): ?SaleEntity
    {
        if ($model === null) {
            return null;
        }

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
            sale: $this->mapModelToSaleEntity($model->sale),
        );
    }
}
