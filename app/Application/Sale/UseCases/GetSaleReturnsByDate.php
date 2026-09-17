<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Entities\SaleReturnEntity;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use DateTimeInterface;
use Illuminate\Validation\ValidationException;

class GetSaleReturnsByDate
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SaleReturnRepositoryInterface $returnRepository) {}

    /**
     * Execute method
     *
     * @return SaleReturnEntity[]
     */
    public function execute(DateTimeInterface $fromDate, DateTimeInterface $toDate): array
    {
        if ($toDate < $fromDate) {
            throw ValidationException::withMessages(messages: [
                'to_date' => 'Tanggal tidak valid.',
            ]);
        }

        return $this->returnRepository->getByDate(
            fromDate: $fromDate,
            toDate: $toDate,
        );
    }
}
