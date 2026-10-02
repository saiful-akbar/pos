<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases;

use App\Domain\Dashboard\Repositories\SaleDashboardRepositoryInterface;
use DateTimeInterface;
use Illuminate\Support\Collection;

class GetBestSellingProduct
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleDashboardRepositoryInterface $repository) {}

    /**
     * Mengambil daftar produk terlaris.
     */
    public function execute(DateTimeInterface $fromDate, DateTimeInterface $toDate): Collection
    {
        return $this->repository->getBestSellingProduct(
            fromDate: $fromDate,
            toDate: $toDate,
        );
    }
}
