<?php

declare(strict_types=1);

namespace App\Models\Transport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportAllocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transport_allocations';

    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'uuid',
        'allocation_no',
        'student_id',
        'route_id',
        'pickup_stop_id',
        'drop_stop_id',
        'seat_number',
        'monthly_fee',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_fee' => 'decimal:2',
    ];

    public static function generateAllocationNo(): string
    {
        $prefix = 'TA';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->allocation_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function pickupStop(): BelongsTo
    {
        return $this->belongsTo(TransportStop::class, 'pickup_stop_id');
    }

    public function dropStop(): BelongsTo
    {
        return $this->belongsTo(TransportStop::class, 'drop_stop_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && (!$this->end_date || $this->end_date >= now()->toDateString());
    }

    public function pause(): void
    {
        $this->update(['status' => self::STATUS_PAUSED]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }
}
