<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'warehouses';

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'type',
        'address',
        'building',
        'floor',
        'manager_name',
        'phone',
        'email',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ===================== WAREHOUSE TYPES =====================
    public const TYPE_MAIN = 'main';
    public const TYPE_DEPARTMENT = 'department';
    public const TYPE_IT = 'it';
    public const TYPE_LIBRARY = 'library';
    public const TYPE_LABORATORY = 'laboratory';

    // ===================== RELATIONSHIPS =====================

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'warehouse_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'warehouse_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'warehouse_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMain($query)
    {
        return $query->where('type', self::TYPE_MAIN);
    }

    // ===================== METHODS =====================

    public static function types(): array
    {
        return [
            self::TYPE_MAIN => 'Main Store',
            self::TYPE_DEPARTMENT => 'Department Store',
            self::TYPE_IT => 'IT Store',
            self::TYPE_LIBRARY => 'Library Store',
            self::TYPE_LABORATORY => 'Laboratory Store',
        ];
    }
}
