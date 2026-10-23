<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Domain\Cash\Entities\CashTransactionEntity;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;

class FindCashTransactionById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashTransactionRepositoryInterface $cashTransactionRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $id): ?CashTransactionEntity
    {
        return $this->cashTransactionRepository->findById(id: $id);
    }
}
