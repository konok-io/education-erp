<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_locations';

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'type',
        'address',
        'parent_id',
        'manager_id',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(InventoryLocation::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'manager_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'location_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'location_id');
    }
}
