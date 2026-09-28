<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Purchase\DTOs\GetPurchasesDTO;
use App\Application\Purchase\UseCases\GetPurchaseDetailsByDate;
use App\Application\Purchase\UseCases\GetPurchases;
use App\Application\Sale\DTOs\GetSalesDTO;
use App\Application\Sale\UseCases\GetSaleDetails;
use App\Application\Sale\UseCases\GetSales;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Http\Requests\Reports\GetPurchasesReportRequest;
use App\Http\Requests\Reports\GetSalesReportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Menmapilkan halaman summary laporan pembelian
     */
    public function purchaseSummaries(GetPurchasesReportRequest $request): View|JsonResponse
    {
        if ($request->ajax()) {
            return $this->jsonResponse(
                data: app(GetPurchases::class)->execute(new GetPurchasesDTO(
                    fromDate: $request->date('from_date'),
                    toDate: $request->date('to_date'),
                    paymentStatus: PaymentStatusEnum::tryFrom($request->validated('payment_status')),
                )),
            );
        }

        return view('pages.reports.purchase-summary');
    }

    /**
     * Menampilkan halaman detail laporan pembelian
     */
    public function purchaseDetails(GetPurchasesReportRequest $request): View|JsonResponse
    {
        if ($request->ajax()) {
            return $this->jsonResponse(
                data: app(GetPurchaseDetailsByDate::class)->execute(new GetPurchasesDTO(
                    fromDate: $request->date('from_date'),
                    toDate: $request->date('to_date'),
                    paymentStatus: PaymentStatusEnum::tryFrom($request->validated('payment_status')),
                )),
            );
        }

        return view('pages.reports.purchase-detail');
    }

    /**
     * Menampilkan halaman total laporan penjualan.
     */
    public function saleSummaries(GetSalesReportRequest $request): View|JsonResponse
    {
        if ($request->ajax()) {
            return $this->jsonResponse(
                data: app(GetSales::class)->execute(new GetSalesDTO(
                    fromDate: $request->date('from_date'),
                    toDate: $request->date('to_date'),
                    invoiceNumber: $request->validated('invoice_number'),
                )),
            );
        }

        return view('pages.reports.sale-summary');
    }

    /**
     * Menampilkan halaman detail laporan penjualan.
     */
    public function saleDetails(GetSalesReportRequest $request): View|JsonResponse
    {
        if ($request->ajax()) {
            return $this->jsonResponse(
                data: app(GetSaleDetails::class)->execute(new GetSalesDTO(
                    fromDate: $request->date('from_date'),
                    toDate: $request->date('to_date'),
                    invoiceNumber: $request->validated('invoice_number'),
                )),
            );
        }

        return view('pages.reports.sale-detail');
    }
}
