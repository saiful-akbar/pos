<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use DateTimeInterface;
use Illuminate\Validation\ValidationException;

class GetSalesByDate
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleRepositoryInterface $saleRepository) {}

    /**
     * Execute method
     *
     * @return SaleEntity[]
     */
    public function execute(DateTimeInterface $fromDate, DateTimeInterface $toDate): array
    {
        if ($toDate < $fromDate) {
            throw ValidationException::withMessages(messages: [
                'to_date' => 'Tanggal tidak valid.',
            ]);
        }

        return $this->saleRepository->getByDate(
            fromDate: $fromDate,
            toDate: $toDate,
        );
    }
}
