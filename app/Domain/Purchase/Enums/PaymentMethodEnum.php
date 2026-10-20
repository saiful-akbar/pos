<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Enums;

enum PaymentMethodEnum: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    case CREDIT = 'credit';
    case DEBIT = 'debit';
}
