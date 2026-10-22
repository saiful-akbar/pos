<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;

class FindSale
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $column, string|int|float|bool|null $value): ?SaleEntity
    {
        return $this->saleRepository->find(
            column: $column,
            value: $value,
        );
    }
}
