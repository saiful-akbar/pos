<?php

declare(strict_types=1);

namespace App\Application\Sale\UseCases;

use App\Domain\Sale\Repositories\SaleRepositoryInterface;
use App\Domain\Sale\Repositories\SaleReturnCartRepositoryInterface;

class GetSaleReturnCartInfo
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private SaleReturnCartRepositoryInterface $cartRepository,
        private SaleRepositoryInterface $saleRepository,
    ) {}

    /**
     * Execute method.
     *
     * @return array<string, mixed>
     */
    public function execute(string $cartId): array
    {
        // Ambil data keranjang pengembalian penjualan berdasarkan ID.
        $cart = $this->cartRepository->findById(
            id: $cartId,
        );

        if ($cart === null) {
            abort(404, 'Keranjang pengembalian penjualan tidak ditemukan.');
        }

        // Ambil data penjualan asli untuk mendapatkan pelanggan (member) dan tanggal transaksi penjualan.
        $sale = $this->saleRepository->findById(
            id: $cart->saleId,
        );

        return [
            'customer' => $sale?->customer?->toArray(),
            'sale_transaction_date' => $sale?->transactionDate->format('Y-m-d'),
            'total_quantity' => $cart->totalQuantity,
            'total_amount' => $cart->totalAmount,
            'discount' => $cart->discount,
            'grand_total' => $cart->grandTotal,
        ];
    }
}
