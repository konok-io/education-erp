<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryProduct extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'inventory_products';

    protected $fillable = [
        'uuid', 'sku', 'barcode', 'qr_code', 'product_name', 'description',
        'category_id', 'brand_id', 'unit_id', 'warehouse_id', 'minimum_stock',
        'maximum_stock', 'current_stock', 'purchase_price', 'average_cost',
        'selling_price', 'currency', 'image', 'specifications', 'is_trackable',
        'is_serialized', 'has_expiry', 'is_featured', 'is_active', 'created_by',
    ];

    protected $casts = [
        'specifications' => 'array',
        'purchase_price' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'current_stock' => 'integer',
        'is_trackable' => 'boolean',
        'is_serialized' => 'boolean',
        'has_expiry' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(InventoryBrand::class, 'brand_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(InventoryUnit::class, 'unit_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(InventoryStockAdjustment::class, 'product_id');
    }

    // ===================== SCOPES =====================

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', 0);
    }

    // ===================== METHODS =====================

    public static function generateSku(): string
    {
        $prefix = 'SKU';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function incrementStock(int $quantity): void
    {
        $this->increment('current_stock', $quantity);
    }

    public function decrementStock(int $quantity): void
    {
        if ($this->current_stock >= $quantity) {
            $this->decrement('current_stock', $quantity);
        }
    }
}
