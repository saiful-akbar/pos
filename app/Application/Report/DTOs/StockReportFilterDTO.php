<?php

declare(strict_types=1);

namespace App\Application\Report\DTOs;

use App\Base\DTO;
use Override;

class StockReportFilterDTO extends DTO
{
    /**
     * Status rekonsiliasi yang valid.
     */
    public const STATUSES = ['match', 'negative', 'positive', 'unavailable'];

    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly int $year,
        public readonly int $month,
        public readonly ?string $categoryId = null,
        public readonly ?string $keyword = null,
        public readonly ?string $status = null,
    ) {}

    /**
     * Awal periode (tanggal 1).
     */
    public function periodStart(): string
    {
        return sprintf('%04d-%02d-01', $this->year, $this->month);
    }

    /**
     * Akhir periode (tanggal terakhir bulan).
     */
    public function periodEnd(): string
    {
        return date('Y-m-t', strtotime($this->periodStart()));
    }

    /**
     * Tahun periode berikutnya (untuk stok akhir aktual).
     */
    public function nextYear(): int
    {
        return $this->month === 12 ? $this->year + 1 : $this->year;
    }

    /**
     * Bulan periode berikutnya (untuk stok akhir aktual).
     */
    public function nextMonth(): int
    {
        return $this->month === 12 ? 1 : $this->month + 1;
    }

    /**
     * Salinan filter tanpa status (untuk ringkasan sebaran status).
     */
    public function withoutStatus(): self
    {
        return new self(
            year: $this->year,
            month: $this->month,
            categoryId: $this->categoryId,
            keyword: $this->keyword,
            status: null,
        );
    }

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'category_id' => $this->categoryId,
            'keyword' => $this->keyword,
            'status' => $this->status,
        ];
    }
}
