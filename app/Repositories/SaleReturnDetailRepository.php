<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleReturnDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleReturnDetailEntity;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;
use App\Models\SaleReturnDetail;
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
     * Membuat (insert) data sale return detail baru.
     */
    #[Override]
    public function create(SaleReturnDetailDTO $value): SaleReturnDetailEntity
    {
        try {
            // Simpan detail pengembalian lalu muat relasi produk dan unit untuk entity.
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['product', 'unit']);

            return SaleReturnDetailEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus (soft delete) seluruh data sale return detail berdasarkan sale_return_id.
     */
    #[Override]
    public function removeBySaleReturnId(string $saleReturnId): int
    {
        try {
            // Soft delete: hanya mengisi deleted_at.
            return $this->model
                ->newQuery()
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
}
