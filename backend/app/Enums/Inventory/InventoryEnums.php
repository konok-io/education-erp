<?php

declare(strict_types=1);

namespace App\Enums\Inventory;

enum InventoryType: string
{
    case FURNITURE = 'furniture';
    case ELECTRONICS = 'electronics';
    case STATIONERY = 'stationery';
    case LAB_EQUIPMENT = 'lab_equipment';
    case SPORTS = 'sports';
    case AUDIO_VISUAL = 'audio_visual';
    case IT_EQUIPMENT = 'it_equipment';
    case MAINTENANCE = 'maintenance';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::FURNITURE => 'Furniture',
            self::ELECTRONICS => 'Electronics',
            self::STATIONERY => 'Stationery',
            self::LAB_EQUIPMENT => 'Lab Equipment',
            self::SPORTS => 'Sports Equipment',
            self::AUDIO_VISUAL => 'Audio Visual',
            self::IT_EQUIPMENT => 'IT Equipment',
            self::MAINTENANCE => 'Maintenance',
            self::OTHER => 'Other',
        };
    }
}

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

enum PurchaseStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case ORDERED = 'ordered';
    case RECEIVED = 'received';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::ORDERED => 'Ordered',
            self::RECEIVED => 'Received',
            self::REJECTED => 'Rejected',
            self::CANCELLED => 'Cancelled',
        };
    }
}
