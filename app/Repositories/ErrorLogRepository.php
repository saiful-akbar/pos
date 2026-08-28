<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Base\Repository;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Models\ErrorLog;
use Override;

class ErrorLogRepository extends Repository implements ErrorLogRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new ErrorLog());
    }

    /**
     * Create error log
     */
    #[Override]
    public function create(string $function, string $message): void
    {
        $this->model->query()->create([
            'function' => $function,
            'message' => $message,
        ]);
    }
}
