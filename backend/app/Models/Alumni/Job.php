<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'jobs';

    protected $fillable = [
        'uuid',
        'job_number',
        'employer_id',
        'job_title',
        'description',
        'job_type',
        'department',
        'designation',
        'location',
        'country',
        'city',
        'work_type',
        'vacancy',
        'requirements',
        'responsibilities',
        'benefits',
        'experience_required',
        'education_required',
        'skills_required',
        'min_salary',
        'max_salary',
        'salary_currency',
        'salary_frequency',
        'application_deadline',
        'start_date',
        'is_featured',
        'is_active',
        'status',
        'posted_by',
        'published_at',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'vacancy' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'application_deadline' => 'date',
        'start_date' => 'date',
        'published_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_FILLED = 'filled';
    public const STATUS_DRAFT = 'draft';

    // ===================== JOB TYPES =====================
    public const TYPE_FULL_TIME = 'full_time';
    public const TYPE_PART_TIME = 'part_time';
    public const TYPE_CONTRACT = 'contract';
    public const TYPE_INTERNSHIP = 'internship';
    public const TYPE_REMOTE = 'remote';
    public const TYPE_GOVERNMENT = 'government';
    public const TYPE_PRIVATE = 'private';

    // ===================== WORK TYPES =====================
    public const WORK_ON_SITE = 'on_site';
    public const WORK_REMOTE = 'remote';
    public const WORK_HYBRID = 'hybrid';

    // ===================== RELATIONSHIPS =====================

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'posted_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class, 'job_id');
    }

    // ===================== SCOPES =====================

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExpired($query)
    {
        return $query->where('application_deadline', '<', now());
    }

    // ===================== METHODS =====================

    public static function generateJobNumber(): string
    {
        $prefix = 'JOB';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function jobTypes(): array
    {
        return [
            self::TYPE_FULL_TIME => 'Full Time',
            self::TYPE_PART_TIME => 'Part Time',
            self::TYPE_CONTRACT => 'Contract',
            self::TYPE_INTERNSHIP => 'Internship',
            self::TYPE_REMOTE => 'Remote',
            self::TYPE_GOVERNMENT => 'Government',
            self::TYPE_PRIVATE => 'Private',
        ];
    }

    public static function workTypes(): array
    {
        return [
            self::WORK_ON_SITE => 'On Site',
            self::WORK_REMOTE => 'Remote',
            self::WORK_HYBRID => 'Hybrid',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_FILLED => 'Filled',
            self::STATUS_DRAFT => 'Draft',
        ];
    }

    public function isExpired(): bool
    {
        return $this->application_deadline && $this->application_deadline < now();
    }

    public function publish(): void
    {
        $this->update([
            'status' => self::STATUS_OPEN,
            'published_at' => now(),
        ]);
    }

    public function close(): void
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }
}
