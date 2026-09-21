<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\StockOpname\DTOs\DailyStockOpnameDTO;
use App\Base\Repository;
use App\Domain\StockOpname\Entities\DailyStockOpnameDetailEntity;
use App\Domain\StockOpname\Entities\DailyStockOpnameEntity;
use App\Domain\StockOpname\Entities\DailyStockOpnameWithDetailsEntity;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use App\Domain\StockOpname\ValueObjects\CategoryProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\ProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\UnitProductStockOpnameValue;
use App\Domain\StockOpname\ValueObjects\UserStockOpnameValue;
use App\Models\Category;
use App\Models\DailyStockOpname;
use App\Models\DailyStockOpnameDetail;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use DateTimeInterface;
use Override;

class DailyStockOpnameRepository extends Repository implements DailyStockOpnameRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new DailyStockOpname());
    }

    /**
     * Mengambil daftar stock opname berdasarkan tentang tanggal.
     *
     * @return DailyStockOpnameEntity[]
     */
    #[Override]
    public function getByDate(DateTimeInterface $fromDate, DateTimeInterface $toDate): array
    {
        try {
            return $this->model->query()
                ->with('user')
                ->whereBetween('date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                ->orderBy('date', 'desc')
                ->get()
                ->map(fn ($data) => $this->mapModelToDailyStockOpnameEntity($data))
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
     * Mengmabil data berdasarkan id.
     */
    #[Override]
    public function findById(string $id): ?DailyStockOpnameEntity
    {
        try {
            $data = $this->model->query()
                ->with('user')
                ->find($id);

            if ($data === null) {
                return null;
            }

            return $this->mapModelToDailyStockOpnameEntity($data);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Memeriksa apakah data ada atau tidak berdasarkan ind.
     */
    #[Override]
    public function existsById(string $id): bool
    {
        try {
            return $this->model->query()
                ->where('id', '=', $id)
                ->exists();
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update data.
     */
    #[Override]
    public function update(string $id, DailyStockOpnameDTO $value): int
    {
        try {
            return $this->model->query()
                ->where('id', '=', $id)
                ->update($value->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan class Model ke class UserStockOpnameValue
     */
    private function mapModelToUserStockOpnameValue(User $model): UserStockOpnameValue
    {
        return new UserStockOpnameValue(
            id: $model->id,
            username: $model->username,
            fullName: $model->full_name,
            photo: $model->photo,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class DailyStockOpnameEntity.
     */
    private function mapModelToDailyStockOpnameEntity(DailyStockOpname $model): DailyStockOpnameEntity
    {
        return new DailyStockOpnameEntity(
            id: $model->id,
            user: $this->mapModelToUserStockOpnameValue($model->user),
            date: parse_date_time_immutable($model->date),
            totalProduct: parse_float($model->total_product),
            totalSystemStock: parse_float($model->total_system_stock),
            totalPhysicalStock: parse_float($model->total_physical_stock),
            totalDifference: parse_float($model->total_difference),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }

    /**
     * Pemetaan class Model ke class CategoryProductStockOpnameValue
     */
    private function mapModelToCategoryProductStockOpnameValue(Category $model): CategoryProductStockOpnameValue
    {
        return new CategoryProductStockOpnameValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class model ke class UnitProductStockOpnameValue.
     */
    private function mapModelToUnitProductStockOpnameValue(Unit $model): UnitProductStockOpnameValue
    {
        return new UnitProductStockOpnameValue(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class ProductStockOpnameValue
     */
    private function mapModelToProductStockOpnameValue(Product $model): ProductStockOpnameValue
    {
        return new ProductStockOpnameValue(
            id: $model->id,
            category: $this->mapModelToCategoryProductStockOpnameValue($model->category),
            unit: $this->mapModelToUnitProductStockOpnameValue($model->unit),
            code: $model->code,
            barcode: $model->barcode,
            name: $model->name,
            image: $model->image,
            purchasePrice: parse_float($model->purchase_price),
            sellingPrice: parse_float($model->selling_price),
            discount: parse_float($model->discount),
            minimumStock: parse_float($model->minimum_stock),
            currentStock: parse_float($model->current_stock),
            isActive: $model->is_active === 1,
        );
    }

    /**
     * Pemetaan class Model ke class DailyStockOpnameDetailEntity.
     */
    private function mapModelToDailyStockOpnameDetailEntity(DailyStockOpnameDetail $model): DailyStockOpnameDetailEntity
    {
        return new DailyStockOpnameDetailEntity(
            id: $model->id,
            dailyStockOpnameId: $model->daily_stock_opname_id,
            product: $this->mapModelToProductStockOpnameValue($model->product),
            systemStock: parse_float($model->system_stock),
            physicalStock: parse_float($model->physical_stock),
            difference: parse_float($model->difference),
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at
        );
    }

    /**
     * Pemetaan class Model ke class DailyStockOpnameWithDetailsEntity.
     */
    private function mapModelToDailyStockOpnameWithDetailsEntity(DailyStockOpname $model): DailyStockOpnameWithDetailsEntity
    {
        $details = [];

        foreach ($model->details as $detail) {
            $details[] = $this->mapModelToDailyStockOpnameDetailEntity($detail);
        }

        return new DailyStockOpnameWithDetailsEntity(
            id: $model->id,
            user: $this->mapModelToUserStockOpnameValue($model->user),
            date: parse_date_time_immutable($model->date),
            totalProduct: parse_float($model->total_product),
            totalSystemStock: parse_float($model->total_system_stock),
            totalPhysicalStock: parse_float($model->total_physical_stock),
            totalDifference: parse_float($model->total_difference),
            details: $details,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
