<?php

declare(strict_types=1);

namespace App\Domain\Setting\Repositories;

use App\Domain\Setting\Entities\SettingEntity;

interface SettingRepositoryInterface
{
    /**
     * Mengambil data setting.
     */
    public function find(): ?SettingEntity;
}
