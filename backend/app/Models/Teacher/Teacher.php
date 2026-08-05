<?php

declare(strict_types=1);

namespace App\Models\Teacher;

use App\Models\User;
use App\Models\Academic\Department;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'teachers';

    protected $fillable = [
        'uuid',
        'teacher_no',
        'user_id',
        'campus_id',
        'department_id',
        'designation_id',
        'employment_type',
        'joining_date',
        'confirmation_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'confirmation_date' => 'date',
    ];

    // ===================== EMPLOYMENT TYPES =====================
    public const TYPE_PERMANENT = 'permanent';
    public const TYPE_CONTRACTUAL = 'contractual';
    public const TYPE_PART_TIME = 'part_time';
    public const TYPE_GUEST = 'guest';
    public const TYPE_VISITING = 'visiting';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_ON_LEAVE = 'on_leave';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_RETIRED = 'retired';
    public const STATUS_RESIGNED = 'resigned';
    public const STATUS_TERMINATED = 'terminated';

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the user account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the profile.
     */
    public function profile(): MorphOne
    {
        return $this->morphOne(TeacherProfile::class, 'teacherable');
    }

    /**
     * Get the department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the qualifications.
     */
    public function qualifications(): HasMany
    {
        return $this->hasMany(TeacherQualification::class, 'teacher_id');
    }

    /**
     * Get the experiences.
     */
    public function experiences(): HasMany
    {
        return $this->hasMany(TeacherExperience::class, 'teacher_id');
    }

    /**
     * Get the documents.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(TeacherDocument::class, 'teacherable');
    }

    /**
     * Get the subject assignments.
     */
    public function subjectAssignments(): HasMany
    {
        return $this->hasMany(TeacherSubjectAssignment::class, 'teacher_id');
    }

    /**
     * Get the class assignments.
     */
    public function classAssignments(): HasMany
    {
        return $this->hasMany(TeacherClassAssignment::class, 'teacher_id');
    }

    /**
     * Get the salary profile.
     */
    public function salary(): MorphOne
    {
        return $this->morphOne(TeacherSalary::class, 'teacherable');
    }

    /**
     * Get the leave records.
     */
    public function leaves(): MorphMany
    {
        return $this->morphMany(TeacherLeave::class, 'teacherable');
    }

    /**
     * Get the campus.
     */
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
            $q->where('teacher_no', 'like', "%{$search}%")
                ->orWhereHas('profile', function ($pq) use ($search) {
                    $pq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('full_name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                });
        });
    }

    // ===================== METHODS =====================

    public static function generateTeacherNo(string $year): string
    {
        $prefix = 'TR';
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

    public function canTakeAttendance(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function canEnterResult(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public static function employmentTypes(): array
    {
        return [
            self::TYPE_PERMANENT => 'Permanent',
            self::TYPE_CONTRACTUAL => 'Contractual',
            self::TYPE_PART_TIME => 'Part Time',
            self::TYPE_GUEST => 'Guest Faculty',
            self::TYPE_VISITING => 'Visiting Faculty',
        ];
    }

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
