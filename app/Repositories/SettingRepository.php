<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Base\Repository;
use App\Domain\Setting\Entities\SettingEntity;
use App\Domain\Setting\Repositories\SettingRepositoryInterface;
use App\Models\Setting;
use Override;

class SettingRepository extends Repository implements SettingRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Setting());
    }

    /**
     * Mengambil data setting.
     */
    #[Override]
    public function find(): ?SettingEntity
    {
        try {
            $result = $this->model->query()->first();

            return $result === null ? null : $this->mapModelToSettingEntity($result);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan model ke SettingEntity
     */
    private function mapModelToSettingEntity(Setting $model): SettingEntity
    {
        return new SettingEntity(
            id: $model->id,
            appName: $model->app_name,
            appVersion: $model->app_version,
            storeName: $model->store_name,
            storeAddress: $model->store_address,
            storeLogo: $model->store_logo,
            backgroundImage: $model->background_image,
        );
    }
}
