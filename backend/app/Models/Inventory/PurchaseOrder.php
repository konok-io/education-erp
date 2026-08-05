<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'uuid',
        'po_no',
        'supplier_id',
        'purchase_request_id',
        'warehouse_id',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'payment_terms',
        'delivery_terms',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'vat_percent',
        'vat_amount',
        'shipping_cost',
        'total',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_percent' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ORDERED = 'ordered';
    public const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function goodsReceivedNotes(): HasMany
    {
        return $this->hasMany(GoodsReceivedNote::class, 'purchase_order_id');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_RECEIVED]);
    }

    // ===================== METHODS =====================

    public static function generatePONo(): string
    {
        $prefix = 'PO';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum('total');
        $discountAmount = $subtotal * ($this->discount_percent / 100);
        $afterDiscount = $subtotal - $discountAmount;
        $vatAmount = $afterDiscount * ($this->vat_percent / 100);
        
        $this->update([
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'vat_amount' => $vatAmount,
            'total' => $afterDiscount + $vatAmount + $this->shipping_cost,
        ]);
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function isFullyReceived(): bool
    {
        $totalOrdered = $this->items->sum('ordered_quantity');
        $totalReceived = $this->items->sum('received_quantity');
        return $totalReceived >= $totalOrdered;
    }
}
