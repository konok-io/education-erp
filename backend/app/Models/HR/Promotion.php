<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Models\Academic\Department;
use App\Models\Employee\Designation;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'promotions';

    protected $fillable = [
        'uuid',
        'promotion_no',
        'employee_id',
        'previous_department_id',
        'new_department_id',
        'previous_designation_id',
        'new_designation_id',
        'previous_grade_id',
        'new_grade_id',
        'previous_basic',
        'new_basic',
        'promotion_date',
        'effective_date',
        'status',
        'approved_by',
        'approved_at',
        'reason',
        'remarks',
    ];

    protected $casts = [
        'previous_basic' => 'decimal:2',
        'new_basic' => 'decimal:2',
        'promotion_date' => 'date',
        'effective_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function previousDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'previous_department_id');
    }

    public function newDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'new_department_id');
    }

    public function previousDesignation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'previous_designation_id');
    }

    public function newDesignation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'new_designation_id');
    }

    public function previousGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'previous_grade_id');
    }

    public function newGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'new_grade_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== METHODS =====================

    public static function generatePromotionNo(): string
    {
        $prefix = 'PRM';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }
}
