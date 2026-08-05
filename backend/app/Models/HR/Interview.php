<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Designation;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'interviews';

    protected $fillable = [
        'uuid',
        'interview_no',
        'job_circular_id',
        'job_application_id',
        'interview_date',
        'start_time',
        'end_time',
        'venue',
        'interview_type',
        'panel_members',
        'total_marks',
        'obtained_marks',
        'questions',
        'answers',
        'remarks',
        'feedback',
        'decision',
        'rating',
        'evaluation_scores',
        'offer_extended',
        'offer_date',
        'joining_date',
        'rejection_reason',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'offer_date' => 'date',
        'joining_date' => 'date',
        'panel_members' => 'array',
        'evaluation_scores' => 'array',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'rating' => 'decimal:2',
        'offer_extended' => 'boolean',
    ];

    // ===================== DECISIONS =====================
    public const DECISION_PENDING = 'pending';
    public const DECISION_SELECTED = 'selected';
    public const DECISION_REJECTED = 'rejected';
    public const DECISION_WAITING_LIST = 'waiting_list';
    public const DECISION_HOLD = 'hold';

    // ===================== INTERVIEW TYPES =====================
    public const TYPE_PERSONAL = 'personal';
    public const TYPE_PANEL = 'panel';
    public const TYPE_WRITTEN = 'written';
    public const TYPE_PRACTICAL = 'practical';

    // ===================== RELATIONSHIPS =====================

    public function jobCircular(): BelongsTo
    {
        return $this->belongsTo(JobCircular::class, 'job_circular_id');
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function offerLetter(): BelongsTo
    {
        return $this->belongsTo(OfferLetter::class, 'interview_id');
    }

    // ===================== METHODS =====================

    public static function generateInterviewNo(): string
    {
        $prefix = 'INT';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function decisions(): array
    {
        return [
            self::DECISION_PENDING => 'Pending',
            self::DECISION_SELECTED => 'Selected',
            self::DECISION_REJECTED => 'Rejected',
            self::DECISION_WAITING_LIST => 'Waiting List',
            self::DECISION_HOLD => 'Hold',
        ];
    }

    public static function interviewTypes(): array
    {
        return [
            self::TYPE_PERSONAL => 'Personal Interview',
            self::TYPE_PANEL => 'Panel Interview',
            self::TYPE_WRITTEN => 'Written Test',
            self::TYPE_PRACTICAL => 'Practical Test',
        ];
    }

    public function getPercentageScoreAttribute(): ?float
    {
        if (!$this->obtained_marks || !$this->total_marks) {
            return null;
        }
        return ($this->obtained_marks / $this->total_marks) * 100;
    }

    public function isSelected(): bool
    {
        return $this->decision === self::DECISION_SELECTED;
    }

    public function calculateOverallScore(): ?float
    {
        if (!$this->evaluation_scores) {
            return null;
        }

        $scores = $this->evaluation_scores;
        $total = 0;
        $count = 0;

        foreach (['education', 'experience', 'technical', 'communication', 'leadership'] as $category) {
            if (isset($scores[$category])) {
                $total += $scores[$category];
                $count++;
            }
        }

        return $count > 0 ? $total / $count : null;
    }
}
