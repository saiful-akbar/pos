<?php

declare(strict_types=1);

namespace App\Application\StockOpname\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class DailyStockOpnameDTO extends DTO
{
    /**
     * ID user
     */
    public readonly string $userId;

    /**
     * Tanggal stock opname
     */
    public readonly DateTimeInterface $date;

    /**
     * Total produk.
     */
    public readonly float|int $totalProduct;

    /**
     * Total stok system.
     */
    public readonly float|int $totalSystemStock;

    /**
     * Total stok fisik.
     */
    public readonly float|int $totalPhysicalStock;

    /**
     * Total selisih.
     */
    public readonly float|int $totalDifference;

    /**
     * Create a new class instance.
     */
    public function __construct(
        string $userId,
        DateTimeInterface $date,
        float|int $totalProduct,
        float|int $totalSystemStock,
        float|int $totalPhysicalStock,
    ) {
        $this->userId = $userId;
        $this->date = $date;
        $this->totalProduct = $totalProduct;
        $this->totalSystemStock = $totalSystemStock;
        $this->totalPhysicalStock = $totalPhysicalStock;
        $this->totalDifference = $totalPhysicalStock - $totalSystemStock;
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'date' => $this->date->format('Y-m-d H:i:s'),
            'total_product' => $this->totalProduct,
            'total_system_stock' => $this->totalSystemStock,
            'total_physical_stock' => $this->totalPhysicalStock,
            'total_difference' => $this->totalDifference,
        ];
    }
}
