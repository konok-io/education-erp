<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'job_applications';

    protected $fillable = [
        'uuid',
        'job_id',
        'alumni_profile_id',
        'student_id',
        'applicant_name',
        'email',
        'phone',
        'resume',
        'cover_letter',
        'portfolio_link',
        'linkedin',
        'experience_summary',
        'skills',
        'expected_salary',
        'current_company',
        'current_designation',
        'status',
        'employer_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'reviewed_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_APPLIED = 'applied';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_INTERVIEW = 'interview';
    public const STATUS_OFFERED = 'offered';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    // ===================== RELATIONSHIPS =====================

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function alumniProfile(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'alumni_profile_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_APPLIED => 'Applied',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_INTERVIEW => 'Interview',
            self::STATUS_OFFERED => 'Offered',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function shortlist(): void
    {
        $this->update(['status' => self::STATUS_SHORTLISTED]);
    }

    public function scheduleInterview(): void
    {
        $this->update(['status' => self::STATUS_INTERVIEW]);
    }

    public function offer(): void
    {
        $this->update(['status' => self::STATUS_OFFERED]);
    }

    public function accept(): void
    {
        $this->update(['status' => self::STATUS_ACCEPTED]);
    }

    public function reject(?string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
        ]);
    }
}
