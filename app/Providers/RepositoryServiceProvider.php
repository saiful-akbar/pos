<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Category\UseCases\CreateCategory;
use App\Application\Category\UseCases\FindCategoryById;
use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Category\UseCases\GetCategoriesTable;
use App\Application\Category\UseCases\UpdateCategory;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Module\UseCases\GetAllModules;
use App\Application\Module\UseCases\GetAllModulesByRoleId;
use App\Application\Module\UseCases\GetModulesByRoleId;
use App\Application\Module\UseCases\UpdateModuleAccess;
use App\Application\Product\UseCases\CreateProduct;
use App\Application\Product\UseCases\FindProductById;
use App\Application\Product\UseCases\GetProductsTable;
use App\Application\Product\UseCases\UpdateProduct;
use App\Application\Role\UseCases\CreateRole;
use App\Application\Role\UseCases\FindRoleById;
use App\Application\Role\UseCases\GetActiveRoles;
use App\Application\Role\UseCases\GetAllRolesTable;
use App\Application\Role\UseCases\UpdateRole;
use App\Application\Setting\UseCases\FindSetting;
use App\Application\Unit\UseCases\CreateUnit;
use App\Application\Unit\UseCases\FindUnitById;
use App\Application\Unit\UseCases\GetAllUnits;
use App\Application\Unit\UseCases\GetUnitsTable;
use App\Application\Unit\UseCases\UpdateUnit;
use App\Application\User\UseCases\CreateUser;
use App\Application\User\UseCases\FindUserById;
use App\Application\User\UseCases\GetUsersTable;
use App\Application\User\UseCases\UpdateUser;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Module\Repositories\ModuleAccessRepositoryInterface;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Domain\Setting\Repositories\SettingRepositoryInterface;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UnitRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository bindings
     */
    private array $repositories = [
        SettingRepositoryInterface::class => [
            FindSetting::class => SettingRepository::class,
        ],

        // Module
        ModuleRepositoryInterface::class => [
            GetModulesByRoleId::class => ModuleRepository::class,
            GetAllModules::class => ModuleRepository::class,
            GetAllModulesByRoleId::class => ModuleRepository::class,
            UpdateModuleAccess::class => ModuleRepository::class,
        ],

        // Module access (role_module)
        ModuleAccessRepositoryInterface::class => [
            UpdateModuleAccess::class => ModuleRepository::class,
            FindModuleAccessByRoute::class => ModuleRepository::class,
        ],

        // Role
        RoleRepositoryInterface::class => [
            GetAllRolesTable::class => RoleRepository::class,
            CreateRole::class => RoleRepository::class,
            FindRoleById::class => RoleRepository::class,
            UpdateRole::class => RoleRepository::class,
            GetActiveRoles::class => RoleRepository::class,
        ],

        // User
        UserRepositoryInterface::class => [
            GetUsersTable::class => UserRepository::class,
            FindUserById::class => UserRepository::class,
            CreateUser::class => UserRepository::class,
            UpdateUser::class => UserRepository::class,
        ],

        // Category
        CategoryRepositoryInterface::class => [
            GetCategoriesTable::class => CategoryRepository::class,
            FindCategoryById::class => CategoryRepository::class,
            CreateCategory::class => CategoryRepository::class,
            UpdateCategory::class => CategoryRepository::class,
            GetAllCategories::class => CategoryRepository::class,
        ],

        // Unit
        UnitRepositoryInterface::class => [
            GetUnitsTable::class => UnitRepository::class,
            FindUnitById::class => UnitRepository::class,
            CreateUnit::class => UnitRepository::class,
            UpdateUnit::class => UnitRepository::class,
            GetAllUnits::class => UnitRepository::class,
        ],

        // Product
        ProductRepositoryInterface::class => [
            GetProductsTable::class => ProductRepository::class,
            FindProductById::class => ProductRepository::class,
            CreateProduct::class => ProductRepository::class,
            UpdateProduct::class => ProductRepository::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        foreach ($this->repositories as $interface => $services) {
            foreach ($services as $service => $repository) {
                $this->app->when($service)->needs($interface)->give($repository);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
