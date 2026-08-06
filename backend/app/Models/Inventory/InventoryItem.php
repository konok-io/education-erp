<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_items';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DISCONTINUED = 'discontinued';

    protected $fillable = [
        'uuid',
        'item_code',
        'name',
        'name_bn',
        'barcode',
        'qr_code',
        'category_id',
        'unit',
        'brand',
        'model',
        'size',
        'color',
        'description',
        'purchase_price',
        'selling_price',
        'min_stock_level',
        'max_stock_level',
        'reorder_level',
        'opening_stock',
        'current_stock',
        'is_serialized',
        'is_taxable',
        'tax_rate',
        'status',
        'image',
        'specifications',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'min_stock_level' => 'decimal:2',
        'max_stock_level' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'opening_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'specifications' => 'array',
    ];

    public static function generateItemCode(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $lastItem = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastItem ? ((int) substr($lastItem->item_code, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'item_id');
    }

    public function serialNumbers(): HasMany
    {
        return $this->hasMany(ItemSerialNumber::class, 'item_id');
    }

    public function updateStock(float $quantity, string $type): void
    {
        if (in_array($type, ['purchase', 'transfer_in', 'adjustment_in', 'return_in'])) {
            $this->increment('current_stock', $quantity);
        } else {
            $this->decrement('current_stock', $quantity);
        }
    }

    public function isBelowReorderLevel(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }
}
