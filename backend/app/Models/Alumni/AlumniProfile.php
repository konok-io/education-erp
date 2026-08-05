<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AlumniProfile extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'alumni_profiles';

    protected $fillable = [
        'uuid',
        'membership_number',
        'student_id',
        'registration_number',
        'full_name',
        'email',
        'phone',
        'photo',
        'passing_year',
        'department',
        'program',
        'current_occupation',
        'current_organization',
        'designation',
        'country',
        'city',
        'address',
        'linkedin',
        'twitter',
        'facebook',
        'website',
        'bio',
        'skills',
        'education',
        'experience',
        'achievements',
        'employment_status',
        'current_salary',
        'salary_currency',
        'membership_type',
        'membership_start_date',
        'membership_end_date',
        'is_verified',
        'verification_token',
        'is_active',
        'status',
        'user_id',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'education' => 'array',
        'experience' => 'array',
        'achievements' => 'array',
        'current_salary' => 'decimal:2',
        'membership_start_date' => 'date',
        'membership_end_date' => 'date',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_SUSPENDED = 'suspended';

    // ===================== MEMBERSHIP TYPES =====================
    public const MEMBERSHIP_LIFETIME = 'lifetime';
    public const MEMBERSHIP_ANNUAL = 'annual';
    public const MEMBERSHIP_PREMIUM = 'premium';
    public const MEMBERSHIP_HONORARY = 'honorary';
    public const MEMBERSHIP_CORPORATE = 'corporate';

    // ===================== EMPLOYMENT STATUS =====================
    public const EMPLOYED = 'employed';
    public const SELF_EMPLOYED = 'self_employed';
    public const UNEMPLOYED = 'unemployed';
    public const STUDENT = 'student';
    public const RETIRED = 'retired';

    // ===================== RELATIONSHIPS =====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'verified_by');
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'alumni_profile_id');
    }

    public function placements(): HasMany
    {
        return $this->hasMany(Placement::class, 'alumni_profile_id');
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'alumni_profile_id');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'alumni_profile_id');
    }

    // ===================== SCOPES =====================

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // ===================== METHODS =====================

    public static function generateMembershipNumber(): string
    {
        $prefix = 'ALM';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function generateVerificationToken(): string
    {
        return Str::random(32);
    }

    public static function membershipTypes(): array
    {
        return [
            self::MEMBERSHIP_LIFETIME => 'Lifetime',
            self::MEMBERSHIP_ANNUAL => 'Annual',
            self::MEMBERSHIP_PREMIUM => 'Premium',
            self::MEMBERSHIP_HONORARY => 'Honorary',
            self::MEMBERSHIP_CORPORATE => 'Corporate',
        ];
    }

    public static function employmentStatuses(): array
    {
        return [
            self::EMPLOYED => 'Employed',
            self::SELF_EMPLOYED => 'Self Employed',
            self::UNEMPLOYED => 'Unemployed',
            self::STUDENT => 'Student',
            self::RETIRED => 'Retired',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_SUSPENDED => 'Suspended',
        ];
    }

    public function verify(int $userId): void
    {
        $this->update([
            'is_verified' => true,
            'verification_token' => null,
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);
    }

    public function isMembershipValid(): bool
    {
        if ($this->membership_type === self::MEMBERSHIP_LIFETIME) {
            return true;
        }

        return !$this->membership_end_date || $this->membership_end_date >= now();
    }
}
