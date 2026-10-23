<?php

declare(strict_types=1);

namespace App\Application\Cash\DTOs;

use App\Base\DTO;
use App\Domain\Cash\Enums\CashTransactionTypeEnum;
use DateTimeInterface;
use Override;

class CashReportFilterDTO extends DTO
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly DateTimeInterface $startDate,
        public readonly DateTimeInterface $endDate,
        public readonly ?CashTransactionTypeEnum $type = null,
        public readonly ?string $userId = null,
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
            'type' => $this->type?->value,
            'user_id' => $this->userId,
        ];
    }
}
