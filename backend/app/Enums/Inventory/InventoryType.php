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
