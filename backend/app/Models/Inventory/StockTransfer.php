<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_transfers';

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_RECEIVED = 'received';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'transfer_no',
        'from_location_id',
        'to_location_id',
        'requested_by',
        'approved_by',
        'transfer_date',
        'received_date',
        'status',
        'reason',
        'notes',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'received_date' => 'date',
    ];

    public static function generateTransferNo(): string
    {
        $prefix = 'ST';
        $year = date('Y');
        $month = date('m');
        $lastTransfer = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastTransfer ? ((int) substr($lastTransfer->transfer_no, -4)) + 1 : 1;
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'to_location_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class, 'transfer_id');
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SENT => 'Sent',
            self::STATUS_IN_TRANSIT => 'In Transit',
            self::STATUS_RECEIVED => 'Received',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
