<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelVisitor extends Model
{
    use HasUuid;

    protected $table = 'hostel_visitors';

    protected $fillable = [
        'uuid',
        'visitor_no',
        'visitor_name',
        'nid',
        'phone',
        'relation',
        'purpose',
        'hostel_id',
        'student_id',
        'student_name',
        'student_class',
        'student_roll',
        'visit_date',
        'check_in_time',
        'check_out_time',
        'remarks',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_CHECKED_IN = 'checked_in';
    public const STATUS_CHECKED_OUT = 'checked_out';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function hostel(): BelongsTo
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== SCOPES =====================

    public function scopeToday($query)
    {
        return $query->whereDate('visit_date', now()->toDateString());
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // ===================== METHODS =====================

    public static function generateVisitorNo(): string
    {
        $prefix = 'VIS';
        $date = now()->format('Ymd');
        $count = self::whereDate('visit_date', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%03d', $prefix, $date, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_CHECKED_IN => 'Checked In',
            self::STATUS_CHECKED_OUT => 'Checked Out',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function purposes(): array
    {
        return [
            'guardian' => 'Guardian Visit',
            'parent' => 'Parent Visit',
            'relative' => 'Relative Visit',
            'official' => 'Official Visit',
            'other' => 'Other',
        ];
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function checkIn(): void
    {
        $this->update([
            'status' => self::STATUS_CHECKED_IN,
            'check_in_time' => now(),
        ]);
    }

    public function checkOut(): void
    {
        $this->update([
            'status' => self::STATUS_CHECKED_OUT,
            'check_out_time' => now(),
        ]);
    }
}
