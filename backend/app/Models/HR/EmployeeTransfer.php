<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Academic\Department;
use App\Models\Employee\Designation;
use App\Models\Employee\Employee;
use App\Models\Employee\Shift;
use App\Models\Campus;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTransfer extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_transfers';

    protected $fillable = [
        'uuid',
        'transfer_no',
        'employee_id',
        'from_department_id',
        'to_department_id',
        'from_designation_id',
        'to_designation_id',
        'from_campus_id',
        'to_campus_id',
        'from_shift_id',
        'to_shift_id',
        'reporting_manager_id',
        'transfer_date',
        'effective_date',
        'transfer_type',
        'reason',
        'remarks',
        'status',
        'recommended_by',
        'recommended_date',
        'approved_by',
        'approved_date',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'effective_date' => 'date',
        'recommended_date' => 'datetime',
        'approved_date' => 'datetime',
    ];

    // ===================== TYPES =====================
    public const TYPE_DEPARTMENT = 'department';
    public const TYPE_CAMPUS = 'campus';
    public const TYPE_DESIGNATION = 'designation';
    public const TYPE_SHIFT = 'shift';
    public const TYPE_REPORTING_MANAGER = 'reporting_manager';
    public const TYPE_COMBINED = 'combined';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_RECOMMENDED = 'recommended';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromDesignation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'from_designation_id');
    }

    public function toDesignation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'to_designation_id');
    }

    public function fromCampus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'from_campus_id');
    }

    public function toCampus(): BelongsTo
    {
        return $this->belongsTo(Campus::class, 'to_campus_id');
    }

    public function fromShift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'from_shift_id');
    }

    public function toShift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'to_shift_id');
    }

    public function reportingManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reporting_manager_id');
    }

    public function recommendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommended_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ===================== METHODS =====================

    public static function generateTransferNo(): string
    {
        $prefix = 'TRF';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function types(): array
    {
        return [
            self::TYPE_DEPARTMENT => 'Department Transfer',
            self::TYPE_CAMPUS => 'Campus Transfer',
            self::TYPE_DESIGNATION => 'Designation Change',
            self::TYPE_SHIFT => 'Shift Change',
            self::TYPE_REPORTING_MANAGER => 'Reporting Manager Change',
            self::TYPE_COMBINED => 'Combined Transfer',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_RECOMMENDED => 'Recommended',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getTransferSummaryAttribute(): string
    {
        $changes = [];

        if ($this->from_department_id !== $this->to_department_id) {
            $changes[] = 'Department';
        }
        if ($this->from_designation_id !== $this->to_designation_id) {
            $changes[] = 'Designation';
        }
        if ($this->from_campus_id !== $this->to_campus_id) {
            $changes[] = 'Campus';
        }
        if ($this->from_shift_id !== $this->to_shift_id) {
            $changes[] = 'Shift';
        }
        if ($this->reporting_manager_id) {
            $changes[] = 'Reporting Manager';
        }

        return implode(', ', $changes);
    }
}
