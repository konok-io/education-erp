<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'uuid',
        'sku',
        'barcode',
        'qr_code',
        'name',
        'short_name',
        'name_bn',
        'category_id',
        'brand_id',
        'unit_id',
        'model',
        'description',
        'specifications',
        'image',
        'cost_price',
        'selling_price',
        'min_stock',
        'max_stock',
        'reorder_level',
        'current_stock',
        'weight',
        'dimensions',
        'color',
        'size',
        'is_trackable',
        'is_sellable',
        'is_purchasable',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'is_trackable' => 'boolean',
        'is_sellable' => 'boolean',
        'is_purchasable' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'reorder_level');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%");
        });
    }

    // ===================== METHODS =====================

    public static function generateSKU(): string
    {
        $prefix = 'PRD';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public static function generateBarcode(): string
    {
        return 'INV' . Str::random(12);
    }

    public static function generateQRCode(): string
    {
        return Str::uuid()->toString();
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function addStock(float $quantity): void
    {
        $this->increment('current_stock', $quantity);
    }

    public function removeStock(float $quantity): bool
    {
        if ($this->current_stock < $quantity) {
            return false;
        }
        $this->decrement('current_stock', $quantity);
        return true;
    }
}
