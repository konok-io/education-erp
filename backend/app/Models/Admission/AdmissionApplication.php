<?php

declare(strict_types=1);

namespace App\Models\Admission;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionApplication extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'admission_applications';

    protected $fillable = [
        'uuid',
        'application_no',
        'campaign_id',
        'applicant_name',
        'father_name',
        'mother_name',
        'guardian_name',
        'guardian_relation',
        'date_of_birth',
        'gender',
        'religion',
        'nationality',
        'blood_group',
        'email',
        'mobile',
        'present_address',
        'permanent_address',
        'ssc_gpa',
        'ssc_board',
        'ssc_group',
        'ssc_passing_year',
        'ssc_institution',
        'hsc_gpa',
        'hsc_board',
        'hsc_group',
        'hsc_passing_year',
        'hsc_institution',
        'quota',
        'selected_program_id',
        'selected_shift',
        'status',
        'payment_status',
        'payment_amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'merit_position',
        'is_waiting',
        'waiting_position',
        'interview_date',
        'interview_time',
        'interview_venue',
        'remarks',
        'submitted_at',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'payment_date' => 'datetime',
        'interview_date' => 'date',
        'interview_time' => 'datetime',
        'submitted_at' => 'datetime',
        'ssc_gpa' => 'decimal:2',
        'hsc_gpa' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'merit_position' => 'integer',
        'waiting_position' => 'integer',
        'is_waiting' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PENDING_DOCUMENT = 'pending_document';
    public const STATUS_DOCUMENT_VERIFIED = 'document_verified';
    public const STATUS_TEST_SCHEDULED = 'test_scheduled';
    public const STATUS_TEST_COMPLETED = 'test_completed';
    public const STATUS_INTERVIEW_SCHEDULED = 'interview_scheduled';
    public const STATUS_MERIT = 'merit';
    public const STATUS_WAITING = 'waiting';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ADMITTED = 'admitted';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== QUOTAS =====================
    public const QUOTA_GENERAL = 'general';
    public const QUOTA_FREEDOM_FIGHTER = 'freedom_fighter';
    public const QUOTA_TRIBAL = 'tribal';
    public const QUOTA_DISABLED = 'disabled';
    public const QUOTA_WOMEN = 'women';
    public const QUOTA_EMPLOYEE = 'employee';

    // ===================== RELATIONSHIPS =====================

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdmissionCampaign::class, 'campaign_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AdmissionDocument::class, 'application_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AdmissionPayment::class, 'application_id');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->whereIn('status', [
            self::STATUS_SUBMITTED,
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PENDING_DOCUMENT,
        ]);
    }

    public function scopeMerit($query)
    {
        return $query->where('status', self::STATUS_MERIT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    // ===================== METHODS =====================

    public static function generateApplicationNo(): string
    {
        $prefix = 'APP';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_PENDING_PAYMENT => 'Pending Payment',
            self::STATUS_PENDING_DOCUMENT => 'Pending Document',
            self::STATUS_DOCUMENT_VERIFIED => 'Document Verified',
            self::STATUS_TEST_SCHEDULED => 'Test Scheduled',
            self::STATUS_TEST_COMPLETED => 'Test Completed',
            self::STATUS_INTERVIEW_SCHEDULED => 'Interview Scheduled',
            self::STATUS_MERIT => 'In Merit List',
            self::STATUS_WAITING => 'Waiting List',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ADMITTED => 'Admitted',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function quotas(): array
    {
        return [
            self::QUOTA_GENERAL => 'General',
            self::QUOTA_FREEDOM_FIGHTER => 'Freedom Fighter',
            self::QUOTA_TRIBAL => 'Tribal',
            self::QUOTA_DISABLED => 'Disabled',
            self::QUOTA_WOMEN => 'Women',
            self::QUOTA_EMPLOYEE => 'Employee Children',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->applicant_name;
    }
}
