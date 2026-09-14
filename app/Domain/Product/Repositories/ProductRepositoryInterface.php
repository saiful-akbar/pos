<?php

declare(strict_types=1);

namespace App\Domain\Product\Repositories;

use App\Application\Product\DTOs\CreateProductDTO;
use App\Application\Product\DTOs\UpdateProductDTO;
use App\Domain\Product\Entities\ProductDetailEntity;
use App\Domain\Product\Entities\ProductEntity;
use Illuminate\Database\Eloquent\Builder;

interface ProductRepositoryInterface
{
    /**
     * Mengambil daftar produk dalam format query builder.
     */
    public function getQuery(): Builder;

    /**
     * Mengambil daftar produk yang masih aktif dalam format query.
     */
    public function getActiveQuery(): Builder;

    /**
     * Mengambil data produk berdasarkan id.
     */
    public function findById(string $id, string $response = ProductDetailEntity::class): ProductDetailEntity|ProductEntity|null;

    /**
     * Membuat produk baru
     */
    public function create(CreateProductDTO $dto): ProductEntity;

    /**
     * Update produk.
     */
    public function update(UpdateProductDTO $dto): void;

    /**
     * Reset discount.
     */
    public function resetDiscount(int|float $discount): void;

    /**
     * Update gambar
     */
    public function updateImage(string $id, ?string $path = null): void;

    /**
     * Megambil data produk berdasarkan kode atau barcode.
     */
    public function findByCodeOrBarcode(string $value): ?ProductEntity;

    /**
     * Update stock product saat ini berdasarkan id.
     */
    public function updateCurrentStockById(string $id, int|float $stock): void;

    /**
     * Mengambil daftar produk aktif.
     *
     * @return ProductEntity[]
     */
    public function getActive(): array;

    /**
     * Menghapus data produk berdasarkan id.
     */
    public function removeById(string $id): int;
}
