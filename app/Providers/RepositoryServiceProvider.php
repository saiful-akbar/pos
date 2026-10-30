<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\ActivityLog\UseCases\CreateActivity;
use App\Application\Cash\UseCases\CreateCashTransaction;
use App\Application\Cash\UseCases\DeleteCashTransaction;
use App\Application\Cash\UseCases\FindCashTransactionById;
use App\Application\Cash\UseCases\GetCashMonthlySummaries;
use App\Application\Cash\UseCases\GetCashMonthSummary;
use App\Application\Cash\UseCases\GetCashRecorders;
use App\Application\Cash\UseCases\GetCashTransactionsQuery;
use App\Application\Cash\UseCases\GetCashTransactionsTable;
use App\Application\Cash\UseCases\GetCashTransactionTotals;
use App\Application\Cash\UseCases\SaveCashMonthlyBalance;
use App\Application\Cash\UseCases\UpdateCashTransaction;
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
use App\Application\Dashboard\UseCases\GetDashboardFastMoving;
use App\Application\Dashboard\UseCases\GetDashboardLowStock;
use App\Application\Dashboard\UseCases\GetDashboardMonthlySales;
use App\Application\Dashboard\UseCases\GetDashboardSalesTotals;
use App\Application\Dashboard\UseCases\GetDashboardSlowMoving;
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
use App\Application\Price\UseCases\UpdatePrice;
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
use App\Application\Purchase\UseCases\UpdatePurchaseCartDetail;
use App\Application\Report\UseCases\GetPurchaseReportProductQuery;
use App\Application\Report\UseCases\GetPurchaseReportProductTable;
use App\Application\Report\UseCases\GetPurchaseReportQuery;
use App\Application\Report\UseCases\GetPurchaseReportSummary;
use App\Application\Report\UseCases\GetPurchaseReportTable;
use App\Application\Report\UseCases\GetPurchaseTransactionReport;
use App\Application\Report\UseCases\GetSaleReportFilterOptions;
use App\Application\Report\UseCases\GetSaleReportProductQuery;
use App\Application\Report\UseCases\GetSaleReportProductTable;
use App\Application\Report\UseCases\GetSaleReportQuery;
use App\Application\Report\UseCases\GetSaleReportSummary;
use App\Application\Report\UseCases\GetSaleReportTable;
use App\Application\Report\UseCases\GetSaleReturnReportAnalysis;
use App\Application\Report\UseCases\GetSaleReturnReportDetailsTable;
use App\Application\Report\UseCases\GetSaleReturnReportFilterOptions;
use App\Application\Report\UseCases\GetSaleReturnReportProductQuery;
use App\Application\Report\UseCases\GetSaleReturnReportProductTable;
use App\Application\Report\UseCases\GetSaleReturnReportQuery;
use App\Application\Report\UseCases\GetSaleReturnReportSummary;
use App\Application\Report\UseCases\GetSaleReturnReportTable;
use App\Application\Report\UseCases\GetSaleTransactionReport;
use App\Application\Report\UseCases\GetStockReportMutations;
use App\Application\Report\UseCases\GetStockReportProduct;
use App\Application\Report\UseCases\GetStockReportQuery;
use App\Application\Report\UseCases\GetStockReportSummary;
use App\Application\Report\UseCases\GetStockReportTable;
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
use App\Application\Sale\UseCases\DeleteSaleCart;
use App\Application\Sale\UseCases\DeleteSaleCartDetail;
use App\Application\Sale\UseCases\DeleteSaleReturnCartById;
use App\Application\Sale\UseCases\DeleteSaleReturnCartDetail;
use App\Application\Sale\UseCases\FindSale;
use App\Application\Sale\UseCases\FindSaleById;
use App\Application\Sale\UseCases\FindSaleCartByUserId;
use App\Application\Sale\UseCases\FindSaleReturnCartByUserId;
use App\Application\Sale\UseCases\GetSaleCartDetail;
use App\Application\Sale\UseCases\GetSaleCartDetailTable;
use App\Application\Sale\UseCases\GetSaleDetailsBySaleId;
use App\Application\Sale\UseCases\GetSaleDetailsTable;
use App\Application\Sale\UseCases\GetSaleReturnCartDetailTable;
use App\Application\Sale\UseCases\GetSaleReturnCartInfo;
use App\Application\Sale\UseCases\GetSaleReturnsByDateRange;
use App\Application\Sale\UseCases\GetSalesByDateRange;
use App\Application\Sale\UseCases\UpdateSaleCart;
use App\Application\Sale\UseCases\UpdateSaleCartDetailByPrice;
use App\Application\Sale\UseCases\UpdateSaleCartDetailByQuantity;
use App\Application\Sale\UseCases\UpdateSaleReturnCartDetailQuantity;
use App\Application\Setting\UseCases\FindSetting;
use App\Application\Setting\UseCases\UpdateSetting;
use App\Application\Stock\UseCases\ActivateDailyStock;
use App\Application\Stock\UseCases\ActivateMonthlyStock;
use App\Application\Stock\UseCases\CreateMonthlyStock;
use App\Application\Stock\UseCases\CreateMonthlyStockByDate;
use App\Application\Stock\UseCases\DeleteDailyStock;
use App\Application\Stock\UseCases\DeleteMonthlyStock;
use App\Application\Stock\UseCases\FindDailyStockById;
use App\Application\Stock\UseCases\FindMonthlyStockById;
use App\Application\Stock\UseCases\GetDailyStockByDateRange;
use App\Application\Stock\UseCases\GetDailyStockDetailsTable;
use App\Application\Stock\UseCases\GetMonthlyStockDetailsQuery;
use App\Application\Stock\UseCases\GetMonthlyStockDetailsTable;
use App\Application\Stock\UseCases\GetMonthlyStocksTable;
use App\Application\Stock\UseCases\ImportDailyStock;
use App\Application\Stock\UseCases\UpdateDailyStockByImport;
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
use App\Domain\Cash\Repositories\CashMonthlyBalanceRepositoryInterface;
use App\Domain\Cash\Repositories\CashReportRepositoryInterface;
use App\Domain\Cash\Repositories\CashTransactionRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Dashboard\Repositories\ProductDashboardRepositoryInterface;
use App\Domain\Dashboard\Repositories\SaleDashboardRepositoryInterface;
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
use App\Domain\Report\Repositories\PurchaseReportRepositoryInterface;
use App\Domain\Report\Repositories\ReportRepositoryInterface;
use App\Domain\Report\Repositories\SaleReportRepositoryInterface;
use App\Domain\Report\Repositories\SaleReturnReportRepositoryInterface;
use App\Domain\Report\Repositories\StockReportRepositoryInterface;
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
use App\Domain\Stock\Repositories\DailyStockDetailRepositoryInterface;
use App\Domain\Stock\Repositories\DailyStockRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockDetailRepositoryInterface;
use App\Domain\Stock\Repositories\MonthlyStockRepositoryInterface;
use App\Domain\Supplier\Repositories\SupplierRepositoryInterface;
use App\Domain\Unit\Repositories\UnitRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Repositories\ActivityLogRepository;
use App\Repositories\CashMonthlyBalanceRepository;
use App\Repositories\CashReportRepository;
use App\Repositories\CashTransactionRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\DailyStockDetailRepository;
use App\Repositories\DailyStockRepository;
use App\Repositories\ErrorLogRepository;
use App\Repositories\ModuleRepository;
use App\Repositories\MonthlyStockDetailRepository;
use App\Repositories\MonthlyStockRepository;
use App\Repositories\PriceRepository;
use App\Repositories\ProductDashboardRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseCartDetailRepository;
use App\Repositories\PurchaseCartRepository;
use App\Repositories\PurchaseDetailRepository;
use App\Repositories\PurchasePaymentRepository;
use App\Repositories\PurchaseReportRepository;
use App\Repositories\PurchaseRepository;
use App\Repositories\ReportRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SaleCartDetailRepository;
use App\Repositories\SaleCartRepository;
use App\Repositories\SaleDashboardRepository;
use App\Repositories\SaleDetailRepository;
use App\Repositories\SaleReportRepository;
use App\Repositories\SaleRepository;
use App\Repositories\SaleReturnCartDetailRepository;
use App\Repositories\SaleReturnCartRepository;
use App\Repositories\SaleReturnDetailRepository;
use App\Repositories\SaleReturnReportRepository;
use App\Repositories\SaleReturnRepository;
use App\Repositories\SettingRepository;
use App\Repositories\StockReportRepository;
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
            UpdatePrice::class => ErrorLogRepository::class,
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
            CreateSaleReturnCart::class => ErrorLogRepository::class,
            DeleteSaleReturnCartById::class => ErrorLogRepository::class,
            CreateSaleReturnCartDetail::class => ErrorLogRepository::class,
            UpdateSaleReturnCartDetailQuantity::class => ErrorLogRepository::class,
            DeleteSaleReturnCartDetail::class => ErrorLogRepository::class,
            CreateCashTransaction::class => ErrorLogRepository::class,
            UpdateCashTransaction::class => ErrorLogRepository::class,
            DeleteCashTransaction::class => ErrorLogRepository::class,
            SaveCashMonthlyBalance::class => ErrorLogRepository::class,
            ImportDailyStock::class => ErrorLogRepository::class,
            UpdateDailyStockByImport::class => ErrorLogRepository::class,
            ActivateDailyStock::class => ErrorLogRepository::class,
            ActivateMonthlyStock::class => ErrorLogRepository::class,
            CreateMonthlyStock::class => ErrorLogRepository::class,
            CreateMonthlyStockByDate::class => ErrorLogRepository::class,
            DeleteDailyStock::class => ErrorLogRepository::class,
            DeleteMonthlyStock::class => ErrorLogRepository::class,
            CreateSaleReturn::class => ErrorLogRepository::class,
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
            UpdatePrice::class => ActivityLogRepository::class,
            FindPriceById::class => ActivityLogRepository::class,
            UpdateProduct::class => ActivityLogRepository::class,
            ResetAllDiscountProducts::class => ActivityLogRepository::class,
            RemoveProduct::class => ActivityLogRepository::class,
            CreatePurchaseCart::class => ActivityLogRepository::class,
            DeletePurchaseCart::class => ActivityLogRepository::class,
            CreatePurchase::class => ActivityLogRepository::class,
            CreatePurchasePayment::class => ActivityLogRepository::class,
            DeletePurchasePayment::class => ActivityLogRepository::class,
            CreateSaleCart::class => ActivityLogRepository::class,
            DeleteSaleCart::class => ActivityLogRepository::class,
            CreateSaleCartDetail::class => ActivityLogRepository::class,
            CreateSale::class => ActivityLogRepository::class,
            CreateSaleReturnCart::class => ActivityLogRepository::class,
            DeleteSaleReturnCartById::class => ActivityLogRepository::class,
            CreateSaleReturn::class => ActivityLogRepository::class,
            CreateCashTransaction::class => ActivityLogRepository::class,
            UpdateCashTransaction::class => ActivityLogRepository::class,
            DeleteCashTransaction::class => ActivityLogRepository::class,
            SaveCashMonthlyBalance::class => ActivityLogRepository::class,
            ImportDailyStock::class => ActivityLogRepository::class,
            UpdateDailyStockByImport::class => ActivityLogRepository::class,
            ActivateDailyStock::class => ActivityLogRepository::class,
            ActivateMonthlyStock::class => ActivityLogRepository::class,
            CreateMonthlyStock::class => ActivityLogRepository::class,
            DeleteDailyStock::class => ActivityLogRepository::class,
            DeleteMonthlyStock::class => ActivityLogRepository::class,
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
            ImportDailyStock::class => UnitRepository::class,
            UpdateDailyStockByImport::class => UnitRepository::class,
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
            CreateSaleCartDetail::class => ProductRepository::class,
            CreateSale::class => ProductRepository::class,
            CreateSaleReturnCartDetail::class => ProductRepository::class,
            CreateSaleReturn::class => ProductRepository::class,
            ImportDailyStock::class => ProductRepository::class,
            UpdateDailyStockByImport::class => ProductRepository::class,
            ActivateDailyStock::class => ProductRepository::class,
            ActivateMonthlyStock::class => ProductRepository::class,
        ],

        // Product Price
        PriceRepositoryInterface::class => [
            ImportDailyStock::class => PriceRepository::class,
            UpdateDailyStockByImport::class => PriceRepository::class,
            GetPricesByProduct::class => PriceRepository::class,
            GetPricesTableByProduct::class => PriceRepository::class,
            CreatePrice::class => PriceRepository::class,
            DeletePrice::class => PriceRepository::class,
            UpdatePrice::class => PriceRepository::class,
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

        // Purchase report
        PurchaseReportRepositoryInterface::class => [
            GetPurchaseReportTable::class => PurchaseReportRepository::class,
            GetPurchaseReportSummary::class => PurchaseReportRepository::class,
            GetPurchaseReportQuery::class => PurchaseReportRepository::class,
            GetPurchaseReportProductTable::class => PurchaseReportRepository::class,
            GetPurchaseReportProductQuery::class => PurchaseReportRepository::class,
        ],

        // Sale report
        SaleReportRepositoryInterface::class => [
            GetSaleReportTable::class => SaleReportRepository::class,
            GetSaleReportSummary::class => SaleReportRepository::class,
            GetSaleReportQuery::class => SaleReportRepository::class,
            GetSaleReportProductTable::class => SaleReportRepository::class,
            GetSaleReportProductQuery::class => SaleReportRepository::class,
            GetSaleReportFilterOptions::class => SaleReportRepository::class,
        ],

        // Sale return report
        SaleReturnReportRepositoryInterface::class => [
            GetSaleReturnReportTable::class => SaleReturnReportRepository::class,
            GetSaleReturnReportSummary::class => SaleReturnReportRepository::class,
            GetSaleReturnReportQuery::class => SaleReturnReportRepository::class,
            GetSaleReturnReportProductTable::class => SaleReturnReportRepository::class,
            GetSaleReturnReportProductQuery::class => SaleReturnReportRepository::class,
            GetSaleReturnReportAnalysis::class => SaleReturnReportRepository::class,
            GetSaleReturnReportDetailsTable::class => SaleReturnReportRepository::class,
            GetSaleReturnReportFilterOptions::class => SaleReturnReportRepository::class,
        ],

        // Stock report
        StockReportRepositoryInterface::class => [
            GetStockReportTable::class => StockReportRepository::class,
            GetStockReportSummary::class => StockReportRepository::class,
            GetStockReportQuery::class => StockReportRepository::class,
            GetStockReportMutations::class => StockReportRepository::class,
            GetStockReportProduct::class => StockReportRepository::class,
        ],

        // Cash transaction
        CashTransactionRepositoryInterface::class => [
            CreateCashTransaction::class => CashTransactionRepository::class,
            DeleteCashTransaction::class => CashTransactionRepository::class,
            FindCashTransactionById::class => CashTransactionRepository::class,
            GetCashRecorders::class => CashTransactionRepository::class,
            GetCashTransactionsQuery::class => CashTransactionRepository::class,
            GetCashTransactionsTable::class => CashTransactionRepository::class,
            GetCashTransactionTotals::class => CashTransactionRepository::class,
            UpdateCashTransaction::class => CashTransactionRepository::class,
            CreateSale::class => CashTransactionRepository::class,
            CreatePurchase::class => CashTransactionRepository::class,
            CreatePurchasePayment::class => CashTransactionRepository::class,
            DeletePurchasePayment::class => CashTransactionRepository::class,
            CreateSaleReturn::class => CashTransactionRepository::class,
        ],

        // Cash monthly balance
        CashMonthlyBalanceRepositoryInterface::class => [
            SaveCashMonthlyBalance::class => CashMonthlyBalanceRepository::class,
        ],

        // Cash report
        CashReportRepositoryInterface::class => [
            GetCashMonthlySummaries::class => CashReportRepository::class,
            GetCashMonthSummary::class => CashReportRepository::class,
        ],

        // Dashboard penjualan
        SaleDashboardRepositoryInterface::class => [
            GetDashboardSalesTotals::class => SaleDashboardRepository::class,
            GetDashboardMonthlySales::class => SaleDashboardRepository::class,
            GetDashboardFastMoving::class => SaleDashboardRepository::class,
            GetDashboardSlowMoving::class => SaleDashboardRepository::class,
        ],

        // Dashboard produk
        ProductDashboardRepositoryInterface::class => [
            GetDashboardLowStock::class => ProductDashboardRepository::class,
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
            FindPurchaseById::class => PurchaseRepository::class,
            GetPurchaseTransactionReport::class => PurchaseRepository::class,
            CreatePurchasePayment::class => PurchaseRepository::class,
            DeletePurchasePayment::class => PurchaseRepository::class,
        ],

        // Purchase detail
        PurchaseDetailRepositoryInterface::class => [
            CreatePurchase::class => PurchaseDetailRepository::class,
            GetPurchaseDetailTableByPurchaseId::class => PurchaseDetailRepository::class,
            GetPurchaseTransactionReport::class => PurchaseDetailRepository::class,
        ],

        // Purchase payment
        PurchasePaymentRepositoryInterface::class => [
            CreatePurchase::class => PurchasePaymentRepository::class,
            GetPurchasePaymentsTableByPurchaseId::class => PurchasePaymentRepository::class,
            GetPurchaseTransactionReport::class => PurchasePaymentRepository::class,
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
            FindSaleById::class => SaleRepository::class,
            GetSaleTransactionReport::class => SaleRepository::class,
            FindSale::class => SaleRepository::class,
            CreateSaleReturnCart::class => SaleRepository::class,
            GetSaleReturnCartInfo::class => SaleRepository::class,
        ],

        // Sale detail
        SaleDetailRepositoryInterface::class => [
            CreateSale::class => SaleDetailRepository::class,
            GetSaleDetailsBySaleId::class => SaleDetailRepository::class,
            GetSaleDetailsTable::class => SaleDetailRepository::class,
            GetSaleTransactionReport::class => SaleDetailRepository::class,
            CreateSaleReturnCartDetail::class => SaleDetailRepository::class,
            UpdateSaleReturnCartDetailQuantity::class => SaleDetailRepository::class,
        ],

        // Sale return cart
        SaleReturnCartRepositoryInterface::class => [
            CreateSaleReturnCart::class => SaleReturnCartRepository::class,
            FindSaleReturnCartByUserId::class => SaleReturnCartRepository::class,
            DeleteSaleReturnCartById::class => SaleReturnCartRepository::class,
            CreateSaleReturnCartDetail::class => SaleReturnCartRepository::class,
            GetSaleReturnCartInfo::class => SaleReturnCartRepository::class,
            UpdateSaleReturnCartDetailQuantity::class => SaleReturnCartRepository::class,
            DeleteSaleReturnCartDetail::class => SaleReturnCartRepository::class,
            CreateSaleReturn::class => SaleReturnCartRepository::class,
        ],

        // Sale return cart detail
        SaleReturnCartDetailRepositoryInterface::class => [
            GetSaleReturnCartDetailTable::class => SaleReturnCartDetailRepository::class,
            CreateSaleReturnCartDetail::class => SaleReturnCartDetailRepository::class,
            UpdateSaleReturnCartDetailQuantity::class => SaleReturnCartDetailRepository::class,
            DeleteSaleReturnCartDetail::class => SaleReturnCartDetailRepository::class,
            CreateSaleReturn::class => SaleReturnCartDetailRepository::class,
        ],

        // Daily stock
        DailyStockRepositoryInterface::class => [
            GetDailyStockByDateRange::class => DailyStockRepository::class,
            ImportDailyStock::class => DailyStockRepository::class,
            UpdateDailyStockByImport::class => DailyStockRepository::class,
            ActivateDailyStock::class => DailyStockRepository::class,
            FindDailyStockById::class => DailyStockRepository::class,
            CreateMonthlyStock::class => DailyStockRepository::class,
            CreateMonthlyStockByDate::class => DailyStockRepository::class,
            DeleteDailyStock::class => DailyStockRepository::class,
        ],

        // Monthly stock
        MonthlyStockRepositoryInterface::class => [
            CreateMonthlyStock::class => MonthlyStockRepository::class,
            CreateMonthlyStockByDate::class => MonthlyStockRepository::class,
            DeleteDailyStock::class => MonthlyStockRepository::class,
            GetMonthlyStocksTable::class => MonthlyStockRepository::class,
            FindMonthlyStockById::class => MonthlyStockRepository::class,
            ActivateMonthlyStock::class => MonthlyStockRepository::class,
            DeleteMonthlyStock::class => MonthlyStockRepository::class,
        ],

        // Monthly stock detail
        MonthlyStockDetailRepositoryInterface::class => [
            CreateMonthlyStock::class => MonthlyStockDetailRepository::class,
            GetMonthlyStockDetailsTable::class => MonthlyStockDetailRepository::class,
            GetMonthlyStockDetailsQuery::class => MonthlyStockDetailRepository::class,
            ActivateMonthlyStock::class => MonthlyStockDetailRepository::class,
        ],

        // Daily stock detail
        DailyStockDetailRepositoryInterface::class => [
            ImportDailyStock::class => DailyStockDetailRepository::class,
            UpdateDailyStockByImport::class => DailyStockDetailRepository::class,
            GetDailyStockDetailsTable::class => DailyStockDetailRepository::class,
        ],

        // Sale return
        SaleReturnRepositoryInterface::class => [
            GetSaleReturnsByDateRange::class => SaleReturnRepository::class,
            CreateSaleReturn::class => SaleReturnRepository::class,
        ],

        // Sale return detail
        SaleReturnDetailRepositoryInterface::class => [
            CreateSaleReturn::class => SaleReturnDetailRepository::class,
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
