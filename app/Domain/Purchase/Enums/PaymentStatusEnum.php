<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Enums;

enum PaymentStatusEnum: string
{
    case UNPAID = 'unpaid';
    case PARTIAL = 'partial';
    case PAID = 'paid';
}
