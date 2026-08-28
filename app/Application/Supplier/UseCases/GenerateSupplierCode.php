<?php

declare(strict_types=1);

namespace App\Application\Supplier\UseCases;

use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use DateTimeImmutable;

class GenerateSupplierCode
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SupplierRepositoryInterface $supplierRepository) {}

    /**
     * Execute method.
     */
    public function execute(): string
    {
        $maxCode = $this->supplierRepository->findMaxCode();
        $now = new DateTimeImmutable();
        $year = $now->format('y');

        if ($maxCode === null) {
            return "SPL{$year}-0001";
        }

        $codeNumber = parse_int(ltrim(trim($maxCode), "SPL{$year}-"));
        $number = $codeNumber + 1;

        if ($number < 10) {
            return "SPL{$year}-000{$number}";
        } elseif ($number < 100) {
            return "SPL{$year}-00{$number}";
        } elseif ($number < 1000) {
            return "SPL{$year}-0{$number}";
        } else {
            return "SPL{$year}-{$number}";
        }
    }
}
