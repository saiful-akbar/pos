<?php

declare(strict_types=1);

namespace App\Application\Cash\UseCases;

use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;

class GetCashRecorders
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CashTransactionRepositoryInterface $cashTransactionRepository,
    ) {}

    /**
     * Daftar pengguna untuk filter pencatat transaksi kas.
     *
     * @return array<int, array{id: string, name: string}>
     */
    public function execute(): array
    {
        return $this->cashTransactionRepository->getRecorders();
    }
}
