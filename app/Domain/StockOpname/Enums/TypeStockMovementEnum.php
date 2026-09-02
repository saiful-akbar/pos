<?php

declare(strict_types=1);

namespace App\Domain\StockOpname\Enums;

enum TypeStockMovementEnum: string
{
    case PURCHASE = 'purchase';
    case PURCHASE_RETURN = 'purchase_return';
    case SALE = 'sale';
    case SALE_RETURN = 'sale_return';
    case ADJUSTMENT = 'adjustment';
    case STOCK_OPNAME = 'stock_opname';
}
