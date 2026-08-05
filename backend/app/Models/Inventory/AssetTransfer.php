<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetTransfer extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'asset_transfers';

    protected $fillable = [
        'uuid',
        'transfer_no',
        'asset_id',
        'from_holder_type',
        'from_holder_id',
        'from_holder_name',
        'to_holder_type',
        'to_holder_id',
        'to_holder_name',
        'from_location',
        'to_location',
        'transfer_date',
        'reason',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'transferred_by',
        'transferred_at',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'approved_at' => 'datetime',
        'transferred_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    // ===================== RELATIONSHIPS =====================

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'transferred_by');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ===================== METHODS =====================

    public static function generateTransferNo(): string
    {
        $prefix = 'AT';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => self::STATUS_REJECTED]);
    }

    public function complete(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'transferred_by' => $userId,
            'transferred_at' => now(),
        ]);

        // Update asset allocation
        $this->asset->update([
            'assigned_to_type' => $this->to_holder_type,
            'assigned_to_id' => $this->to_holder_id,
            'assigned_to_name' => $this->to_holder_name,
            'location' => $this->to_location,
        ]);
    }
}
