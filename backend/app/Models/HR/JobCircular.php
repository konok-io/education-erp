<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Academic\Department;
use App\Models\Employee\Designation;
use App\Models\Employee\EmploymentType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCircular extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'job_circulars';

    protected $fillable = [
        'uuid',
        'circular_no',
        'title',
        'title_bn',
        'description',
        'requirements',
        'benefits',
        'job_code',
        'department_id',
        'designation_id',
        'employment_type_id',
        'vacancy',
        'min_salary',
        'max_salary',
        'salary_range',
        'application_deadline',
        'published_date',
        'interview_date',
        'status',
        'is_active',
        'terms_conditions',
        'notes',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'application_deadline' => 'date',
        'published_date' => 'date',
        'interview_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

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

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_circular_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'job_circular_id');
    }

    public function offerLetters(): HasMany
    {
        return $this->hasMany(OfferLetter::class, 'job_circular_id');
    }

    // ===================== METHODS =====================

    public static function generateCircularNo(): string
    {
        $prefix = 'JC';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function getApplicationCountAttribute(): int
    {
        return $this->applications()->count();
    }

    public function getShortlistedCountAttribute(): int
    {
        return $this->applications()->where('applicant_status', JobApplication::STATUS_SHORTLISTED)->count();
    }
}
