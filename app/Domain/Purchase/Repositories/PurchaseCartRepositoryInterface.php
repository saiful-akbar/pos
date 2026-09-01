<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Repositories;

use App\Application\Purchase\DTOs\AddProductPurchaseDTO;
use App\Domain\Purchase\Entities\PurchaseDetailEntity;
use App\Domain\Purchase\Entities\PurchaseEntity;

interface PurchaseCartRepositoryInterface
{
    /**
     * Mengambil data keranjang pembelian.
     */
    public function find(): ?PurchaseEntity;

    /**
     * Mengambil data keranjang pembelian dengan detail produknya.
     */
    public function findWithDetails(): ?PurchaseDetailEntity;

    /**
     * Membuat purchase cart baru.
     */
    public function create(string $userId): PurchaseDetailEntity;

    /**
     * Menambahkan produk ke details
     */
    public function addProduct(AddProductPurchaseDTO $dto): void;

    /**
     * Update total amount
     */
    public function updateTotalAmount(): void;
}
