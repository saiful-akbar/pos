<?php

declare(strict_types=1);

namespace App\Base;

use JsonSerializable;

abstract class Entity implements JsonSerializable
{
    /**
     * Konversi data object ke array.
     */
    abstract public function toArray(): array;

    /**
     * Format data json.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
