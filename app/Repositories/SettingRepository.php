<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Setting\DTOs\SettingDTO;
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
     * Craete setting
     */
    #[Override]
    public function create(SettingDTO $dto): SettingEntity
    {
        try {
            return $this->mapModelToSettingEntity(
                model: $this->model->query()->create($dto->toArray()),
            );
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update setting
     */
    #[Override]
    public function update(SettingDTO $dto): void
    {
        try {
            $this->model->query()->where('id', $dto->id)->update($dto->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update logo
     */
    #[Override]
    public function updateLogo(string $id, string $path): void
    {
        try {
            $this->model->query()
                ->where('id', '=', $id)
                ->update(['store_logo' => $path]);
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update background image.
     */
    #[Override]
    public function updateBackgroundImage(string $id, string $path): void
    {
        try {
            $this->model->query()
                ->where('id', '=', $id)
                ->update(['background_image' => $path]);
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
