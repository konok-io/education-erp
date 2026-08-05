<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Placement extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'placements';

    protected $fillable = [
        'uuid',
        'placement_number',
        'employer_id',
        'job_id',
        'alumni_profile_id',
        'student_id',
        'student_name',
        'student_email',
        'company_name',
        'designation',
        'department',
        'location',
        'salary',
        'salary_currency',
        'employment_type',
        'joining_date',
        'offer_letter',
        'status',
        'remarks',
        'added_by',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'joining_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_OFFER_EXTENDED = 'offer_extended';
    public const STATUS_OFFER_ACCEPTED = 'offer_accepted';
    public const STATUS_OFFER_DECLINED = 'offer_declined';
    public const STATUS_JOINED = 'joined';
    public const STATUS_PROBATION = 'probation';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_LEFT = 'left';

    // ===================== RELATIONSHIPS =====================

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class, 'employer_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function alumniProfile(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'alumni_profile_id');
    }

    public function adder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'added_by');
    }

    // ===================== SCOPES =====================

    public function scopeAccepted($query)
    {
        return $query->whereIn('status', [self::STATUS_OFFER_ACCEPTED, self::STATUS_JOINED, self::STATUS_CONFIRMED]);
    }

    // ===================== METHODS =====================

    public static function generatePlacementNumber(): string
    {
        $prefix = 'PLC';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_OFFER_EXTENDED => 'Offer Extended',
            self::STATUS_OFFER_ACCEPTED => 'Offer Accepted',
            self::STATUS_OFFER_DECLINED => 'Offer Declined',
            self::STATUS_JOINED => 'Joined',
            self::STATUS_PROBATION => 'Probation',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_LEFT => 'Left',
        ];
    }
}
