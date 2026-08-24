<?php

declare(strict_types=1);

namespace App\Application\Setting\UseCases;

use App\Domain\Setting\Entities\SettingEntity;
use App\Domain\Setting\Repositories\SettingRepositoryInterface;

class FindSetting
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SettingRepositoryInterface $repository) {}

    /**
     * Execute method.
     *
     * @return SettingEntity|null
     */
    public function execute(): ?SettingEntity
    {
        return $this->repository->find();
    }
}
