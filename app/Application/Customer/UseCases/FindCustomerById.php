<?php

declare(strict_types=1);

namespace App\Application\Customer\UseCases;

use App\Domain\Customer\Entities\CustomerEntity;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;

class FindCustomerById
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    /**
     * Execute.
     */
    public function execute(string $id): ?CustomerEntity
    {
        return $this->customerRepository->findById(
            id: $id,
        );
    }
}
