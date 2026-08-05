<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Models\User;
use App\Models\Academic\Department;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employees';

    protected $fillable = [
        'uuid',
        'employee_no',
        'user_id',
        'campus_id',
        'department_id',
        'designation_id',
        'employment_type_id',
        'salary_grade_id',
        'shift_id',
        'joining_date',
        'confirmation_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'confirmation_date' => 'date',
    ];

    // ===================== RELATIONSHIPS =====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profile(): MorphOne
    {
        return $this->morphOne(EmployeeProfile::class, 'employeeable');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    public function salaryGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'salary_grade_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(EmployeeDocument::class, 'employeeable');
    }

    public function emergencyContacts(): MorphMany
    {
        return $this->morphMany(EmployeeEmergencyContact::class, 'employeeable');
    }

    public function leaves(): MorphMany
    {
        return $this->morphMany(EmployeeLeave::class, 'employeeable');
    }

    public function salary(): MorphOne
    {
        return $this->morphOne(EmployeeSalary::class, 'employeeable');
    }

    public function campus(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Campus::class, 'campus_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeByDepartment($query, string $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('employee_no', 'like', "%{$search}%")
                ->orWhereHas('profile', function ($pq) use ($search) {
                    $pq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
        });
    }

    // ===================== METHODS =====================

    public static function generateEmployeeNo(string $year): string
    {
        $prefix = 'EMP';
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public function getFullNameAttribute(): ?string
    {
        return $this->profile?->full_name;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        $photo = $this->profile?->photo;
        return $photo ? asset('storage/' . $photo) : null;
    }

    public function canLogin(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // ===================== CONSTANTS =====================

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_ON_LEAVE = 'on_leave';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_RETIRED = 'retired';
    public const STATUS_RESIGNED = 'resigned';
    public const STATUS_TERMINATED = 'terminated';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ON_LEAVE => 'On Leave',
            self::STATUS_SUSPENDED => 'Suspended',
            self::STATUS_RETIRED => 'Retired',
            self::STATUS_RESIGNED => 'Resigned',
            self::STATUS_TERMINATED => 'Terminated',
        ];
    }
}
