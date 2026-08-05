<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingRecord extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'training_records';

    protected $fillable = [
        'uuid',
        'training_no',
        'employee_id',
        'training_type_id',
        'training_name',
        'organizer',
        'venue',
        'start_date',
        'end_date',
        'duration_days',
        'duration_hours',
        'certificate_number',
        'certificate_date',
        'result',
        'feedback',
        'score',
        'cost',
        'certificate_file',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'certificate_date' => 'date',
        'score' => 'integer',
        'cost' => 'decimal:2',
        'duration_days' => 'integer',
        'duration_hours' => 'integer',
    ];

    // ===================== RESULTS =====================
    public const RESULT_PENDING = 'pending';
    public const RESULT_PASSED = 'passed';
    public const RESULT_FAILED = 'failed';
    public const RESULT_INCOMPLETE = 'incomplete';
    public const RESULT_EXCELLENT = 'excellent';
    public const RESULT_VERY_GOOD = 'very_good';
    public const RESULT_GOOD = 'good';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function trainingType(): BelongsTo
    {
        return $this->belongsTo(TrainingType::class, 'training_type_id');
    }

    // ===================== METHODS =====================

    public static function generateTrainingNo(): string
    {
        $prefix = 'TRG';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function results(): array
    {
        return [
            self::RESULT_PENDING => 'Pending',
            self::RESULT_PASSED => 'Passed',
            self::RESULT_FAILED => 'Failed',
            self::RESULT_INCOMPLETE => 'Incomplete',
            self::RESULT_EXCELLENT => 'Excellent',
            self::RESULT_VERY_GOOD => 'Very Good',
            self::RESULT_GOOD => 'Good',
        ];
    }

    public function getDurationAttribute(): string
    {
        $parts = [];
        if ($this->duration_days > 0) {
            $parts[] = $this->duration_days . ' day(s)';
        }
        if ($this->duration_hours > 0) {
            $parts[] = $this->duration_hours . ' hour(s)';
        }
        return implode(', ', $parts) ?: 'N/A';
    }

    public function isCompleted(): bool
    {
        return in_array($this->result, [
            self::RESULT_PASSED,
            self::RESULT_EXCELLENT,
            self::RESULT_VERY_GOOD,
            self::RESULT_GOOD,
        ]);
    }
}
