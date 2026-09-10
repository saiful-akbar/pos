<?php

declare(strict_types=1);

namespace App\Domain\Purchase\Enums;

enum PaymentMethodEnum: string
{
    case CASH = 'cash';
    case QRIS = 'qris';
    case TRANSFER = 'transfer';
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}
