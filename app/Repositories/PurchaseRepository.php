<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Base\Repository;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;
use Override;

class PurchaseRepository extends Repository implements PurchaseRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Purchase());
    }

    /**
     * Mengambil daftar pembelidan dalam format query.
     */
    #[Override]
    public function getQuery(): Builder
    {
        try {
            return $this->model->query()->with(['supplier', 'user']);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
