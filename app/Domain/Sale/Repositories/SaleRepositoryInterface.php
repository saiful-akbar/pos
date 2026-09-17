<?php

declare(strict_types=1);

namespace App\Domain\Sale\Repositories;

use App\Application\Sale\DTOs\CreateSaleDTO;
use App\Domain\Sale\Entities\SaleEntity;
use App\Domain\Sale\Entities\SaleWithDetailEntity;
use DateTimeInterface;

interface SaleRepositoryInterface
{
    /**
     * Membuat invoice number.
     */
    public function generateInvoiceNumber(): string;

    /**
     * Menyimpan (insert) data penjualan.
     */
    public function create(CreateSaleDTO $dto): SaleEntity;

    /**
     * Mengambil data sales berdsarkan id.
     */
    public function findById(string $id, string $responseType = SaleWithDetailEntity::class): SaleWithDetailEntity|SaleEntity|null;

    /**
     * Mengambil data penjualan berdasarkan nomor faktur.
     */
    public function findByInvoice(string $invoice): ?SaleEntity;

    /**
     * Mengambil daftar berdasarkan rendang tanggal transaksi.
     *
     * @return SaleEntity[]
     */
    public function getByDate(DateTimeInterface $fromDate, DateTimeInterface $toDate): array;
}
