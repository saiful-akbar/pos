<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Base\Repository;
use App\Domain\Module\Entities\ModuleEntity;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Domain\Module\ValueObjects\DetailModuleValue;
use App\Models\Module;
use Illuminate\Database\Query\JoinClause;
use Override;

class ModuleRepository extends Repository implements ModuleRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct(new Module());
    }

    /**
     * Mengambil daftar module berdasarkan id role.
     *
     * @return ModuleEntity[]
     */
    #[Override]
    public function getByRoleId(string $roleId): array
    {
        try {
            $columns = [
                'modules.id as id',
                'modules.header_id as header_id',
                'modules.name as name',
                'modules.icon as icon',
                'modules.path as path',
                'modules.route as route',
                'pivot.create as create',
                'pivot.read as read',
                'pivot.update as update',
                'pivot.remove as remove',
                'pivot.delete as delete',
            ];

            $details = $this->model->query()
                ->from('modules as modules')
                ->select($columns)
                ->join('role_module as pivot', function (JoinClause $join) use ($roleId): void {
                    $join->on('pivot.module_id', '=', 'modules.id')
                        ->where('pivot.role_id', '=', $roleId)
                        ->where('pivot.read', '=', 1)
                        ->whereNull('pivot.deleted_at');
                })
                ->whereNotNull('modules.header_id')
                ->orderBy('modules.name', 'asc')
                ->get();

            $modules = $this->model->query()
                ->whereNull('header_id')
                ->whereIn('id', $details->pluck('header_id')->unique())
                ->orderBy('name', 'asc')
                ->get();

            $result = [];

            foreach ($modules as $module) {
                $resultDetails = [];

                foreach ($details as $detail) {
                    if ($detail->header_id === $module->id) {
                        $resultDetails[] = $this->mapModelToDetailModuleValue($detail);
                    }
                }

                $result[] = $this->mapModelToModuleEntity($module, $resultDetails);
            }

            return $result;
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Pemetaan model ke ModuleEntity
     *
     * @param DetailModuleValue[] $details
     */
    private function mapModelToModuleEntity(Module $model, array $details): ModuleEntity
    {
        return new ModuleEntity(
            id: $model->id,
            name: $model->name,
            icon: $model->icon,
            path: $model->path,
            route: $model->route,
            details: $details,
        );
    }

    /**
     * Pemetaan model ke DetailModuleValue
     */
    private function mapModelToDetailModuleValue(Module $model): DetailModuleValue
    {
        return new DetailModuleValue(
            id: $model->id,
            name: $model->name,
            icon: $model->icon,
            path: $model->path,
            route: $model->route,
            create: $model->create === 1,
            read: $model->read === 1,
            update: $model->update === 1,
            remove: $model->remove === 1,
            delete: $model->delete === 1,
        );
    }
}
