<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Application\Report\DTOs\GetMonitoringMonthlyDTO;
use App\Base\Repository;
use App\Domain\Report\Entities\MonitoringMonthlyStockEntity;
use App\Domain\Report\Repositories\ReportRepositoryInterface;
use App\Models\Category;
use App\Models\MonthlyStockOpnameDetail;
use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Models\SaleDetail;
use App\Models\SaleReturnDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Override;

class ReportRepository extends Repository implements ReportRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * Mengambil daftar monitoring stok bulanan per produk.
     *
     * @return MonitoringMonthlyStockEntity[]
     */
    #[Override]
    public function getMonitoringMonthlyStock(GetMonitoringMonthlyDTO $params): array
    {
        try {
            $columns = [
                'categories.code as category_code',
                'categories.name as category_name',
                'products.code as product_code',
                'products.name as product_name',
                DB::raw('coalesce(beginning_stocks.total, 0) as beginning_stock'),
                DB::raw('coalesce(purchases.total, 0) as purchase_total'),
                DB::raw('coalesce(sale_returns.total, 0) as sale_return_total'),
                DB::raw('coalesce(sales.total, 0) as sale_total'),
                DB::raw('coalesce(ending_stocks.total, 0) as ending_stock'),
            ];

            // query kategori produk.
            $categoryQuery = Category::query()->select('id', 'code', 'name');

            // query stock awal.
            $beginningStockQuery = MonthlyStockOpnameDetail::query()
                ->select('product_id', DB::raw('sum(quantity) as total'))
                ->whereHas('monthlyStockOpname', function (Builder $query) use ($params): void {
                    $query->where('year', $params->year)
                        ->where('month', $params->month);
                })
                ->groupBy('product_id');

            // Query pembelian
            $purchaseQuery = PurchaseDetail::query()
                ->select('product_id', DB::raw('sum(quantity) as total'))
                ->whereHas('purchase', function (Builder $query) use ($params): void {
                    $query->whereRaw('year(purchase_date) = ?', [$params->year])
                        ->whereRaw('month(purchase_date) = ?', [$params->month]);
                })
                ->groupBy('product_id');

            // Query return penjualan
            $saleReturnQuery = SaleReturnDetail::query()
                ->select('product_id', DB::raw('sum(quantity) as total'))
                ->whereHas('saleReturn', function (Builder $query) use ($params): void {
                    $query->whereRaw('year(transaction_date) = ?', [$params->year])
                        ->whereRaw('month(transaction_date) = ?', [$params->month]);
                })
                ->groupBy('product_id');

            // Query penjualan
            $saleQuery = SaleDetail::query()
                ->select('product_id', DB::raw('sum(quantity) as total'))
                ->whereHas('sale', function (Builder $query) use ($params): void {
                    $query->whereRaw('year(transaction_date) = ?', [$params->year])
                        ->whereRaw('month(transaction_date) = ?', [$params->month]);
                })
                ->groupBy('product_id');

            // query stock akhir.
            $endingStockQuery = MonthlyStockOpnameDetail::query()
                ->select('product_id', DB::raw('sum(quantity) as total'))
                ->whereHas('monthlyStockOpname', function (Builder $query) use ($params): void {
                    $query->where('year', $params->endYear)
                        ->where('month', $params->endMonth);
                })
                ->groupBy('product_id');

            // Query monitoring.
            $result = Product::query()
                ->from('products')
                ->select($columns)
                ->leftJoinSub($categoryQuery, 'categories', function (JoinClause $subQuery): void {
                    $subQuery->on('products.category_id', '=', 'categories.id');
                })
                ->leftJoinSub($beginningStockQuery, 'beginning_stocks', function (JoinClause $subQuery): void {
                    $subQuery->on('products.id', '=', 'beginning_stocks.product_id');
                })
                ->leftJoinSub($purchaseQuery, 'purchases', function (JoinClause $subQuery): void {
                    $subQuery->on('purchases.product_id', '=', 'products.id');
                })
                ->leftJoinSub($saleReturnQuery, 'sale_returns', function (JoinClause $subQuery): void {
                    $subQuery->on('sale_returns.product_id', '=', 'products.id');
                })
                ->leftJoinSub($saleQuery, 'sales', function (JoinClause $subQuery): void {
                    $subQuery->on('sales.product_id', '=', 'products.id');
                })
                ->leftJoinSub($endingStockQuery, 'ending_stocks', function (JoinClause $subQuery): void {
                    $subQuery->on('ending_stocks.product_id', '=', 'products.id');
                })
                ->where('products.is_active', 1)
                ->orderBy('products.code', 'asc')
                ->get()
                ->map(fn($data) => $this->mapModelToMonitoringMonthlyStockEntity($data))
                ->toArray();

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
     * Pemetaan class Model ke class MonitoringMonthlyStockEntity.
     */
    private function mapModelToMonitoringMonthlyStockEntity(Model $model): MonitoringMonthlyStockEntity
    {
        return new MonitoringMonthlyStockEntity(
            categoryCode: $model->category_code,
            categoryName: $model->category_name,
            productCode: $model->product_code,
            productName: $model->product_name,
            beginningStock: parse_float($model->beginning_stock),
            purchaseQuantity: parse_float($model->purchase_total),
            saleReturnQuantity: parse_float($model->sale_return_total),
            saleQuantity: parse_float($model->sale_total),
            endingStock: parse_float($model->ending_stock),
        );
    }
}
