<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Application\Category\UseCases\CreateCategory;
use App\Application\Category\UseCases\FindCategoryById;
use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Category\UseCases\GetCategoriesTable;
use App\Application\Category\UseCases\RemoveCategory;
use App\Application\Category\UseCases\UpdateCategory;
use App\Application\Customer\UseCases\CreateCustomer;
use App\Application\Customer\UseCases\FindCustomerById;
use App\Application\Customer\UseCases\GetCustomersTable;
use App\Application\Customer\UseCases\RemoveCustomer;
use App\Application\Customer\UseCases\UpdateCustomer;
use App\Application\Dashboard\UseCases\GetBestSellingProduct;
use App\Application\Dashboard\UseCases\GetMonthlySalesSummary;
use App\Application\ErrorLog\UseCases\CreateErrorLog;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Module\UseCases\GetAllModules;
use App\Application\Module\UseCases\GetAllModulesByRoleId;
use App\Application\Module\UseCases\GetModulesByRoleId;
use App\Application\Module\UseCases\UpdateModuleAccess;
use App\Application\Price\UseCases\CreatePrice;
use App\Application\Price\UseCases\DeletePrice;
use App\Application\Price\UseCases\FindPriceById;
use App\Application\Price\UseCases\GetPricesByProduct;
use App\Application\Price\UseCases\GetPricesTableByProduct;
use App\Application\Product\UseCases\CreateProduct;
use App\Application\Product\UseCases\FindProductById;
use App\Application\Product\UseCases\GetProductsTable;
use App\Application\Product\UseCases\RemoveProduct;
use App\Application\Product\UseCases\ResetAllDiscountProducts;
use App\Application\Product\UseCases\UpdateProduct;
use App\Application\Product\UseCases\UploadImageProduct;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Purchase\UseCases\DeletePurchaseCart;
use App\Application\Purchase\UseCases\DeletePurchaseCartDetail;
use App\Application\Purchase\UseCases\FindPurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\FindPurchaseCartById;
use App\Application\Purchase\UseCases\FindPurchaseCartDetailById;
use App\Application\Purchase\UseCases\GetPurchaseCartDetailsByPurchaseCartId;
use App\Application\Role\UseCases\CreateRole;
use App\Application\Role\UseCases\FindRoleById;
use App\Application\Role\UseCases\GetActiveRoles;
use App\Application\Role\UseCases\GetAllRolesTable;
use App\Application\Role\UseCases\UpdateRole;
use App\Application\Rreport\UseCases\GetMonitoringMonthlyStock;
use App\Application\Sale\UseCases\CreateSale;
use App\Application\Sale\UseCases\CreateSaleCart;
use App\Application\Sale\UseCases\CreateSaleCartDetail;
use App\Application\Sale\UseCases\CreateSaleReturn;
use App\Application\Sale\UseCases\CreateSaleReturnCart;
use App\Application\Sale\UseCases\CreateSaleReturnCartDetail;
use App\Application\Sale\UseCases\DeleteSaleCartById;
use App\Application\Sale\UseCases\DeleteSaleCartDetailById;
use App\Application\Sale\UseCases\DeleteSaleReturnCartByCurrentUser;
use App\Application\Sale\UseCases\DeleteSaleReturnCartDetailById;
use App\Application\Sale\UseCases\FindSaleById;
use App\Application\Sale\UseCases\FindSaleCartByCurrentUser;
use App\Application\Sale\UseCases\FindSaleCartById;
use App\Application\Sale\UseCases\FindSaleReturnCartByCurrentUser;
use App\Application\Sale\UseCases\GetSaleCartDetailsByCartIdTable;
use App\Application\Sale\UseCases\GetSaleDetails;
use App\Application\Sale\UseCases\GetSaleDetailsTable;
use App\Application\Sale\UseCases\GetSaleReturn;
use App\Application\Sale\UseCases\GetSaleReturnCartDetailsTable;
use App\Application\Sale\UseCases\GetSaleReturnDetail;
use App\Application\Sale\UseCases\GetSaleReturnsByDate;
use App\Application\Sale\UseCases\GetSales;
use App\Application\Sale\UseCases\RemoveSale;
use App\Application\Sale\UseCases\RemoveSaleReturn;
use App\Application\Sale\UseCases\UpdateQuantityReturnCartDetail;
use App\Application\Sale\UseCases\UpdateQuantitySalesCartDetailById;
use App\Application\Setting\UseCases\FindSetting;
use App\Application\Setting\UseCases\UpdateSetting;
use App\Application\StockOpname\UseCases\CreateDailyStockOpnameDetail;
use App\Application\StockOpname\UseCases\CreateMonthlyStockOpname;
use App\Application\StockOpname\UseCases\DeleteDailyStockOpname;
use App\Application\StockOpname\UseCases\DeleteDailyStockOpnameDetail;
use App\Application\StockOpname\UseCases\ExistsDailyStockOpnameById;
use App\Application\StockOpname\UseCases\FindDailyStockOpnameByDate;
use App\Application\StockOpname\UseCases\FindDailyStockOpnameById;
use App\Application\StockOpname\UseCases\FindMonthlyStockByYearAndMonth;
use App\Application\StockOpname\UseCases\GetDailyStockOpnameByDate;
use App\Application\StockOpname\UseCases\GetDailyStockOpnameDetailByHeaderId;
use App\Application\StockOpname\UseCases\GetMonthlyStockDetails;
use App\Application\StockOpname\UseCases\RemoveDailyStockOpname;
use App\Application\StockOpname\UseCases\SetActiveStock;
use App\Application\StockOpname\UseCases\UpdateMonthlyStock;
use App\Application\StockOpname\UseCases\UpdateQuantityDailyStockOpnameDetail;
use App\Application\Supplier\UseCases\CreateSupplier;
use App\Application\Supplier\UseCases\FindSupplierById;
use App\Application\Supplier\UseCases\GetSupplierTable;
use App\Application\Supplier\UseCases\RemoveSupplier;
use App\Application\Supplier\UseCases\UpdateSupplier;
use App\Application\Unit\UseCases\CreateUnit;
use App\Application\Unit\UseCases\FindUnitById;
use App\Application\Unit\UseCases\GetAllUnits;
use App\Application\Unit\UseCases\GetUnitsTable;
use App\Application\Unit\UseCases\RemoveUnit;
use App\Application\Unit\UseCases\UpdateUnit;
use App\Application\User\UseCases\CreateUser;
use App\Application\User\UseCases\FindUserById;
use App\Application\User\UseCases\GetUsersTable;
use App\Application\User\UseCases\RemoveUser;
use App\Application\User\UseCases\UpdateUser;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Dashboard\Repositories\SaleDashboardRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Module\Repositories\ModuleAccessRepositoryInterface;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Report\Repositories\ReportRepositoryInterface;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnRepositoryInterface;
use App\Domain\Setting\Repositories\SettingRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\DailyStockOpnameRepositoryInterface;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameDetailRepositoryInterface;
use App\Domain\StockOpname\Repositories\MonthlyStockOpnameRepositoryInterface;
use App\Domain\StockOpname\Repositories\StockMovementRepositoryInterface;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Repositories\ActivityLogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DailyStockOpnameDetailRepository;
use App\Repositories\DailyStockOpnameRepository;
use App\Repositories\ErrorLogRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\MonthlyStockOpnameDetailRepository;
use App\Repositories\MonthlyStockOpnameRepository;
use App\Repositories\PriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseCartDetailRepository;
use App\Repositories\PurchaseCartRepository;
use App\Repositories\ReportRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SaleCartDetailRepository;
use App\Repositories\SaleCartRepository;
use App\Repositories\SaleDetailRepository;
use App\Repositories\SaleRepository;
use App\Repositories\SaleReturnCartDetailRepository;
use App\Repositories\SaleReturnCartRepository;
use App\Repositories\SaleReturnDetailRepository;
use App\Repositories\SaleReturnRepository;
use App\Repositories\SettingRepository;
use App\Repositories\StockMovementRepository;
use App\Repositories\SupplierRepository;
use App\Repositories\UnitRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Repository bindings
     */
    private array $repositories = [
        // Error log
        ErrorLogRepositoryInterface::class => [
            CreateErrorLog::class => ErrorLogRepository::class,
            CreateCategory::class => ErrorLogRepository::class,
            UpdateCategory::class => ErrorLogRepository::class,
            CreateUnit::class => ErrorLogRepository::class,
            UpdateUnit::class => ErrorLogRepository::class,
            CreateCustomer::class => ErrorLogRepository::class,
            UpdateCustomer::class => ErrorLogRepository::class,
            GetCustomersTable::class => ErrorLogRepository::class,
            FindCustomerById::class => ErrorLogRepository::class,
            UpdateSetting::class => ErrorLogRepository::class,
            CreateSaleCart::class => ErrorLogRepository::class,
            DeleteSaleCartById::class => ErrorLogRepository::class,
            CreateSaleCartDetail::class => ErrorLogRepository::class,
            DeleteSaleCartDetailById::class => ErrorLogRepository::class,
            UpdateQuantitySalesCartDetailById::class => ErrorLogRepository::class,
            CreateSale::class => ErrorLogRepository::class,
            CreateSaleReturnCart::class => ErrorLogRepository::class,
            DeleteSaleReturnCartByCurrentUser::class => ErrorLogRepository::class,
            CreateSaleReturnCartDetail::class => ErrorLogRepository::class,
            DeleteSaleReturnCartDetailById::class => ErrorLogRepository::class,
            UpdateQuantityReturnCartDetail::class => ErrorLogRepository::class,
            CreateSaleReturn::class => ErrorLogRepository::class,
            CreateDailyStockOpnameDetail::class => ErrorLogRepository::class,
            DeleteDailyStockOpnameDetail::class => ErrorLogRepository::class,
            UpdateQuantityDailyStockOpnameDetail::class => ErrorLogRepository::class,
            SetActiveStock::class => ErrorLogRepository::class,
            CreateMonthlyStockOpname::class => ErrorLogRepository::class,
            UpdateMonthlyStock::class => ErrorLogRepository::class,
            RemoveUser::class => ErrorLogRepository::class,
            RemoveCustomer::class => ErrorLogRepository::class,
            RemoveCategory::class => ErrorLogRepository::class,
            RemoveUnit::class => ErrorLogRepository::class,
            RemoveSale::class => ErrorLogRepository::class,
            RemoveSaleReturn::class => ErrorLogRepository::class,
            RemoveDailyStockOpname::class => ErrorLogRepository::class,
            DeleteDailyStockOpname::class => ErrorLogRepository::class,
            CreateSupplier::class => ErrorLogRepository::class,
            UpdateSupplier::class => ErrorLogRepository::class,
            RemoveSupplier::class => ErrorLogRepository::class,
            CreateProduct::class => ErrorLogRepository::class,
            CreatePrice::class => ErrorLogRepository::class,
            DeletePrice::class => ErrorLogRepository::class,
            FindPriceById::class => ErrorLogRepository::class,
            UploadImageProduct::class => ErrorLogRepository::class,
            UpdateProduct::class => ErrorLogRepository::class,
            ResetAllDiscountProducts::class => ErrorLogRepository::class,
            RemoveProduct::class => ErrorLogRepository::class,
            CreatePurchaseCart::class => ErrorLogRepository::class,
            DeletePurchaseCart::class => ErrorLogRepository::class,
            CreatePurchaseCartDetail::class => ErrorLogRepository::class,
        ],

        // Activity log
        ActivityLogRepositoryInterface::class => [
            CreateActivity::class => ActivityLogRepository::class,
            UpdateRole::class => ActivityLogRepository::class,
            CreateUser::class => ActivityLogRepository::class,
            UpdateUser::class => ActivityLogRepository::class,
            CreateCategory::class => ActivityLogRepository::class,
            UpdateCategory::class => ActivityLogRepository::class,
            CreateUnit::class => ActivityLogRepository::class,
            UpdateUnit::class => ActivityLogRepository::class,
            CreateCustomer::class => ActivityLogRepository::class,
            UpdateCustomer::class => ActivityLogRepository::class,
            GetCustomersTable::class => ActivityLogRepository::class,
            FindCustomerById::class => ActivityLogRepository::class,
            UpdateSetting::class => ActivityLogRepository::class,
            CreateSaleCart::class => ActivityLogRepository::class,
            DeleteSaleCartById::class => ActivityLogRepository::class,
            CreateSale::class => ActivityLogRepository::class,
            CreateSaleReturnCart::class => ActivityLogRepository::class,
            DeleteSaleReturnCartByCurrentUser::class => ActivityLogRepository::class,
            CreateSaleReturn::class => ActivityLogRepository::class,
            CreateDailyStockOpnameDetail::class => ActivityLogRepository::class,
            DeleteDailyStockOpnameDetail::class => ActivityLogRepository::class,
            UpdateQuantityDailyStockOpnameDetail::class => ActivityLogRepository::class,
            SetActiveStock::class => ActivityLogRepository::class,
            CreateMonthlyStockOpname::class => ActivityLogRepository::class,
            UpdateMonthlyStock::class => ActivityLogRepository::class,
            RemoveUser::class => ActivityLogRepository::class,
            RemoveCustomer::class => ActivityLogRepository::class,
            RemoveCategory::class => ActivityLogRepository::class,
            RemoveUnit::class => ActivityLogRepository::class,
            RemoveSale::class => ActivityLogRepository::class,
            RemoveSaleReturn::class => ActivityLogRepository::class,
            RemoveDailyStockOpname::class => ActivityLogRepository::class,
            DeleteDailyStockOpname::class => ActivityLogRepository::class,
            CreateSupplier::class => ActivityLogRepository::class,
            UpdateSupplier::class => ActivityLogRepository::class,
            RemoveSupplier::class => ActivityLogRepository::class,
            CreateProduct::class => ActivityLogRepository::class,
            CreatePrice::class => ActivityLogRepository::class,
            DeletePrice::class => ActivityLogRepository::class,
            FindPriceById::class => ActivityLogRepository::class,
            UpdateProduct::class => ActivityLogRepository::class,
            ResetAllDiscountProducts::class => ActivityLogRepository::class,
            RemoveProduct::class => ActivityLogRepository::class,
            CreatePurchaseCart::class => ActivityLogRepository::class,
            DeletePurchaseCart::class => ActivityLogRepository::class,
        ],

        // Setting
        SettingRepositoryInterface::class => [
            FindSetting::class => SettingRepository::class,
            UpdateSetting::class => SettingRepository::class,
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
            RemoveUser::class => UserRepository::class,
        ],

        // Category
        CategoryRepositoryInterface::class => [
            GetCategoriesTable::class => CategoryRepository::class,
            FindCategoryById::class => CategoryRepository::class,
            CreateCategory::class => CategoryRepository::class,
            UpdateCategory::class => CategoryRepository::class,
            GetAllCategories::class => CategoryRepository::class,
            RemoveCategory::class => CategoryRepository::class,
        ],

        // Unit
        UnitRepositoryInterface::class => [
            GetUnitsTable::class => UnitRepository::class,
            FindUnitById::class => UnitRepository::class,
            CreateUnit::class => UnitRepository::class,
            UpdateUnit::class => UnitRepository::class,
            RemoveUnit::class => UnitRepository::class,
            GetAllUnits::class => UnitRepository::class,
        ],

        // Product
        ProductRepositoryInterface::class => [
            GetProductsTable::class => ProductRepository::class,
            CreateProduct::class => ProductRepository::class,
            FindProductById::class => ProductRepository::class,
            UploadImageProduct::class => ProductRepository::class,
            UpdateProduct::class => ProductRepository::class,
            ResetAllDiscountProducts::class => ProductRepository::class,
            RemoveProduct::class => ProductRepository::class,
            CreatePurchaseCartDetail::class => ProductRepository::class,
        ],

        // Product Price
        PriceRepositoryInterface::class => [
            GetPricesByProduct::class => PriceRepository::class,
            GetPricesTableByProduct::class => PriceRepository::class,
            CreatePrice::class => PriceRepository::class,
            DeletePrice::class => PriceRepository::class,
            FindPriceById::class => PriceRepository::class,
            CreatePurchaseCartDetail::class => PriceRepository::class,
        ],

        // Customer
        CustomerRepositoryInterface::class => [
            GetCustomersTable::class => CustomerRepository::class,
            FindCustomerById::class => CustomerRepository::class,
            CreateCustomer::class => CustomerRepository::class,
            UpdateCustomer::class => CustomerRepository::class,
            RemoveCustomer::class => CustomerRepository::class,
            CreateSale::class => CustomerRepository::class,
        ],

        // Supplier
        SupplierRepositoryInterface::class => [
            GetSupplierTable::class => SupplierRepository::class,
            CreateSupplier::class => SupplierRepository::class,
            UpdateSupplier::class => SupplierRepository::class,
            RemoveSupplier::class => SupplierRepository::class,
            FindSupplierById::class => SupplierRepository::class,
        ],

        // Stock movement
        StockMovementRepositoryInterface::class => [
            CreateSale::class => StockMovementRepository::class,
            CreateSaleReturn::class => StockMovementRepository::class,
        ],

        // Sale cart
        SaleCartRepositoryInterface::class => [
            FindSaleCartByCurrentUser::class => SaleCartRepository::class,
            CreateSaleCart::class => SaleCartRepository::class,
            DeleteSaleCartById::class => SaleCartRepository::class,
            FindSaleCartById::class => SaleCartRepository::class,
            CreateSaleCartDetail::class => SaleCartRepository::class,
            DeleteSaleCartDetailById::class => SaleCartRepository::class,
            UpdateQuantitySalesCartDetailById::class => SaleCartRepository::class,
            CreateSale::class => SaleCartRepository::class,
        ],

        // Sale cart detail
        SaleCartDetailRepositoryInterface::class => [
            CreateSaleCartDetail::class => SaleCartDetailRepository::class,
            GetSaleCartDetailsByCartIdTable::class => SaleCartDetailRepository::class,
            DeleteSaleCartDetailById::class => SaleCartDetailRepository::class,
            UpdateQuantitySalesCartDetailById::class => SaleCartDetailRepository::class,
            CreateSale::class => SaleCartDetailRepository::class,
        ],

        // Sale
        SaleRepositoryInterface::class => [
            CreateSale::class => SaleRepository::class,
            FindSaleById::class => SaleRepository::class,
            CreateSaleReturnCart::class => SaleRepository::class,
            UpdateQuantityReturnCartDetail::class => SaleRepository::class,
            GetSales::class => SaleRepository::class,
            RemoveSale::class => SaleRepository::class,
        ],

        // Sale detail
        SaleDetailRepositoryInterface::class => [
            CreateSale::class => SaleDetailRepository::class,
            GetSaleDetailsTable::class => SaleDetailRepository::class,
            CreateSaleReturnCartDetail::class => SaleDetailRepository::class,
            UpdateQuantityReturnCartDetail::class => SaleDetailRepository::class,
            GetSaleDetails::class => SaleDetailRepository::class,
            RemoveSale::class => SaleDetailRepository::class,
        ],

        // Sale return cart
        SaleReturnCartRepositoryInterface::class => [
            CreateSaleReturnCart::class => SaleReturnCartRepository::class,
            FindSaleReturnCartByCurrentUser::class => SaleReturnCartRepository::class,
            DeleteSaleReturnCartByCurrentUser::class => SaleReturnCartRepository::class,
            CreateSaleReturnCartDetail::class => SaleReturnCartRepository::class,
            GetSaleReturnCartDetailsTable::class => SaleReturnCartRepository::class,
            DeleteSaleReturnCartDetailById::class => SaleReturnCartRepository::class,
            UpdateQuantityReturnCartDetail::class => SaleReturnCartRepository::class,
            CreateSaleReturn::class => SaleReturnCartRepository::class,
        ],

        // Sale return cart detail
        SaleReturnCartDetailRepositoryInterface::class => [
            CreateSaleReturnCartDetail::class => SaleReturnCartDetailRepository::class,
            GetSaleReturnCartDetailsTable::class => SaleReturnCartDetailRepository::class,
            DeleteSaleReturnCartDetailById::class => SaleReturnCartDetailRepository::class,
            UpdateQuantityReturnCartDetail::class => SaleReturnCartDetailRepository::class,
            CreateSaleReturn::class => SaleReturnCartDetailRepository::class,
        ],

        // Sale return
        SaleReturnRepositoryInterface::class => [
            CreateSaleReturn::class => SaleReturnRepository::class,
            GetSaleReturnsByDate::class => SaleReturnRepository::class,
            GetSaleReturn::class => SaleReturnRepository::class,
            RemoveSaleReturn::class => SaleReturnRepository::class,
        ],

        // Sale return Detail
        SaleReturnDetailRepositoryInterface::class => [
            CreateSaleReturn::class => SaleReturnDetailRepository::class,
            GetSaleReturnDetail::class => SaleReturnDetailRepository::class,
            RemoveSaleReturn::class => SaleReturnDetailRepository::class,
        ],

        // Daily stock opname
        DailyStockOpnameRepositoryInterface::class => [
            GetDailyStockOpnameByDate::class => DailyStockOpnameRepository::class,
            FindDailyStockOpnameById::class => DailyStockOpnameRepository::class,
            FindDailyStockOpnameByDate::class => DailyStockOpnameRepository::class,
            ExistsDailyStockOpnameById::class => DailyStockOpnameRepository::class,
            CreateDailyStockOpnameDetail::class => DailyStockOpnameRepository::class,
            DeleteDailyStockOpnameDetail::class => DailyStockOpnameRepository::class,
            UpdateQuantityDailyStockOpnameDetail::class => DailyStockOpnameRepository::class,
            SetActiveStock::class => DailyStockOpnameRepository::class,
            UpdateMonthlyStock::class => DailyStockOpnameRepository::class,
            RemoveDailyStockOpname::class => DailyStockOpnameRepository::class,
            DeleteDailyStockOpname::class => DailyStockOpnameRepository::class,
        ],

        // Daily stock opname detail
        DailyStockOpnameDetailRepositoryInterface::class => [
            GetDailyStockOpnameDetailByHeaderId::class => DailyStockOpnameDetailRepository::class,
            CreateDailyStockOpnameDetail::class => DailyStockOpnameDetailRepository::class,
            DeleteDailyStockOpnameDetail::class => DailyStockOpnameDetailRepository::class,
            UpdateQuantityDailyStockOpnameDetail::class => DailyStockOpnameDetailRepository::class,
            SetActiveStock::class => DailyStockOpnameDetailRepository::class,
            CreateMonthlyStockOpname::class => DailyStockOpnameDetailRepository::class,
            UpdateMonthlyStock::class => DailyStockOpnameDetailRepository::class,
            RemoveDailyStockOpname::class => DailyStockOpnameDetailRepository::class,
            DeleteDailyStockOpname::class => DailyStockOpnameDetailRepository::class,
        ],

        // Monthly stock opname
        MonthlyStockOpnameRepositoryInterface::class => [
            CreateMonthlyStockOpname::class => MonthlyStockOpnameRepository::class,
            FindMonthlyStockByYearAndMonth::class => MonthlyStockOpnameRepository::class,
            UpdateMonthlyStock::class => MonthlyStockOpnameRepository::class,
            DeleteDailyStockOpname::class => MonthlyStockOpnameRepository::class,
        ],

        // Monthly stock opname detail
        MonthlyStockOpnameDetailRepositoryInterface::class => [
            CreateMonthlyStockOpname::class => MonthlyStockOpnameDetailRepository::class,
            GetMonthlyStockDetails::class => MonthlyStockOpnameDetailRepository::class,
            UpdateMonthlyStock::class => MonthlyStockOpnameDetailRepository::class,
        ],

        // Report
        ReportRepositoryInterface::class => [
            GetMonitoringMonthlyStock::class => ReportRepository::class,
        ],

        // Dashboard
        SaleDashboardRepositoryInterface::class => [
            GetMonthlySalesSummary::class => SaleRepository::class,
            GetBestSellingProduct::class => SaleRepository::class,
        ],

        // Purchase cart
        PurchaseCartRepositoryInterface::class => [
            FindPurchaseCartByCurrentUser::class => PurchaseCartRepository::class,
            FindPurchaseCartById::class => PurchaseCartRepository::class,
            CreatePurchaseCart::class => PurchaseCartRepository::class,
            DeletePurchaseCart::class => PurchaseCartRepository::class,
            CreatePurchaseCartDetail::class => PurchaseCartRepository::class,
            DeletePurchaseCartDetail::class => PurchaseCartRepository::class,
        ],

        // Purchase cart detail
        PurchaseCartDetailRepositoryInterface::class => [
            CreatePurchaseCartDetail::class => PurchaseCartDetailRepository::class,
            GetPurchaseCartDetailsByPurchaseCartId::class => PurchaseCartDetailRepository::class,
            DeletePurchaseCartDetail::class => PurchaseCartDetailRepository::class,
            FindPurchaseCartDetailById::class => PurchaseCartDetailRepository::class,
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
