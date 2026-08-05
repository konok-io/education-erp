<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLeave extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_leaves';

    protected $fillable = [
        'uuid',
        'employeeable_type',
        'employeeable_id',
        'leave_type',
        'start_date',
        'end_date',
        'total_days',
        'reason',
        'status',
        'applied_by',
        'applied_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_days' => 'integer',
        'applied_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public const TYPE_CASUAL = 'casual';
    public const TYPE_MEDICAL = 'medical';
    public const TYPE_ANNUAL = 'annual';
    public const TYPE_MATERNITY = 'maternity';
    public const TYPE_PATERNITY = 'paternity';
    public const TYPE_SPECIAL = 'special';
    public const TYPE_WITHOUT_PAY = 'without_pay';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    public function employeeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public static function leaveTypes(): array
    {
        return [
            self::TYPE_CASUAL => 'Casual Leave',
            self::TYPE_MEDICAL => 'Medical Leave',
            self::TYPE_ANNUAL => 'Annual Leave',
            self::TYPE_MATERNITY => 'Maternity Leave',
            self::TYPE_PATERNITY => 'Paternity Leave',
            self::TYPE_SPECIAL => 'Special Leave',
            self::TYPE_WITHOUT_PAY => 'Leave Without Pay',
        ];
    }
}
