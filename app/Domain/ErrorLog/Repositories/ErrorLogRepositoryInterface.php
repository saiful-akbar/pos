<?php

declare(strict_types=1);

namespace App\Domain\ErrorLog\Repositories;

interface ErrorLogRepositoryInterface
{
    /**
     * Create error log
     */
    public function create(string $function, string $message): void;
}
