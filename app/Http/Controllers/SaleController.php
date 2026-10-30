<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Module\UseCases\FindModuleAccessByRoute;
use App\Application\Sale\UseCases\CreateSale;
use App\Application\Sale\UseCases\CreateSaleCart;
use App\Application\Sale\UseCases\CreateSaleCartDetail;
use App\Application\Sale\UseCases\DeleteSaleCart;
use App\Application\Sale\UseCases\DeleteSaleCartDetail;
use App\Application\Sale\UseCases\FindSaleById;
use App\Application\Sale\UseCases\FindSaleCartByUserId;
use App\Application\Sale\UseCases\GetSaleCartDetailTable;
use App\Application\Sale\UseCases\GetSaleDetailsBySaleId;
use App\Application\Sale\UseCases\GetSaleDetailsTable;
use App\Application\Sale\UseCases\GetSalesByDateRange;
use App\Application\Sale\UseCases\UpdateSaleCart;
use App\Application\Sale\UseCases\UpdateSaleCartDetailByPrice;
use App\Application\Sale\UseCases\UpdateSaleCartDetailByQuantity;
use App\Http\Requests\Sales\AddCustomerSaleRequest;
use App\Http\Requests\Sales\CreateSaleRequest;
use App\Http\Requests\Sales\GetSalesRequest;
use App\Http\Requests\Sales\StoreSaleCartDetailRequest;
use App\Http\Requests\Sales\UpdateSaleCartDetailByPriceRequest;
use App\Http\Requests\Sales\UpdateSaleCartDetailByQuantityRequest;
use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorSVG;

class SaleController extends Controller
{
    /**
     * Menampilkan halaman utama penjualan.
     */
    public function index(GetSalesRequest $request, GetSalesByDateRange $getSalesByDateRange): View|JsonResponse
    {
        $access = app(FindModuleAccessByRoute::class)->execute(
            roleId: user()->role_id,
            route: 'transactions.sales',
        );

        $sales = [];

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $sales = $getSalesByDateRange->execute(
                startDate: Carbon::parse($request->input('start_date')),
                endDate: Carbon::parse($request->input('end_date')),
            );
        }

        if ($request->ajax()) {
            return $this->jsonResponse(
                data: $sales,
                message: 'Data penjualan berhasil diambil.',
            );
        }

        return view(
            view: 'pages.sales.index',
            data: compact('access', 'sales'),
        );
    }

    /**
     * Create (insert) data penjualan.
     */
    public function store(CreateSaleRequest $request): RedirectResponse|JsonResponse
    {
        $sale = app(CreateSale::class)->execute(
            request: $request,
        );

        // Permintaan ajax: kirim url struk agar dibuka pada tab baru oleh halaman keranjang.
        if ($request->ajax()) {
            session()->flash('alert', [
                'status' => 'success',
                'message' => 'Transaksi penjualan berhasil diselesaikan.',
            ]);

            return $this->jsonResponse(
                data: [
                    'receipt_url' => route('transactions.sales.receipt', ['saleId' => $sale->id]),
                    'redirect_url' => route('transactions.sales'),
                ],
                status: 201,
                message: 'Transaksi penjualan berhasil diselesaikan.',
            );
        }

        return to_route('transactions.sales')->with('alert', [
            'status' => 'success',
            'message' => 'Transaksi penjualan berhasil diselesaikan.',
        ]);
    }

    /**
     * Menampilkan struk penjualan.
     */
    public function receipt(string $saleId): View
    {
        $sale = app(FindSaleById::class)->execute(
            id: $saleId,
        );

        abort_if($sale === null, 404, 'Data penjualan tidak ditemukan.');

        $details = app(GetSaleDetailsBySaleId::class)->execute(
            saleId: $saleId,
        );

        $generator = new BarcodeGeneratorSVG();

        $barcode = $generator->getBarcode(
            barcode: $sale->invoiceNumber,
            type: $generator::TYPE_CODE_128,
            height: 80,

        );

        return view(
            view: 'pages.sales.receipt',
            data: compact('sale', 'details', 'barcode'),
        );
    }

    /**
     * Cetak PDF
     *
     * @return void
     */
    public function pdf(string $saleId)
    {
        $sale = app(FindSaleById::class)
            ->execute(id: $saleId);

        abort_if(
            $sale === null,
            404,
            'Data penjualan tidak ditemukan.'
        );

        $details = app(GetSaleDetailsBySaleId::class)
            ->execute(saleId: $saleId);

        $pdf = DomPdf::loadView('pages.sales.pdf', [
            'sale' => $sale,
            'details' => $details,
        ])->setPaper('a5', 'portrait');

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-'.
                $sale->invoiceNumber.'.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    /**
     * Mengambil daftar detail penjualan untuk dataTables serverside.
     */
    public function details(Request $request, string $saleId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetSaleDetailsTable::class)->execute(
            saleId: $saleId,
        );
    }

    /**
     * Menampilkan halaman keranjang penjualan.
     */
    public function carts(Request $request): View|JsonResponse
    {
        $cart = app(FindSaleCartByUserId::class)->execute(
            userId: user()->id,
        );

        if ($cart === null) {
            $cart = app(CreateSaleCart::class)->execute();
        }

        if ($request->ajax()) {
            return $this->jsonResponse(
                data: $cart,
            );
        }

        return view(view: 'pages.sales.cart', data: compact('cart'));
    }

    /**
     * Hapus (batalkan) transaksi keranjang penjualan.
     */
    public function deleteCart(string $cartId): RedirectResponse
    {
        app(DeleteSaleCart::class)->execute(
            cartId: $cartId,
        );

        return to_route('transactions.sales')->with('alert', [
            'status' => 'success',
            'message' => 'Transaksi berhasil dibatalkan.',
        ]);
    }

    /**
     * Update transaksi keranjang penjualan.
     */
    public function updateCart(AddCustomerSaleRequest $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(UpdateSaleCart::class)->execute(
            cartId: $cartId,
            customerId: $request->validated('customer_id'),
        );

        return $this->jsonResponse(
            message: 'Pelanggan berhsail ditambahkan.'
        );
    }

    /**
     * Mengmabil daftar detail keranjang penjualan.
     */
    public function cartDetails(Request $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        return app(GetSaleCartDetailTable::class)->execute(
            cartId: $cartId,
        );
    }

    /**
     * Create (insert) data detail keranjang penjualan.
     */
    public function storeCartDetail(StoreSaleCartDetailRequest $request, string $cartId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(CreateSaleCartDetail::class)->execute(
            cartId: $cartId,
            productCode: $request->validated('product_code'),
        );

        return $this->jsonResponse(
            status: 201,
            message: 'Produk berhasil ditambahkan.'
        );
    }

    /**
     * Update detail keranjang penjualan berdasarkan quantity
     */
    public function updateCartDetailByQuantity(UpdateSaleCartDetailByQuantityRequest $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(UpdateSaleCartDetailByQuantity::class)->execute(
            request: $request,
            cartId: $cartId,
            cartDetailId: $cartDetailId,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil diperbarui.'
        );
    }

    /**
     * Update detail keranjang penjualan berdasarkan harga
     */
    public function updateCartDetailByPrice(UpdateSaleCartDetailByPriceRequest $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(UpdateSaleCartDetailByPrice::class)->execute(
            request: $request,
            cartId: $cartId,
            cartDetailId: $cartDetailId,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil diperbarui.'
        );
    }

    /**
     * Hapus detaill keranjang penjualan
     */
    public function deleteCartDetail(Request $request, string $cartId, string $cartDetailId): JsonResponse
    {
        if (! $request->ajax()) {
            abort(404);
        }

        app(DeleteSaleCartDetail::class)->execute(
            cartId: $cartId,
            cartDetailId: $cartDetailId,
        );

        return $this->jsonResponse(
            message: 'Produk berhasil dihapus.'
        );
    }
}
