<?php

declare(strict_types=1);

namespace App\Application\Setting\UseCases;

use App\Application\ActivityLog\DTOs\CreateActivityDTO;
use App\Application\Setting\DTOs\SettingDTO;
use App\Domain\ActivityLog\Enums\EventActivityEnum;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Setting\Entities\SettingEntity;
use App\Domain\Setting\Repositories\SettingRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateSetting
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SettingRepositoryInterface $settingRepository,
        private ActivityLogRepositoryInterface $activityRepository,
        private ErrorLogRepositoryInterface $errorRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(Request $request): void
    {
        DB::connection(pos_connection())->beginTransaction();

        try {
            $setting = $this->settingRepository->find();

            if ($setting === null) {
                $setting = $this->settingRepository->create(dto: new SettingDTO(
                    appName: $request->validated('app_name'),
                    storeName: $request->validated('store_name'),
                    storeAddress: $request->validated('store_address'),
                ));
            } else {
                $this->settingRepository->update(dto: new SettingDTO(
                    id: $setting->id,
                    appName: $request->validated('app_name'),
                    storeName: $request->validated('store_name'),
                    storeAddress: $request->validated('store_address'),
                ));
            }

            $this->updateLogo(setting: $setting, request: $request);
            $this->updateBackgroundImage(setting: $setting, request: $request);

            $this->activityRepository->create(dto: new CreateActivityDTO(
                userId: user()->id,
                logName: 'Memperbarui data pengaturan',
                event: EventActivityEnum::UPDATED,
            ));

            DB::connection(pos_connection())->commit();
        } catch (\Throwable $e) {
            DB::connection(pos_connection())->rollBack();

            $this->errorRepository->create(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update logo
     *
     * @param SettingEntity|null $setting
     */
    private function updateLogo(SettingEntity $setting, Request $request): void
    {
        $path = $setting->storeLogo;

        if ($request->hasFile('store_logo')) {
            if ($path !== null && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $logo = $request->file('store_logo')->store('images/settings', 'public');

            $this->settingRepository->updateLogo(
                id: $setting->id,
                path: $logo,
            );
        }
    }

    /**
     * Update image background
     *
     * @param SettingEntity|null $setting
     */
    private function updateBackgroundImage(SettingEntity $setting, Request $request): void
    {
        $path = $setting->backgroundImage;

        if ($request->hasFile('background_image')) {
            if ($path !== null && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $backgroundImage = $request->file('background_image')->store('images/settings', 'public');

            $this->settingRepository->updateBackgroundImage(
                id: $setting->id,
                path: $backgroundImage,
            );
        }
    }
}
