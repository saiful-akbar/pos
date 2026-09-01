<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use Illuminate\Database\Eloquent\Builder;

interface PurchaseRepositoryInterface
{
    /**
     * Mengambil daftar pembelidan dalam format query.
     */
    public function getQuery(): Builder;
}
