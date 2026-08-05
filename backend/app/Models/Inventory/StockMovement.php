<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'stock_movements';

    protected $fillable = [
        'uuid',
        'movement_no',
        'product_id',
        'warehouse_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'movement_type',
        'quantity',
        'opening_stock',
        'closing_stock',
        'unit_cost',
        'total_cost',
        'movement_date',
        'reference_type',
        'reference_id',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'opening_stock' => 'decimal:2',
        'closing_stock' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'movement_date' => 'date',
    ];

    // ===================== MOVEMENT TYPES =====================
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_SALE = 'sale';
    public const TYPE_STOCK_IN = 'stock_in';
    public const TYPE_STOCK_OUT = 'stock_out';
    public const TYPE_TRANSFER_IN = 'transfer_in';
    public const TYPE_TRANSFER_OUT = 'transfer_out';
    public const TYPE_ADJUSTMENT_IN = 'adjustment_in';
    public const TYPE_ADJUSTMENT_OUT = 'adjustment_out';
    public const TYPE_RETURN_IN = 'return_in';
    public const TYPE_RETURN_OUT = 'return_out';
    public const TYPE_DAMAGE = 'damage';
    public const TYPE_LOSS = 'loss';

    // ===================== RELATIONSHIPS =====================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    public function scopeStockIn($query)
    {
        return $query->whereIn('movement_type', [
            self::TYPE_PURCHASE,
            self::TYPE_STOCK_IN,
            self::TYPE_TRANSFER_IN,
            self::TYPE_ADJUSTMENT_IN,
            self::TYPE_RETURN_IN,
        ]);
    }

    public function scopeStockOut($query)
    {
        return $query->whereIn('movement_type', [
            self::TYPE_SALE,
            self::TYPE_STOCK_OUT,
            self::TYPE_TRANSFER_OUT,
            self::TYPE_ADJUSTMENT_OUT,
            self::TYPE_RETURN_OUT,
            self::TYPE_DAMAGE,
            self::TYPE_LOSS,
        ]);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('movement_date', [$from, $to]);
    }

    // ===================== METHODS =====================

    public static function generateMovementNo(): string
    {
        $prefix = 'SM';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public function isIncoming(): bool
    {
        return in_array($this->movement_type, [
            self::TYPE_PURCHASE,
            self::TYPE_STOCK_IN,
            self::TYPE_TRANSFER_IN,
            self::TYPE_ADJUSTMENT_IN,
            self::TYPE_RETURN_IN,
        ]);
    }

    public function isOutgoing(): bool
    {
        return in_array($this->movement_type, [
            self::TYPE_SALE,
            self::TYPE_STOCK_OUT,
            self::TYPE_TRANSFER_OUT,
            self::TYPE_ADJUSTMENT_OUT,
            self::TYPE_RETURN_OUT,
            self::TYPE_DAMAGE,
            self::TYPE_LOSS,
        ]);
    }
}
