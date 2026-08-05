<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryWarehouse extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_warehouses';

    protected $fillable = [
        'uuid', 'warehouse_code', 'warehouse_name', 'description', 'address',
        'city', 'country', 'phone', 'email', 'manager_id', 'capacity',
        'current_stock', 'is_default', 'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'current_stock' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function manager(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(InventoryProduct::class, 'warehouse_id');
    }

    // ===================== METHODS =====================

    public static function generateWarehouseCode(): string
    {
        return 'WH-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    public function isFull(): bool
    {
        return $this->capacity && $this->current_stock >= $this->capacity;
    }

    public function getAvailableCapacity(): int
    {
        return $this->capacity ? max(0, $this->capacity - $this->current_stock) : 0;
    }
}
