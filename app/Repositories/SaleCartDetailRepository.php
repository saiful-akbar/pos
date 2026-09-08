<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Base\Repository;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Models\SaleCartDetail;

class SaleCartDetailRepository extends Repository implements SaleCartDetailRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new SaleCartDetail());
    }
}
