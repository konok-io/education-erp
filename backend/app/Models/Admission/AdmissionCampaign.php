<?php

declare(strict_types=1);

namespace App\Models\Admission;

use App\Models\Academic\AcademicLevel;
use App\Models\Academic\AcademicSession;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionCampaign extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'admission_campaigns';

    protected $fillable = [
        'uuid',
        'title',
        'title_bn',
        'academic_session_id',
        'academic_level_id',
        'program_id',
        'department_id',
        'application_fee',
        'late_fee',
        'start_date',
        'end_date',
        'result_date',
        'admission_date',
        'total_seats',
        'status',
        'description',
        'requirements',
        'eligibility_criteria',
        'banner_image',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'result_date' => 'date',
        'admission_date' => 'date',
        'application_fee' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'total_seats' => 'integer',
        'is_active' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ARCHIVED = 'archived';

    // ===================== RELATIONSHIPS =====================

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(AdmissionApplication::class, 'campaign_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', '!=', self::STATUS_ARCHIVED);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_OPEN => 'Open',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN && now()->between($this->start_date, $this->end_date);
    }

    public function getApplicationCount(): int
    {
        return $this->applications()->count();
    }

    public function getMeritCount(): int
    {
        return $this->applications()->where('status', 'merit')->count();
    }
}
