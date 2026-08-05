<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Academic\Department;
use App\Models\Employee\Designation;
use App\Models\Employee\EmploymentType;
use App\Models\HR\SalaryGrade;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferLetter extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'offer_letters';

    protected $fillable = [
        'uuid',
        'offer_no',
        'job_circular_id',
        'job_application_id',
        'interview_id',
        'candidate_name',
        'email',
        'mobile',
        'designation_id',
        'department_id',
        'employment_type_id',
        'salary_grade_id',
        'offered_salary',
        'offer_date',
        'joining_date',
        'terms_conditions',
        'benefits',
        'status',
        'response_date',
        'response_notes',
    ];

    protected $casts = [
        'offer_date' => 'date',
        'joining_date' => 'date',
        'response_date' => 'date',
        'offered_salary' => 'decimal:2',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_JOINED = 'joined';

    // ===================== RELATIONSHIPS =====================

    public function jobCircular(): BelongsTo
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'interview_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function employmentType(): BelongsTo
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }

    public function salaryGrade(): BelongsTo
    {
        return $this->belongsTo(SalaryGrade::class, 'salary_grade_id');
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(EmployeeOnboarding::class, 'offer_letter_id');
    }

    // ===================== METHODS =====================

    public static function generateOfferNo(): string
    {
        $prefix = 'OFR';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SENT => 'Sent',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_DECLINED => 'Declined',
            self::STATUS_EXPIRED => 'Expired',
            self::STATUS_JOINED => 'Joined',
        ];
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SENT]);
    }
}
