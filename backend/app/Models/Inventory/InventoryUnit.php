<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryUnit extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_units';

    protected $fillable = [
        'uuid', 'unit_code', 'unit_name', 'short_name', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== METHODS =====================

    public static function generateUnitCode(): string
    {
        return 'U-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    public static function defaultUnits(): array
    {
        return [
            'piece' => 'Piece',
            'box' => 'Box',
            'packet' => 'Packet',
            'dozen' => 'Dozen',
            'kg' => 'Kilogram',
            'g' => 'Gram',
            'l' => 'Liter',
            'ml' => 'Milliliter',
            'm' => 'Meter',
            'set' => 'Set',
            'bundle' => 'Bundle',
        ];
    }
}
