<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Module\DTOs\CreateModuleAccessDTO;
use App\Application\Module\DTOs\UpdateModuleAccessDTO;
use App\Base\Repository;
use App\Domain\Module\Entities\DetailModuleEntity;
use App\Domain\Module\Entities\ModuleEntity;
use App\Domain\Module\Repositories\ModuleAccessRepositoryInterface;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Domain\Module\ValueObjects\DetailModuleValue;
use App\Models\Module;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Override;

class ModuleRepository extends Repository implements ModuleAccessRepositoryInterface, ModuleRepositoryInterface
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
    public function getByRoleId(string $roleId, bool $isRead = false): array
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
                ->join('role_module as pivot', function (JoinClause $join) use ($roleId, $isRead): void {
                    $join->on('pivot.module_id', '=', 'modules.id')
                        ->where('pivot.role_id', '=', $roleId)
                        ->whereNull('pivot.deleted_at');

                    if ($isRead) {
                        $join->where('pivot.read', '=', 1);
                    }
                })
                ->whereNotNull('modules.header_id')
                ->get();

            $modules = $this->model->query()
                ->whereNull('header_id')
                ->whereIn('id', $details->pluck('header_id')->unique())
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
     * Mengambil semua daftar module.
     *
     * @return ModuleEntity[]
     */
    #[Override]
    public function getAll(): array
    {
        try {
            $columns = [
                'id',
                'header_id',
                'name',
                'icon',
                'path',
                'route',
                DB::raw('0 as `create`'),
                DB::raw('0 as `read`'),
                DB::raw('0 as `update`'),
                DB::raw('0 as `remove`'),
                DB::raw('0 as `delete`'),
            ];

            $details = $this->model->query()
                ->select($columns)
                ->whereNotNull('modules.header_id')
                ->get();

            $modules = $this->model->query()
                ->whereNull('header_id')
                ->whereIn('id', $details->pluck('header_id')->unique())
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
     * Mengambil semua daftar module berdasarkan role id.
     *
     * @return ModuleEntity[]
     */
    #[Override]
    public function getAllByRoleId(string $roleId): array
    {
        try {
            // Daftar kolom untuk module detail
            // dengan akses custom yang masih false.
            $columns = [
                'id',
                'name',
                'header_id',
                'icon',
                'path',
                'route',
                'created_at',
                'updated_at',
                'deleted_at',
                DB::raw('0 as `create`'),
                DB::raw('0 as `read`'),
                DB::raw('0 as `update`'),
                DB::raw('0 as `remove`'),
                DB::raw('0 as `delete`'),
            ];

            // Ambil semua daftar module detail (yang memiliki header_id)
            $moduleDetails = $this->model->query()
                ->select($columns)
                ->whereNotNull('header_id')
                ->get();

            // Ambil semua module header (yang tidak memiliki header_id)
            $moduleHeaders = $this->model->query()
                ->whereNull('header_id')
                ->get();

            // Ambil akses module (pivot) berdasarkan role_id.
            $pivots = DB::table('role_module')
                ->where('role_id', '=', $roleId)
                ->get();

            $result = [];

            foreach ($moduleHeaders as $moduleHeader) {
                $resultDetails = [];

                foreach ($moduleDetails as $moduleDetail) {
                    foreach ($pivots as $pivot) {

                        // Periksa jika role sudah memiliki akses berdasarkan module detail
                        // ubah akses custom yang sebelumnya false menjadi sesuai dengan
                        // akses yang dimiliki role tersebut.
                        if ($pivot->module_id === $moduleDetail->id) {
                            $moduleDetail->create = $pivot->create;
                            $moduleDetail->read = $pivot->read;
                            $moduleDetail->update = $pivot->update;
                            $moduleDetail->remove = $pivot->remove;
                            $moduleDetail->delete = $pivot->delete;
                        }
                    }

                    // Metakan module detail berdasarkan module headernya
                    if ($moduleHeader->id === $moduleDetail->header_id) {
                        $resultDetails[] = $this->mapModelToDetailModuleValue($moduleDetail);
                    }
                }

                $result[] = $this->mapModelToModuleEntity($moduleHeader, $resultDetails);
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
     * Mengambil daftar hanya module detail berdasarkan role id.
     *
     * @param string $roleId
     * @return DetailModuleEntity[]
     */
    #[Override]
    public function getDetailsByRoleId(string $roleId): array
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

            $modules = $this->model->query()
                ->from('modules as modules')
                ->select($columns)
                ->join('role_module as pivot', function (JoinClause $join) use ($roleId): void {
                    $join->on('pivot.module_id', '=', 'modules.id')
                        ->where('pivot.role_id', '=', $roleId)
                        ->whereNull('pivot.deleted_at');
                })
                ->whereNotNull('modules.header_id')
                ->get();

            $result = [];

            foreach ($modules as $module) {
                $result[] = $this->mapModelToDetailModuleEntity($module);
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
     * Insert data akses module (role_module)
     */
    public function createAccess(CreateModuleAccessDTO $dto): void
    {
        try {
            DB::connection(pos_connection())
                ->table('role_module')
                ->insert($dto->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update data module access (role_module)
     */
    public function updateAccess(UpdateModuleAccessDTO $dto): void
    {
        try {
            DB::connection(pos_connection())
                ->table('role_module')
                ->where('role_id', '=', $dto->roleId)
                ->where('module_id', '=', $dto->moduleId)
                ->update($dto->toArray());
        } catch (\Throwable $e) {
            $this->errorLog(
                function: __METHOD__,
                message: $e->getMessage(),
            );

            throw $e;
        }
    }

    /**
     * Update data module access (role_module)
     */
    public function deleteAccess(string $roleId, string $moduleId): void
    {
        try {
            DB::connection(pos_connection())
                ->table('role_module')
                ->where('role_id', '=', $roleId)
                ->where('module_id', '=', $moduleId)
                ->delete();
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

    /**
     * Pemetaan model ke DetailModuleEntity
     */
    private function mapModelToDetailModuleEntity(Module $model): DetailModuleEntity
    {
        return new DetailModuleEntity(
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
