<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Base\Repository;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Models\ActivityLog;
use Override;

class ActivityLogRepository extends Repository implements ActivityLogRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new ActivityLog());
    }

    /**
     * Membuat catatan akatifitas.
     */
    #[Override]
    public function create(CreateActivityDTO $dto): void
    {
        try {
            $this->model->query()->create($dto->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }
}
