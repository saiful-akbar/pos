<?php

declare(strict_types=1);

namespace App\Domain\Cash\Enums;

enum CashSourceTypeEnum: string
{
    case SALE = 'sale';
    case PURCHASE_PAYMENT = 'purchase_payment';
    case SALE_RETURN = 'sale_return';
}
