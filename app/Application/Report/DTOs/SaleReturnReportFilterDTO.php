<?php

declare(strict_types=1);

namespace App\Application\Report\DTOs;

use App\Base\DTO;
use DateTimeInterface;
use Override;

class SaleReturnReportFilterDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly DateTimeInterface $startDate,
        public readonly DateTimeInterface $endDate,
        public readonly ?string $returnNumber = null,
        public readonly ?string $invoiceNumber = null,
        public readonly ?string $userId = null,
        public readonly ?string $customerName = null,
        public readonly ?string $categoryId = null,
        public readonly ?string $productCode = null,
    ) {}

    /**
     * Konversi data object ke array.
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'return_number' => $this->returnNumber,
            'invoice_number' => $this->invoiceNumber,
            'user_id' => $this->userId,
            'customer_name' => $this->customerName,
            'category_id' => $this->categoryId,
            'product_code' => $this->productCode,
        ];
    }
}
