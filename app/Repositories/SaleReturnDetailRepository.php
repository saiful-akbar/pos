<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\GetSaleReturnDTO;
use App\Application\Sale\DTOs\SaleReturnDetailDTO;
use App\Base\Repository;
use App\Domain\Purchase\Enums\PaymentMethodEnum;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Entities\SaleReturnDetailEntity;
use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;
use App\Domain\Sale\ValueObjects\CategoryProductSaleValue;
use App\Domain\Sale\ValueObjects\CustomerSaleValue;
use App\Domain\Sale\ValueObjects\ProductSaleValue;
use App\Domain\Sale\ValueObjects\UnitProductSaleValue;
use App\Domain\Sale\ValueObjects\UserSaleValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Override;

class SaleReturnDetailRepository extends Repository implements SaleReturnDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleReturnDetail());
    }

    /**
     * Menyimpan (insert) data detail (produk) pengembalian penjualan.
     */
    #[Override]
    public function create(SaleReturnDetailDTO $dto): SaleReturnDetailEntity
    {
        try {
            $data = $this->model->query()->create($dto->toArray());

            return $this->mapModelToSaleReturnDetailEntity(
                model: $data->load(['product', 'product.unit', 'product.category']),
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
     * Mengambil daftar data detail pengembalian pembelian.
     *
     * @return SaleReturnDetailEntity[]
     */
    #[Override]
    public function get(GetSaleReturnDTO $params): array
    {
        try {
            if ($params->allAttributesIsNull()) {
                return [];
            }

            $query = $this->model->query()->with([
                'saleReturn.user',
                'saleReturn.sale.user',
                'saleReturn.sale.customer',
                'product.unit',
                'product.category',
            ]);

            if ($params->datesIsNotNull()) {
                $query->whereHas('saleReturn', function (Builder $subQuery) use ($params): void {
                    $subQuery->whereBetween('transaction_date', [
                        $params->fromDate->format('Y-m-d'),
                        $params->toDate->format('Y-m-d'),
                    ]);
                });
            }

            if ($params->returnNumberIsNotNull()) {
                $query->whereHas('saleReturn', function (Builder $subQuery) use ($params): void {
                    $subQuery->where('return_number', $params->returnNumber);
                });
            }

            if ($params->invoiceNumberIsNotNull()) {
                $query->whereHas('saleReturn.sale', function (Builder $subQuery) use ($params): void {
                    $subQuery->where('invoice_number', $params->invoiceNumber);
                });
            }

            return $query->get()
                ->map(fn ($data) => $this->mapModelToSaleReturnDetailEntity($data))
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
     * Soft delete by sale return id
     */
    #[Override]
    public function removeBySaleReturnId(string $saleReturnId): int
    {
        try {
            return $this->model->query()
                ->where('sale_return_id', $saleReturnId)
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
     * Mengambil daftar data detail pengembalian pembelian
     * berdasarkan sale reutrn id.
     *
     * @return SaleReturnDetailEntity[]
     */
    #[Override]
    public function getBySaleReturnId(string $saleReturnId): array
    {
        try {
            return $this->model->query()
                ->with(['product.category', 'product.unit'])
                ->where('sale_return_id', $saleReturnId)
                ->get()
                ->map(fn ($data) => $this->mapModelToSaleReturnDetailEntity($data))
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
    private function mapModelToSaleReturnEntity(?SaleReturn $model): ?SaleReturnEntity
    {
        if ($model === null) {
            return null;
        }

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

    /**
     * Pemetaan class model ke class SaleReturnDetailEntity.
     */
    private function mapModelToSaleReturnDetailEntity(SaleReturnDetail $model): SaleReturnDetailEntity
    {
        return new SaleReturnDetailEntity(
            id: $model->id,
            saleReturnId: $model->sale_return_id,
            product: $this->mapModelToProductSaleValue($model->product),
            quantity: parse_float($model->quantity),
            purchasePrice: parse_float($model->purchase_price),
            sellingPrice: parse_float($model->selling_price),
            discount: parse_float($model->discount),
            subtotal: parse_float($model->subtotal),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
            saleReturn: $this->mapModelToSaleReturnEntity($model->saleReturn),
        );
    }
}
