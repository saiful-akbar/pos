<?php

declare(strict_types=1);

namespace App\Application\Customer\UseCases;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class GetCustomersTable
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {}

    /**
     * Execute method
     */
    public function execute(): JsonResponse
    {
        return DataTables::eloquent($this->customerRepository->getQuery())->toJson();
    }
}
