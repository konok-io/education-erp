<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryAction extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'disciplinary_actions';

    protected $fillable = [
        'uuid',
        'case_no',
        'employee_id',
        'action_type',
        'title',
        'description',
        'evidence',
        'incident_date',
        'issue_date',
        'response_deadline',
        'employee_response',
        'response_date',
        'status',
        'final_decision',
        'decision_details',
        'decision_date',
        'fine_amount',
        'suspension_start',
        'suspension_end',
        'investigation_officer',
        'decided_by',
        'remarks',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'issue_date' => 'date',
        'response_deadline' => 'date',
        'response_date' => 'date',
        'decision_date' => 'date',
        'suspension_start' => 'date',
        'suspension_end' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    // ===================== ACTION TYPES =====================
    public const TYPE_VERBAL_WARNING = 'verbal_warning';
    public const TYPE_WRITTEN_WARNING = 'written_warning';
    public const TYPE_SHOW_CAUSE = 'show_cause';
    public const TYPE_SUSPENSION = 'suspension';
    public const TYPE_FINE = 'fine';
    public const TYPE_DEMOTION = 'demotion';
    public const TYPE_TERMINATION_RECOMMENDATION = 'termination_recommendation';

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_UNDER_INVESTIGATION = 'under_investigation';
    public const STATUS_SHOW_CAUSE_ISSUED = 'show_cause_issued';
    public const STATUS_RESPONSE_RECEIVED = 'response_received';
    public const STATUS_DECIDED = 'decided';
    public const STATUS_WITHDRAWN = 'withdrawn';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== DECISIONS =====================
    public const DECISION_NO_ACTION = 'no_action';
    public const DECISION_WARNING = 'warning';
    public const DECISION_FINE = 'fine';
    public const DECISION_SUSPENSION = 'suspension';
    public const DECISION_DEMOTION = 'demotion';
    public const DECISION_TERMINATION = 'termination';
    public const DECISION_OTHER = 'other';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function investigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigation_officer');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    // ===================== METHODS =====================

    public static function generateCaseNo(): string
    {
        $prefix = 'DC';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function actionTypes(): array
    {
        return [
            self::TYPE_VERBAL_WARNING => 'Verbal Warning',
            self::TYPE_WRITTEN_WARNING => 'Written Warning',
            self::TYPE_SHOW_CAUSE => 'Show Cause Notice',
            self::TYPE_SUSPENSION => 'Suspension',
            self::TYPE_FINE => 'Fine',
            self::TYPE_DEMOTION => 'Demotion',
            self::TYPE_TERMINATION_RECOMMENDATION => 'Termination Recommendation',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_UNDER_INVESTIGATION => 'Under Investigation',
            self::STATUS_SHOW_CAUSE_ISSUED => 'Show Cause Issued',
            self::STATUS_RESPONSE_RECEIVED => 'Response Received',
            self::STATUS_DECIDED => 'Decided',
            self::STATUS_WITHDRAWN => 'Withdrawn',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public static function decisions(): array
    {
        return [
            self::DECISION_NO_ACTION => 'No Action',
            self::DECISION_WARNING => 'Warning',
            self::DECISION_FINE => 'Fine',
            self::DECISION_SUSPENSION => 'Suspension',
            self::DECISION_DEMOTION => 'Demotion',
            self::DECISION_TERMINATION => 'Termination',
            self::DECISION_OTHER => 'Other',
        ];
    }

    public function getSeverityLabelAttribute(): string
    {
        return match ($this->action_type) {
            self::TYPE_VERBAL_WARNING => 'Low',
            self::TYPE_WRITTEN_WARNING => 'Low-Medium',
            self::TYPE_SHOW_CAUSE => 'Medium',
            self::TYPE_FINE => 'Medium-High',
            self::TYPE_SUSPENSION => 'High',
            self::TYPE_DEMOTION => 'High',
            self::TYPE_TERMINATION_RECOMMENDATION => 'Critical',
            default => 'Unknown',
        };
    }
}
