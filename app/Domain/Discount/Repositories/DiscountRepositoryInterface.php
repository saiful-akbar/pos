<?php

declare(strict_types=1);

namespace App\Domain\Discount\Repositories;

use App\Application\Discount\DTOs\DiscountDTO;
use App\Domain\Discount\Entities\DiscountEntity;
use Illuminate\Database\Eloquent\Builder;

interface DiscountRepositoryInterface
{
    /**
     * Mengambil daftar diskon dalam format query builder
     */
    public function getAllQuery(): Builder;

    /**
     * Mengambil daftar discount hanya yang masih aktif
     * dalam format query builder.
     */
    public function getActiveQuery(): Builder;

    /**
     * Membuat (insert) diskon baru.
     */
    public function create(DiscountDTO $dto): DiscountEntity;

    /**
     * Memperbarui data diskon.
     */
    public function update(string $id, DiscountDTO $dto): int;

    /**
     * Mengambil 1 data diskon berdasarkan id.
     */
    public function findById(string $id): ?DiscountEntity;

    /**
     * hapus data diskon.
     */
    public function removeById(string $id): int;
}
