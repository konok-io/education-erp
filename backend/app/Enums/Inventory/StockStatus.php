<?php

declare(strict_types=1);

namespace App\Enums\Inventory;

enum StockStatus: string
{
    case IN_STOCK = 'in_stock';
    case LOW_STOCK = 'low_stock';
    case OUT_OF_STOCK = 'out_of_stock';
    case REORDERED = 'reordered';
    case DISCONTINUED = 'discontinued';

    public function label(): string
    {
        return match($this) {
            self::IN_STOCK => 'In Stock',
            self::LOW_STOCK => 'Low Stock',
            self::OUT_OF_STOCK => 'Out of Stock',
            self::REORDERED => 'Reordered',
            self::DISCONTINUED => 'Discontinued',
        };
    }
}
