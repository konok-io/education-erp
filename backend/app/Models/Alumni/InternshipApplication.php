<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternshipApplication extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'internship_applications';

    protected $fillable = [
        'uuid',
        'internship_id',
        'alumni_profile_id',
        'student_id',
        'applicant_name',
        'email',
        'phone',
        'resume',
        'cover_letter',
        'university',
        'degree',
        'current_year',
        'skills',
        'status',
        'employer_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_APPLIED = 'applied';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_INTERVIEW = 'interview';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    // ===================== RELATIONSHIPS =====================

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class, 'internship_id');
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
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }
}
