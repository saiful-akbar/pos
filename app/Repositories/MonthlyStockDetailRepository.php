<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Stock\DTOs\MonthlyStockDetailDTO;
use App\Base\Repository;
use App\Domain\Stock\Repositories\MonthlyStockDetailRepositoryInterface;
use App\Models\MonthlyStockDetail;
use Override;

class MonthlyStockDetailRepository extends Repository implements MonthlyStockDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new MonthlyStockDetail());
    }

    /**
     * Membuat (insert) banyak data detail stok bulanan sekaligus.
     *
     * @param  array<MonthlyStockDetailDTO>  $values
     */
    #[Override]
    public function createMany(array $values): int
    {
        try {
            $created = 0;

            // Gunakan create() agar ULID dan timestamp terisi otomatis oleh model.
            foreach ($values as $value) {
                $this->model
                    ->newQuery()
                    ->create($value->toArray());

                $created++;
            }

            return $created;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
