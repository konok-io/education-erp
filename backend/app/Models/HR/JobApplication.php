<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'job_applications';

    protected $fillable = [
        'uuid',
        'application_no',
        'job_circular_id',
        'full_name',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'nationality',
        'marital_status',
        'nid',
        'passport',
        'email',
        'mobile',
        'alternative_mobile',
        'present_address',
        'permanent_address',
        'photo',
        'cv',
        'cover_letter',
        'certificates',
        'experience_details',
        'education_details',
        'applicant_status',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'certificates' => 'array',
    ];

    // ===================== STATUS =====================
    public const STATUS_APPLIED = 'applied';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_INTERVIEW_SCHEDULED = 'interview_scheduled';
    public const STATUS_INTERVIEWED = 'interviewed';
    public const STATUS_SELECTED = 'selected';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_WAITING_LIST = 'waiting_list';
    public const STATUS_WITHDRAWN = 'withdrawn';

    // ===================== RELATIONSHIPS =====================

    public function jobCircular(): BelongsTo
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'job_application_id');
    }

    public function offerLetter(): HasOne
    {
        return $this->hasOne(OfferLetter::class, 'job_application_id');
    }

    // ===================== METHODS =====================

    public static function generateApplicationNo(): string
    {
        $prefix = 'APP';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%06d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_APPLIED => 'Applied',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::STATUS_INTERVIEWED => 'Interviewed',
            self::STATUS_SELECTED => 'Selected',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_WAITING_LIST => 'Waiting List',
            self::STATUS_WITHDRAWN => 'Withdrawn',
        ];
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function isSelected(): bool
    {
        return $this->applicant_status === self::STATUS_SELECTED;
    }

    public function isRejected(): bool
    {
        return $this->applicant_status === self::STATUS_REJECTED;
    }
}
