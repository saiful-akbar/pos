<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Purchase\DTOs\GetPurchasesDTO;
use App\Application\Purchase\UseCases\GetPurchaseDetailsByDate;
use App\Application\Purchase\UseCases\GetPurchases;
use App\Domain\Purchase\Enums\PaymentStatusEnum;
use App\Http\Requests\Reports\GetPurchasesReportRequest;
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
}
