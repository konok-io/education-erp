<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelMessSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_mess_subscriptions';

    const TYPE_MONTHLY = 'monthly';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_DAILY = 'daily';
    const TYPE_CUSTOM = 'custom';

    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'uuid',
        'subscription_no',
        'student_id',
        'building_id',
        'mess_plan_id',
        'start_date',
        'end_date',
        'monthly_fee',
        'subscription_type',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'monthly_fee' => 'decimal:2',
    ];

    public static function generateSubscriptionNo(): string
    {
        $prefix = 'HMS';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->subscription_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(HostelBuilding::class, 'building_id');
    }

    public function messPlan(): BelongsTo
    {
        return $this->belongsTo(HostelMessPlan::class, 'mess_plan_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->end_date >= now()->toDateString();
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
