<?php

declare(strict_types=1);

namespace App\Domain\Setting\Repositories;

use App\Application\Setting\DTOs\SettingDTO;
use App\Domain\Setting\Entities\SettingEntity;

interface SettingRepositoryInterface
{
    /**
     * Mengambil data setting.
     */
    public function find(): ?SettingEntity;

    /**
     * Create setting.
     */
    public function create(SettingDTO $dto): SettingEntity;

    /**
     * Update setting.
     */
    public function update(SettingDTO $dto): void;

    /**
     * Update logo
     */
    public function updateLogo(string $id, string $path): void;

    /**
     * Update background image
     */
    public function updateBackgroundImage(string $id, string $path): void;
}
