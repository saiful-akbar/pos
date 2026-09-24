<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Application\Category\UseCases\CreateCategory;
use App\Application\Category\UseCases\FindCategoryById;
use App\Application\Category\UseCases\GetAllCategories;
use App\Application\Category\UseCases\GetCategoriesTable;
use App\Application\Category\UseCases\UpdateCategory;
use App\Application\Customer\UseCases\CreateCustomer;
use App\Application\Customer\UseCases\FindCustomerById;
use App\Application\Customer\UseCases\GetAllCustomers;
use App\Application\Customer\UseCases\GetCustomersTable;
use App\Application\Customer\UseCases\UpdateCustomer;
use App\Application\Discount\UseCases\CreateDiscount;
use App\Application\Discount\UseCases\FindDiscountById;
use App\Application\Discount\UseCases\GetActiveDiscountsTable;
use App\Application\Discount\UseCases\GetAllDiscounts;
use App\Application\Discount\UseCases\GetAllDiscountsTable;
use App\Application\Discount\UseCases\RemoveDiscountById;
use App\Application\Discount\UseCases\UpdateDiscount;
use App\Application\ErrorLog\UseCases\CreateErrorLog;
use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Module\UseCases\GetAllModules;
use App\Application\Module\UseCases\GetAllModulesByRoleId;
use App\Application\Module\UseCases\GetModulesByRoleId;
use App\Application\Module\UseCases\UpdateModuleAccess;
use App\Application\Product\UseCases\CreateProduct;
use App\Application\Product\UseCases\FindProductById;
use App\Application\Product\UseCases\GetActiveProductsTable;
use App\Application\Product\UseCases\GetProductsTable;
use App\Application\Product\UseCases\ResetDiscountProduct;
use App\Application\Product\UseCases\UpdateProduct;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Purchase\UseCases\CreatePurchasePayment;
use App\Application\Purchase\UseCases\DeletePurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\DeletePurchaseCartDetail;
use App\Application\Purchase\UseCases\DeletePurchasePaymentById;
use App\Application\Purchase\UseCases\FindPurchaseById;
use App\Application\Purchase\UseCases\FindPurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\GetPurchaseByDateRangeTable;
use App\Application\Purchase\UseCases\GetPurchaseCartDetailByCartId;
use App\Application\Purchase\UseCases\GetPurchaseDetailByPurchaseId;
use App\Application\Purchase\UseCases\GetPurchaseDetailByPurchaseIdTable;
use App\Application\Purchase\UseCases\GetPurchasePaymentByPurchaseId;
use App\Application\Purchase\UseCases\GetPurchasePaymentByPurchaseIdTable;
use App\Application\Purchase\UseCases\GetPurchases;
use App\Application\Purchase\UseCases\SavePurchase;
use App\Application\Purchase\UseCases\UpdatePurchaseCartDetail;
use App\Application\Role\UseCases\CreateRole;
use App\Application\Role\UseCases\FindRoleById;
use App\Application\Role\UseCases\GetActiveRoles;
use App\Application\Role\UseCases\GetAllRolesTable;
use App\Application\Role\UseCases\UpdateRole;
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
use App\Application\Sale\UseCases\GetSaleDetailsTable;
use App\Application\Sale\UseCases\GetSaleReturnCartDetailsTable;
use App\Application\Sale\UseCases\GetSaleReturnsByDate;
use App\Application\Sale\UseCases\GetSalesByDate;
use App\Application\Sale\UseCases\UpdateQuantityReturnCartDetail;
use App\Application\Sale\UseCases\UpdateQuantitySalesCartDetailById;
use App\Application\Setting\UseCases\FindSetting;
use App\Application\Setting\UseCases\UpdateSetting;
use App\Application\StockOpname\UseCases\CreateDailyStockOpnameDetail;
use App\Application\StockOpname\UseCases\CreateMonthlyStockOpname;
use App\Application\StockOpname\UseCases\DeleteDailyStockOpnameDetail;
use App\Application\StockOpname\UseCases\ExistsDailyStockOpnameById;
use App\Application\StockOpname\UseCases\FindDailyStockOpnameByDate;
use App\Application\StockOpname\UseCases\FindDailyStockOpnameById;
use App\Application\StockOpname\UseCases\FindMonthlyStockByYearAndMonth;
use App\Application\StockOpname\UseCases\GetDailyStockOpnameByDate;
use App\Application\StockOpname\UseCases\GetDailyStockOpnameDetailByHeaderId;
use App\Application\StockOpname\UseCases\GetMonthlyStockDetails;
use App\Application\StockOpname\UseCases\SetActiveStock;
use App\Application\StockOpname\UseCases\UpdateQuantityDailyStockOpnameDetail;
use App\Application\Supplier\UseCases\CreateSupplier;
use App\Application\Supplier\UseCases\FindSupplierById;
use App\Application\Supplier\UseCases\GenerateSupplierCode;
use App\Application\Supplier\UseCases\GetActiveSuppliers;
use App\Application\Supplier\UseCases\GetAllSuppliers;
use App\Application\Supplier\UseCases\GetSuppliersTable;
use App\Application\Supplier\UseCases\UpdateSupplier;
use App\Application\Unit\UseCases\CreateUnit;
use App\Application\Unit\UseCases\FindUnitById;
use App\Application\Unit\UseCases\GetAllUnits;
use App\Application\Unit\UseCases\GetUnitsTable;
use App\Application\Unit\UseCases\UpdateUnit;
use App\Application\User\UseCases\CreateUser;
use App\Application\User\UseCases\FindUserById;
use App\Application\User\UseCases\GetUsersTable;
use App\Application\User\UseCases\UpdateUser;
use App\Domain\ActivityLog\Repositories\ActivityLogRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Discount\Repositories\DiscountRepositoryInterface;
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Module\Repositories\ModuleAccessRepositoryInterface;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
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
use App\Repositories\DiscountRepository;
use App\Repositories\ErrorLogRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\MonthlyStockOpnameDetailRepository;
use App\Repositories\MonthlyStockOpnameRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseCartDetailRepository;
use App\Repositories\PurchaseCartRepository;
use App\Repositories\PurchaseDetailRepository;
use App\Repositories\PurchasePaymentRepository;
use App\Repositories\PurchaseRepository;
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
            CreateProduct::class => ErrorLogRepository::class,
            UpdateProduct::class => ErrorLogRepository::class,
            ResetDiscountProduct::class => ErrorLogRepository::class,
            CreateCustomer::class => ErrorLogRepository::class,
            UpdateCustomer::class => ErrorLogRepository::class,
            GetCustomersTable::class => ErrorLogRepository::class,
            FindCustomerById::class => ErrorLogRepository::class,
            GetSuppliersTable::class => ErrorLogRepository::class,
            CreateSupplier::class => ErrorLogRepository::class,
            UpdateSupplier::class => ErrorLogRepository::class,
            FindSupplierById::class => ErrorLogRepository::class,
            GetAllSuppliers::class => ErrorLogRepository::class,
            UpdateSetting::class => ErrorLogRepository::class,
            CreatePurchaseCart::class => ErrorLogRepository::class,
            CreatePurchaseCartDetail::class => ErrorLogRepository::class,
            UpdatePurchaseCartDetail::class => ErrorLogRepository::class,
            DeletePurchaseCartDetail::class => ErrorLogRepository::class,
            SavePurchase::class => ErrorLogRepository::class,
            CreatePurchasePayment::class => ErrorLogRepository::class,
            DeletePurchasePaymentById::class => ErrorLogRepository::class,
            CreateSaleCart::class => ErrorLogRepository::class,
            DeleteSaleCartById::class => ErrorLogRepository::class,
            CreateDiscount::class => ErrorLogRepository::class,
            UpdateDiscount::class => ErrorLogRepository::class,
            RemoveDiscountById::class => ErrorLogRepository::class,
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
            CreateProduct::class => ActivityLogRepository::class,
            UpdateProduct::class => ActivityLogRepository::class,
            ResetDiscountProduct::class => ActivityLogRepository::class,
            CreateCustomer::class => ActivityLogRepository::class,
            UpdateCustomer::class => ActivityLogRepository::class,
            GetCustomersTable::class => ActivityLogRepository::class,
            FindCustomerById::class => ActivityLogRepository::class,
            GetSuppliersTable::class => ActivityLogRepository::class,
            CreateSupplier::class => ActivityLogRepository::class,
            UpdateSupplier::class => ActivityLogRepository::class,
            FindSupplierById::class => ActivityLogRepository::class,
            GetAllSuppliers::class => ActivityLogRepository::class,
            UpdateSetting::class => ActivityLogRepository::class,
            CreatePurchaseCart::class => ActivityLogRepository::class,
            SavePurchase::class => ActivityLogRepository::class,
            CreatePurchasePayment::class => ActivityLogRepository::class,
            DeletePurchasePaymentById::class => ActivityLogRepository::class,
            CreateSaleCart::class => ActivityLogRepository::class,
            DeleteSaleCartById::class => ActivityLogRepository::class,
            CreateDiscount::class => ActivityLogRepository::class,
            UpdateDiscount::class => ActivityLogRepository::class,
            RemoveDiscountById::class => ActivityLogRepository::class,
            CreateSale::class => ActivityLogRepository::class,
            CreateSaleReturnCart::class => ActivityLogRepository::class,
            DeleteSaleReturnCartByCurrentUser::class => ActivityLogRepository::class,
            CreateSaleReturn::class => ActivityLogRepository::class,
            CreateDailyStockOpnameDetail::class => ActivityLogRepository::class,
            DeleteDailyStockOpnameDetail::class => ActivityLogRepository::class,
            UpdateQuantityDailyStockOpnameDetail::class => ActivityLogRepository::class,
            SetActiveStock::class => ActivityLogRepository::class,
            CreateMonthlyStockOpname::class => ActivityLogRepository::class,
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
            ResetDiscountProduct::class => ProductRepository::class,
            CreatePurchaseCartDetail::class => ProductRepository::class,
            SavePurchase::class => ProductRepository::class,
            GetActiveProductsTable::class => ProductRepository::class,
            CreateSaleCartDetail::class => ProductRepository::class,
            DeleteSaleCartDetailById::class => ProductRepository::class,
            UpdateQuantitySalesCartDetailById::class => ProductRepository::class,
            CreateSale::class => ProductRepository::class,
            CreateSaleReturnCartDetail::class => ProductRepository::class,
            CreateSaleReturn::class => ProductRepository::class,
            CreateDailyStockOpnameDetail::class => ProductRepository::class,
            SetActiveStock::class => ProductRepository::class,
        ],

        // Customer
        CustomerRepositoryInterface::class => [
            CreateCustomer::class => CustomerRepository::class,
            UpdateCustomer::class => CustomerRepository::class,
            GetCustomersTable::class => CustomerRepository::class,
            FindCustomerById::class => CustomerRepository::class,
            GetAllCustomers::class => CustomerRepository::class,
            CreateSale::class => CustomerRepository::class,
        ],

        // Supplier
        SupplierRepositoryInterface::class => [
            GetSuppliersTable::class => SupplierRepository::class,
            CreateSupplier::class => SupplierRepository::class,
            UpdateSupplier::class => SupplierRepository::class,
            FindSupplierById::class => SupplierRepository::class,
            GetAllSuppliers::class => SupplierRepository::class,
            GenerateSupplierCode::class => SupplierRepository::class,
            GetActiveSuppliers::class => SupplierRepository::class,
        ],

        // Discount
        DiscountRepositoryInterface::class => [
            GetAllDiscountsTable::class => DiscountRepository::class,
            GetActiveDiscountsTable::class => DiscountRepository::class,
            CreateDiscount::class => DiscountRepository::class,
            UpdateDiscount::class => DiscountRepository::class,
            FindDiscountById::class => DiscountRepository::class,
            RemoveDiscountById::class => DiscountRepository::class,
            GetAllDiscounts::class => DiscountRepository::class,
        ],

        // Purchase cart
        PurchaseCartRepositoryInterface::class => [
            FindPurchaseCartByCurrentUser::class => PurchaseCartRepository::class,
            CreatePurchaseCart::class => PurchaseCartRepository::class,
            CreatePurchaseCartDetail::class => PurchaseCartRepository::class,
            UpdatePurchaseCartDetail::class => PurchaseCartRepository::class,
            DeletePurchaseCartDetail::class => PurchaseCartRepository::class,
            DeletePurchaseCartByCurrentUser::class => PurchaseCartRepository::class,
            SavePurchase::class => PurchaseCartRepository::class,
        ],

        // Purchase cart detail
        PurchaseCartDetailRepositoryInterface::class => [
            CreatePurchaseCartDetail::class => PurchaseCartDetailRepository::class,
            UpdatePurchaseCartDetail::class => PurchaseCartDetailRepository::class,
            DeletePurchaseCartDetail::class => PurchaseCartDetailRepository::class,
            GetPurchaseCartDetailByCartId::class => PurchaseCartDetailRepository::class,
            SavePurchase::class => PurchaseCartDetailRepository::class,
        ],

        // Purchase
        PurchaseRepositoryInterface::class => [
            SavePurchase::class => PurchaseRepository::class,
            GetPurchaseByDateRangeTable::class => PurchaseRepository::class,
            FindPurchaseById::class => PurchaseRepository::class,
            CreatePurchasePayment::class => PurchaseRepository::class,
            DeletePurchasePaymentById::class => PurchaseRepository::class,
            GetPurchases::class => PurchaseRepository::class,
        ],

        // Purchase Detail
        PurchaseDetailRepositoryInterface::class => [
            SavePurchase::class => PurchaseDetailRepository::class,
            GetPurchaseDetailByPurchaseIdTable::class => PurchaseDetailRepository::class,
            GetPurchaseDetailByPurchaseId::class => PurchaseDetailRepository::class,
        ],

        // Purchase payment
        PurchasePaymentRepositoryInterface::class => [
            SavePurchase::class => PurchasePaymentRepository::class,
            GetPurchasePaymentByPurchaseId::class => PurchasePaymentRepository::class,
            GetPurchasePaymentByPurchaseIdTable::class => PurchasePaymentRepository::class,
            CreatePurchasePayment::class => PurchasePaymentRepository::class,
            DeletePurchasePaymentById::class => PurchasePaymentRepository::class,
        ],

        // Stock movement
        StockMovementRepositoryInterface::class => [
            SavePurchase::class => StockMovementRepository::class,
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
            GetSalesByDate::class => SaleRepository::class,
        ],

        // Sale detail
        SaleDetailRepositoryInterface::class => [
            CreateSale::class => SaleDetailRepository::class,
            GetSaleDetailsTable::class => SaleDetailRepository::class,
            CreateSaleReturnCartDetail::class => SaleDetailRepository::class,
            UpdateQuantityReturnCartDetail::class => SaleDetailRepository::class,
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
        ],

        // Sale return Detail
        SaleReturnDetailRepositoryInterface::class => [
            CreateSaleReturn::class => SaleReturnDetailRepository::class,
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
        ],

        // Daily stock opname detail
        DailyStockOpnameDetailRepositoryInterface::class => [
            GetDailyStockOpnameDetailByHeaderId::class => DailyStockOpnameDetailRepository::class,
            CreateDailyStockOpnameDetail::class => DailyStockOpnameDetailRepository::class,
            DeleteDailyStockOpnameDetail::class => DailyStockOpnameDetailRepository::class,
            UpdateQuantityDailyStockOpnameDetail::class => DailyStockOpnameDetailRepository::class,
            SetActiveStock::class => DailyStockOpnameDetailRepository::class,
            CreateMonthlyStockOpname::class => DailyStockOpnameDetailRepository::class,
        ],

        // Monthly stock opname
        MonthlyStockOpnameRepositoryInterface::class => [
            CreateMonthlyStockOpname::class => MonthlyStockOpnameRepository::class,
            FindMonthlyStockByYearAndMonth::class => MonthlyStockOpnameRepository::class,
        ],

        // Monthly stock opname detail
        MonthlyStockOpnameDetailRepositoryInterface::class => [
            CreateMonthlyStockOpname::class => MonthlyStockOpnameDetailRepository::class,
            GetMonthlyStockDetails::class => MonthlyStockOpnameDetailRepository::class,
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
