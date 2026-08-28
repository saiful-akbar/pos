<?php

declare(strict_types=1);

namespace App\Application\ErrorLog\UseCases;

use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;

class CreateErrorLog
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ErrorLogRepositoryInterface $repository) {}

    /**
     * Execute method.
     */
    public function execute(string $function, string $message): void
    {
        $this->repository->create(
            function: $function,
            message: $message,
        );
    }
}
