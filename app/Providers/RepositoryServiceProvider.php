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
use App\Application\Purchase\UseCases\CreatePurchase;
use App\Application\Purchase\UseCases\CreatePurchaseCart;
use App\Application\Purchase\UseCases\CreatePurchaseCartDetail;
use App\Application\Purchase\UseCases\CreatePurchasePayment;
use App\Application\Purchase\UseCases\DeletePurchaseCart;
use App\Application\Purchase\UseCases\DeletePurchaseCartDetail;
use App\Application\Purchase\UseCases\DeletePurchasePayment;
use App\Application\Purchase\UseCases\FindPurchaseById;
use App\Application\Purchase\UseCases\FindPurchaseCartByCurrentUser;
use App\Application\Purchase\UseCases\FindPurchaseCartById;
use App\Application\Purchase\UseCases\FindPurchaseCartDetailById;
use App\Application\Purchase\UseCases\GetPurchaseCartDetailsByPurchaseCartId;
use App\Application\Purchase\UseCases\GetPurchaseCartDetailsTable;
use App\Application\Purchase\UseCases\GetPurchaseDetailTableByPurchaseId;
use App\Application\Purchase\UseCases\GetPurchasePaymentsTableByPurchaseId;
use App\Application\Purchase\UseCases\GetPurchasesByDate;
use App\Application\Purchase\UseCases\RemovePurchase;
use App\Application\Purchase\UseCases\UpdatePurchaseCartDetail;
use App\Application\Role\UseCases\CreateRole;
use App\Application\Role\UseCases\FindRoleById;
use App\Application\Role\UseCases\GetActiveRoles;
use App\Application\Role\UseCases\GetAllRolesTable;
use App\Application\Role\UseCases\UpdateRole;
use App\Application\Rreport\UseCases\GetMonitoringMonthlyStock;
use App\Application\Sale\UseCases\CreateSale;
use App\Application\Sale\UseCases\CreateSaleCart;
use App\Application\Sale\UseCases\CreateSaleCartDetail;
use App\Application\Sale\UseCases\DeleteSaleCart;
use App\Application\Sale\UseCases\DeleteSaleCartDetail;
use App\Application\Sale\UseCases\FindSaleById;
use App\Application\Sale\UseCases\FindSaleCartByUserId;
use App\Application\Sale\UseCases\GetSaleCartDetail;
use App\Application\Sale\UseCases\GetSaleCartDetailTable;
use App\Application\Sale\UseCases\GetSaleDetailsBySaleId;
use App\Application\Sale\UseCases\GetSalesByDateRange;
use App\Application\Sale\UseCases\RemoveSale;
use App\Application\Sale\UseCases\UpdateSaleCart;
use App\Application\Sale\UseCases\UpdateSaleCartDetailByPrice;
use App\Application\Sale\UseCases\UpdateSaleCartDetailByQuantity;
use App\Application\Setting\UseCases\FindSetting;
use App\Application\Setting\UseCases\UpdateSetting;
use App\Application\Supplier\UseCases\CreateSupplier;
use App\Application\Supplier\UseCases\FindSupplierById;
use App\Application\Supplier\UseCases\GetAllSuppliers;
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
use App\Domain\ErrorLog\Repositories\ErrorLogRepositoryInterface;
use App\Domain\Module\Repositories\ModuleAccessRepositoryInterface;
use App\Domain\Module\Repositories\ModuleRepositoryInterface;
use App\Domain\Price\Repositories\PriceRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseCartRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseDetailRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchasePaymentRepositoryInterface;
use App\Domain\Purchase\Repositories\PurchaseRepositoryInterface;
use App\Domain\Report\Repositories\ReportRepositoryInterface;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleCartRepositoryInterface;
use App\Domain\Sale\Repositories\SaleDetailRepositoryInterface;
use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Setting\Repositories\SettingRepositoryInterface;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Repositories\ActivityLogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ErrorLogRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\PriceRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseCartDetailRepository;
use App\Repositories\PurchaseCartRepository;
use App\Repositories\PurchaseDetailRepository;
use App\Repositories\PurchasePaymentRepository;
use App\Repositories\PurchaseRepository;
use App\Repositories\ReportRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SaleCartDetailRepository;
use App\Repositories\SaleCartRepository;
use App\Repositories\SaleDetailRepository;
use App\Repositories\SaleRepository;
use App\Repositories\SettingRepository;
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
            RemoveUser::class => ErrorLogRepository::class,
            RemoveCustomer::class => ErrorLogRepository::class,
            RemoveCategory::class => ErrorLogRepository::class,
            RemoveUnit::class => ErrorLogRepository::class,
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
            UpdatePurchaseCartDetail::class => ErrorLogRepository::class,
            CreatePurchase::class => ErrorLogRepository::class,
            RemovePurchase::class => ErrorLogRepository::class,
            CreatePurchasePayment::class => ErrorLogRepository::class,
            DeletePurchasePayment::class => ErrorLogRepository::class,
            CreateSaleCart::class => ErrorLogRepository::class,
            DeleteSaleCart::class => ErrorLogRepository::class,
            CreateSaleCartDetail::class => ErrorLogRepository::class,
            UpdateSaleCartDetailByPrice::class => ErrorLogRepository::class,
            UpdateSaleCartDetailByQuantity::class => ErrorLogRepository::class,
            DeleteSaleCartDetail::class => ErrorLogRepository::class,
            UpdateSaleCart::class => ErrorLogRepository::class,
            CreateSale::class => ErrorLogRepository::class,
            RemoveSale::class => ErrorLogRepository::class,
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
            RemoveUser::class => ActivityLogRepository::class,
            RemoveCustomer::class => ActivityLogRepository::class,
            RemoveCategory::class => ActivityLogRepository::class,
            RemoveUnit::class => ActivityLogRepository::class,
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
            CreatePurchase::class => ActivityLogRepository::class,
            RemovePurchase::class => ActivityLogRepository::class,
            CreatePurchasePayment::class => ActivityLogRepository::class,
            DeletePurchasePayment::class => ActivityLogRepository::class,
            CreateSaleCart::class => ActivityLogRepository::class,
            DeleteSaleCart::class => ActivityLogRepository::class,
            CreateSaleCartDetail::class => ActivityLogRepository::class,
            CreateSale::class => ActivityLogRepository::class,
            RemoveSale::class => ActivityLogRepository::class,
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
            CreatePurchase::class => ProductRepository::class,
            RemovePurchase::class => ProductRepository::class,
            CreateSaleCartDetail::class => ProductRepository::class,
            CreateSale::class => ProductRepository::class,
        ],

        // Product Price
        PriceRepositoryInterface::class => [
            GetPricesByProduct::class => PriceRepository::class,
            GetPricesTableByProduct::class => PriceRepository::class,
            CreatePrice::class => PriceRepository::class,
            DeletePrice::class => PriceRepository::class,
            FindPriceById::class => PriceRepository::class,
            CreatePurchaseCartDetail::class => PriceRepository::class,
            UpdatePurchaseCartDetail::class => PriceRepository::class,
            CreateSaleCartDetail::class => PriceRepository::class,
            UpdateSaleCartDetailByPrice::class => PriceRepository::class,
            UpdateSaleCartDetailByQuantity::class => PriceRepository::class,
        ],

        // Customer
        CustomerRepositoryInterface::class => [
            GetCustomersTable::class => CustomerRepository::class,
            FindCustomerById::class => CustomerRepository::class,
            CreateCustomer::class => CustomerRepository::class,
            UpdateCustomer::class => CustomerRepository::class,
            RemoveCustomer::class => CustomerRepository::class,
            UpdateSaleCart::class => CustomerRepository::class,
        ],

        // Supplier
        SupplierRepositoryInterface::class => [
            GetSupplierTable::class => SupplierRepository::class,
            CreateSupplier::class => SupplierRepository::class,
            UpdateSupplier::class => SupplierRepository::class,
            RemoveSupplier::class => SupplierRepository::class,
            FindSupplierById::class => SupplierRepository::class,
            GetAllSuppliers::class => SupplierRepository::class,
        ],

        // Report
        ReportRepositoryInterface::class => [
            GetMonitoringMonthlyStock::class => ReportRepository::class,
        ],

        // Purchase cart
        PurchaseCartRepositoryInterface::class => [
            FindPurchaseCartByCurrentUser::class => PurchaseCartRepository::class,
            FindPurchaseCartById::class => PurchaseCartRepository::class,
            CreatePurchaseCart::class => PurchaseCartRepository::class,
            DeletePurchaseCart::class => PurchaseCartRepository::class,
            CreatePurchaseCartDetail::class => PurchaseCartRepository::class,
            DeletePurchaseCartDetail::class => PurchaseCartRepository::class,
            UpdatePurchaseCartDetail::class => PurchaseCartRepository::class,
            CreatePurchase::class => PurchaseCartRepository::class,
        ],

        // Purchase cart detail
        PurchaseCartDetailRepositoryInterface::class => [
            CreatePurchaseCartDetail::class => PurchaseCartDetailRepository::class,
            GetPurchaseCartDetailsByPurchaseCartId::class => PurchaseCartDetailRepository::class,
            DeletePurchaseCartDetail::class => PurchaseCartDetailRepository::class,
            FindPurchaseCartDetailById::class => PurchaseCartDetailRepository::class,
            GetPurchaseCartDetailsTable::class => PurchaseCartDetailRepository::class,
            UpdatePurchaseCartDetail::class => PurchaseCartDetailRepository::class,
            CreatePurchase::class => PurchaseCartDetailRepository::class,
        ],

        // Purchase
        PurchaseRepositoryInterface::class => [
            CreatePurchase::class => PurchaseRepository::class,
            GetPurchasesByDate::class => PurchaseRepository::class,
            RemovePurchase::class => PurchaseRepository::class,
            FindPurchaseById::class => PurchaseRepository::class,
            CreatePurchasePayment::class => PurchaseRepository::class,
            DeletePurchasePayment::class => PurchaseRepository::class,
        ],

        // Purchase detail
        PurchaseDetailRepositoryInterface::class => [
            CreatePurchase::class => PurchaseDetailRepository::class,
            RemovePurchase::class => PurchaseDetailRepository::class,
            GetPurchaseDetailTableByPurchaseId::class => PurchaseDetailRepository::class,
        ],

        // Purchase payment
        PurchasePaymentRepositoryInterface::class => [
            CreatePurchase::class => PurchasePaymentRepository::class,
            RemovePurchase::class => PurchasePaymentRepository::class,
            GetPurchasePaymentsTableByPurchaseId::class => PurchasePaymentRepository::class,
            CreatePurchasePayment::class => PurchasePaymentRepository::class,
            DeletePurchasePayment::class => PurchasePaymentRepository::class,
        ],

        // Sale cart
        SaleCartRepositoryInterface::class => [
            CreateSaleCart::class => SaleCartRepository::class,
            FindSaleCartByUserId::class => SaleCartRepository::class,
            DeleteSaleCart::class => SaleCartRepository::class,
            CreateSaleCartDetail::class => SaleCartRepository::class,
            UpdateSaleCartDetailByPrice::class => SaleCartRepository::class,
            UpdateSaleCartDetailByQuantity::class => SaleCartRepository::class,
            DeleteSaleCartDetail::class => SaleCartRepository::class,
            UpdateSaleCart::class => SaleCartRepository::class,
            CreateSale::class => SaleCartRepository::class,
        ],

        // Sale cart detail
        SaleCartDetailRepositoryInterface::class => [
            CreateSaleCart::class => SaleCartDetailRepository::class,
            CreateSaleCartDetail::class => SaleCartDetailRepository::class,
            GetSaleCartDetailTable::class => SaleCartDetailRepository::class,
            GetSaleCartDetail::class => SaleCartDetailRepository::class,
            UpdateSaleCartDetailByPrice::class => SaleCartDetailRepository::class,
            UpdateSaleCartDetailByQuantity::class => SaleCartDetailRepository::class,
            DeleteSaleCartDetail::class => SaleCartDetailRepository::class,
            CreateSale::class => SaleCartDetailRepository::class,
        ],

        // Sale
        SaleRepositoryInterface::class => [
            CreateSale::class => SaleRepository::class,
            GetSalesByDateRange::class => SaleRepository::class,
            RemoveSale::class => SaleRepository::class,
            FindSaleById::class => SaleRepository::class,
        ],

        // Sale detail
        SaleDetailRepositoryInterface::class => [
            CreateSale::class => SaleDetailRepository::class,
            RemoveSale::class => SaleDetailRepository::class,
            GetSaleDetailsBySaleId::class => SaleDetailRepository::class,
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
