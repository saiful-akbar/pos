<?php

declare(strict_types=1);

namespace App\Application\Dashboard\UseCases;

use App\Domain\Dashboard\Repositories\SaleDashboardRepositoryInterface;

class GetMonthlySalesSummary
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleDashboardRepositoryInterface $repository) {}

    /**
     * Execute method
     */
    public function execute(): array
    {
        return $this->repository->getMonthlySummary((int) date('Y'));
    }
}
