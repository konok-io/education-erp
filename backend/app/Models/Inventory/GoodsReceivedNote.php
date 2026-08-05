<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceivedNote extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'goods_received_notes';

    protected $fillable = [
        'uuid',
        'grn_no',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'received_date',
        'challan_no',
        'vehicle_no',
        'remarks',
        'total',
        'status',
        'received_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'received_date' => 'date',
        'total' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_RECEIVED = 'received';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_PARTIAL = 'partial';

    // ===================== RELATIONSHIPS =====================

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceivedNoteItem::class, 'goods_received_note_id');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_RECEIVED);
    }

    // ===================== METHODS =====================

    public static function generateGRNNo(): string
    {
        $prefix = 'GRN';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public function verify(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_VERIFIED,
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);
    }
}
