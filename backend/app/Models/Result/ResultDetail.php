<?php

declare(strict_types=1);

namespace App\Models\Result;

use App\Models\Academic\Subject;
use App\Models\Teacher\Teacher;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultDetail extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'result_details';

    protected $fillable = [
        'uuid',
        'result_id',
        'subject_id',
        'teacher_id',
        'theory_marks',
        'practical_marks',
        'viva_marks',
        'attendance_marks',
        'assignment_marks',
        'internal_marks',
        'total_marks',
        'obtained_marks',
        'pass_marks',
        'credit',
        'grade_point',
        'grade',
        'is_pass',
        'grace_marks',
        'grace_approved',
        'remarks',
        'entered_by',
        'entered_at',
    ];

    protected $casts = [
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'viva_marks' => 'decimal:2',
        'attendance_marks' => 'decimal:2',
        'assignment_marks' => 'decimal:2',
        'internal_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
        'credit' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'grace_marks' => 'decimal:2',
        'is_pass' => 'boolean',
        'entered_at' => 'datetime',
    ];

    public function result(): BelongsTo
    {
        return $this->belongsTo(Result::class, 'result_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function calculateGrade(): array
    {
        $totalObtained = (float) $this->obtained_marks;
        $totalFull = (float) $this->total_marks;

        $percentage = $totalFull > 0 ? ($totalObtained / $totalFull) * 100 : 0;

        $gradeRule = GradeRule::where('is_active', true)->first();
        
        if ($gradeRule) {
            return $gradeRule->calculateGrade($percentage);
        }

        // Default grading (Bangladesh Board)
        if ($percentage >= 80) {
            return ['grade' => 'A+', 'point' => 5.00];
        } elseif ($percentage >= 70) {
            return ['grade' => 'A', 'point' => 4.00];
        } elseif ($percentage >= 60) {
            return ['grade' => 'A-', 'point' => 3.50];
        } elseif ($percentage >= 50) {
            return ['grade' => 'B', 'point' => 3.00];
        } elseif ($percentage >= 40) {
            return ['grade' => 'C', 'point' => 2.00];
        } elseif ($percentage >= 33) {
            return ['grade' => 'D', 'point' => 1.00];
        } else {
            return ['grade' => 'F', 'point' => 0.00];
        }
    }
}
