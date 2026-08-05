<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlumniActivity extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'alumni_activities';

    protected $fillable = [
        'uuid',
        'user_id',
        'activity_type',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // ===================== ACTIVITY TYPES =====================
    public const ACTIVITY_ALUMNI_REGISTERED = 'alumni_registered';
    public const ACTIVITY_ALUMNI_VERIFIED = 'alumni_verified';
    public const ACTIVITY_MEMBERSHIP_CREATED = 'membership_created';
    public const ACTIVITY_JOB_POSTED = 'job_posted';
    public const ACTIVITY_INTERNSHIP_POSTED = 'internship_posted';
    public const ACTIVITY_APPLICATION_SUBMITTED = 'application_submitted';
    public const ACTIVITY_PLACEMENT_COMPLETED = 'placement_completed';
    public const ACTIVITY_EVENT_CREATED = 'event_created';
    public const ACTIVITY_REGISTRATION_CREATED = 'registration_created';
    public const ACTIVITY_DONATION_RECEIVED = 'donation_received';
    public const ACTIVITY_MENTORSHIP_STARTED = 'mentorship_started';
    public const ACTIVITY_PROFILE_UPDATED = 'profile_updated';

    // ===================== RELATIONSHIPS =====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // ===================== METHODS =====================

    public static function activityTypes(): array
    {
        return [
            self::ACTIVITY_ALUMNI_REGISTERED => 'Alumni Registered',
            self::ACTIVITY_ALUMNI_VERIFIED => 'Alumni Verified',
            self::ACTIVITY_MEMBERSHIP_CREATED => 'Membership Created',
            self::ACTIVITY_JOB_POSTED => 'Job Posted',
            self::ACTIVITY_INTERNSHIP_POSTED => 'Internship Posted',
            self::ACTIVITY_APPLICATION_SUBMITTED => 'Application Submitted',
            self::ACTIVITY_PLACEMENT_COMPLETED => 'Placement Completed',
            self::ACTIVITY_EVENT_CREATED => 'Event Created',
            self::ACTIVITY_REGISTRATION_CREATED => 'Registration Created',
            self::ACTIVITY_DONATION_RECEIVED => 'Donation Received',
            self::ACTIVITY_MENTORSHIP_STARTED => 'Mentorship Started',
            self::ACTIVITY_PROFILE_UPDATED => 'Profile Updated',
        ];
    }

    public static function log(
        string $activityType,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        return self::create([
            'user_id' => auth()->id(),
            'activity_type' => $activityType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
