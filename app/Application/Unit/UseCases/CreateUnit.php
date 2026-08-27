<?php

declare(strict_types=1);

namespace App\Application\Unit\UseCases;

use App\Application\Unit\DTOs\CreateUnitDTO;
use App\Domain\Unit\Entities\UnitEntity;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateUnit
{
    /**
     * Create a new class instance.
     */
    public function __construct(private UnitRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(CreateUnitDTO $dto): UnitEntity
    {
        return DB::transaction(fn () => $this->repository->create($dto));
    }
}
