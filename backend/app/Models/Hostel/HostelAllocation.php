<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HostelAllocation extends Model
{
    use HasUuid;

    protected $table = 'hostel_allocations';

    protected $fillable = [
        'uuid',
        'allocation_no',
        'allocatable_type',
        'allocatable_id',
        'hostel_id',
        'building_id',
        'room_id',
        'bed_id',
        'check_in_date',
        'expected_checkout',
        'actual_checkout',
        'monthly_fee',
        'security_deposit',
        'total_paid',
        'status',
        'remarks',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'expected_checkout' => 'date',
        'actual_checkout' => 'date',
        'monthly_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CHECKED_OUT = 'checked_out';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function allocatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class, 'bed_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ===================== METHODS =====================

    public static function generateAllocationNo(): string
    {
        $prefix = 'HA';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereMonth('created_at', now()->month)->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_CHECKED_OUT => 'Checked Out',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        // Allocate bed
        if ($this->bed) {
            $this->bed->allocate($this->allocatable_type, $this->allocatable_id);
        }

        // Update room occupancy
        if ($this->room) {
            $this->room->updateOccupancy();
        }
    }

    public function checkIn(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'check_in_date' => now(),
        ]);
    }

    public function checkOut(): void
    {
        $this->update([
            'status' => self::STATUS_CHECKED_OUT,
            'actual_checkout' => now(),
        ]);

        // Free the bed
        if ($this->bed) {
            $this->bed->checkout();
        }

        // Update room occupancy
        if ($this->room) {
            $this->room->updateOccupancy();
        }
    }
}
