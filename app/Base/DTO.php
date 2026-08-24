<?php

declare(strict_types=1);

namespace App\Base;

abstract class DTO
{
    /**
     * Format data ke array.
     */
    abstract public function toArray(): array;
}
