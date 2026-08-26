<?php

declare(strict_types=1);

namespace App\Application\Module\UseCases;

use App\Application\Module\DTOs\CreateModuleAccessDTO;
use App\Application\Module\DTOs\UpdateModuleAccessDTO;
use App\Domain\Module\Repositories\ModuleAccessRepositoryInterface;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use Illuminate\Support\Collection;

class UpdateModuleAccess
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ModuleRepositoryInterface $moduleRepository,
        private ModuleAccessRepositoryInterface $accessRepository,
    ) {}

    /**
     * Execute method.
     */
    public function execute(string $roleId, array $modules): void
    {
        $now = now();
        $currentModules = $this->getCurrentModules($roleId);
        $newModules = $this->getNewModules($modules);

        $currentModuleIds = $currentModules->pluck('id');
        $newModuleIds = $newModules->pluck('id');

        // Mengambil data module yang sama diantara module baru
        // dengan module yang ada saat ini untuk diupdate.
        $boths = $newModules
            ->whereIn('id', $currentModuleIds)
            ->values();

        if ($boths->count() > 0) {
            foreach ($boths as $both) {
                $this->accessRepository->updateAccess(
                    dto: new UpdateModuleAccessDTO(
                        roleId: $roleId,
                        moduleId: $both['id'],
                        read: $both['read'],
                        create: $both['create'],
                        update: $both['update'],
                        remove: $both['remove'],
                        delete: $both['delete'],
                    ),
                );
            }
        }

        // Mengambil data yang ada pada module baru tetapi tidak
        // ada pada module saat ini untuk di insert baru
        $onlyNewModules = $newModules
            ->whereNotIn('id', $currentModuleIds)
            ->values();

        if ($onlyNewModules->count() > 0) {
            foreach ($onlyNewModules as $newModule) {
                $this->accessRepository->createAccess(
                    dto: new CreateModuleAccessDTO(
                        id: ulid(),
                        roleId: $roleId,
                        moduleId: $newModule['id'],
                        read: $newModule['read'],
                        create: $newModule['create'],
                        update: $newModule['update'],
                        remove: $newModule['remove'],
                        delete: $newModule['delete'],
                        createdAt: $now,
                        updatedAt: $now,
                        deletedAt: null,
                    ),
                );
            }
        }

        // Mengambil data yang ada pada module saat ini
        // tetapi tidak ada pada module baru untuk dihapus
        $onlyCurrentModules = $currentModules
            ->whereNotIn('id', $newModuleIds)
            ->values();

        if ($onlyCurrentModules->count() > 0) {
            foreach ($onlyCurrentModules as $currentModule) {
                $this->accessRepository->deleteAccess(
                    roleId: $roleId,
                    moduleId: $currentModule['id'],
                );
            }
        }
    }

    /**
     * Mengambil daftar akses modul saat ini.
     */
    private function getCurrentModules(string $roleId): Collection
    {
        $modules = $this->moduleRepository->getDetailsByRoleId(roleId: $roleId);
        $result = [];

        foreach ($modules as $module) {
            $result[] = [
                'id' => $module->id,
                'read' => $module->read,
                'create' => $module->create,
                'update' => $module->update,
                'remove' => $module->remove,
                'delete' => $module->delete,
            ];
        }

        return collect($result);
    }

    /**
     * Mengambil daftar module baru.
     */
    private function getNewModules(array $modules): Collection
    {
        $result = [];

        foreach ($modules as $id => $module) {
            $result[] = [
                'id' => $id,
                'read' => parse_bool($module['read'] ?? false),
                'create' => parse_bool($module['create'] ?? false),
                'update' => parse_bool($module['update'] ?? false),
                'remove' => parse_bool($module['remove'] ?? false),
                'delete' => parse_bool($module['delete'] ?? false),
            ];
        }

        return collect($result);
    }
}
