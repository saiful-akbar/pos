<?php

declare(strict_types=1);

namespace App\Application\Customer\UseCases;

use App\Domain\Customer\Entities\CustomerEntity;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;

class GetAllCustomers
{
    /**
     * Create a new class instance.
     */
    public function __construct(private CustomerRepositoryInterface $repository) {}

    /**
     * Execute method.
     *
     * @return CustomerEntity[]
     */
    public function execute(): array
    {
        return $this->repository->getAll();
    }
}
