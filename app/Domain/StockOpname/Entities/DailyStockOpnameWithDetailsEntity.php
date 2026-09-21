<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Entities;

use App\Base\Entity;
use App\Domain\StockOpname\ValueObjects\UserStockOpnameValue;
use DateTimeInterface;
use InvalidArgumentException;
use Override;

class DailyStockOpnameWithDetailsEntity extends Entity
{
    /**
     * ID.
     */
    public readonly string $id;

    /**
     * Data user yang melakukan input stock.
     */
    public readonly UserStockOpnameValue $user;

    /**
     * Tanggal stock opame.
     */
    public readonly DateTimeInterface $date;

    /**
     * Total produk.
     */
    public readonly float|int $totalProduct;

    /**
     * Total stok system (book balance).
     */
    public readonly float|int $totalSystemStock;

    /**
     * Total stok fisik.
     */
    public readonly float|int $totalPhysicalStock;

    /**
     * Total selisih (stok fisik - stok system).
     */
    public readonly float|int $totalDifference;

    /**
     * Daftar produk dan quantity
     *
     * @var DailyStockOpnameDetailEntity[]
     */
    public readonly array $details;

    /**
     * Waktu stock opname dibuat.
     */
    public readonly ?DateTimeInterface $createdAt;

    /**
     * Waktu stock opname diperbarui.
     */
    public readonly ?DateTimeInterface $updatedAt;

    /**
     * Waktu stock opname dihapus.
     */
    public readonly ?DateTimeInterface $deletedAt;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $id,
        UserStockOpnameValue $user,
        DateTimeInterface $date,
        float|int $totalProduct,
        float|int $totalSystemStock,
        float|int $totalPhysicalStock,
        float|int $totalDifference,
        array $details,
        ?DateTimeInterface $createdAt,
        ?DateTimeInterface $updatedAt,
        ?DateTimeInterface $deletedAt,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->date = $date;
        $this->totalProduct = $totalProduct;
        $this->totalSystemStock = $totalSystemStock;
        $this->totalPhysicalStock = $totalPhysicalStock;
        $this->totalDifference = $totalDifference;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deletedAt = $deletedAt;
        $this->details = $this->detailsValidator($details);
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->toArray(),
            'date' => $this->date->format('Y-m-d H:i:s'),
            'total_product' => $this->totalProduct,
            'total_system_stock' => $this->totalSystemStock,
            'total_physical_stock' => $this->totalPhysicalStock,
            'total_difference' => $this->totalDifference,
            'details' => array_map(fn ($detail) => $detail->toArray(), $this->details),
            'created_at' => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Validasi properti details.
     *
     * @return DailyStockOpnameDetailEntity[]
     */
    private function detailsValidator(array $details): array
    {
        $result = [];

        foreach ($details as $detail) {
            if ($detail instanceof DailyStockOpnameDetailEntity) {
                $result[] = $detail;

                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Argumen detail harus merupakan instansiasi dari %s',
                    DailyStockOpnameDetailEntity::class,
                ),
            );
        }

        return $result;
    }
}
