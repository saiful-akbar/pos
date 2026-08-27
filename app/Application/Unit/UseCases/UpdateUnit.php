<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Application\Unit\DTOs\UpdateUnitDTO;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateUnit
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UnitRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(UpdateUnitDTO $dto): void
    {
        DB::transaction(fn () => $this->repository->update($dto));
    }
}
