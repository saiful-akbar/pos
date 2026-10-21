<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Sale\DTOs\SaleDetailDTO;
use App\Base\Repository;
use App\Domain\Sale\Entities\SaleDetailEntity;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Models\SaleDetail;
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
     * Mencari data sale detail berdasarkan ID.
     */
    #[Override]
    public function findById(string $id): ?SaleDetailEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->with(['product', 'unit'])
                ->find($id);

            return $model !== null
                ? SaleDetailEntity::fromModel($model)
                : null;
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Membuat data sale detail baru.
     */
    #[Override]
    public function create(SaleDetailDTO $value): SaleDetailEntity
    {
        try {
            $model = $this->model
                ->newQuery()
                ->create($value->toArray());

            $model->load(['product', 'unit']);

            return SaleDetailEntity::fromModel($model);
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Menghapus data sale detail berdasarkan ID.
     */
    #[Override]
    public function deleteById(string $id): int
    {
        try {
            return $this->model
                ->newQuery()
                ->whereKey($id)
                ->delete();
        } catch (\Throwable $e) {
            $this->errorLog(
                __METHOD__,
                $e->getMessage(),
            );

            throw $e;
        }
    }
}
