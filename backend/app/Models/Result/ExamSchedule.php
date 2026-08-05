<?php

declare(strict_types=1);

namespace App\Models\Result;

use App\Models\Academic\Subject;
use App\Models\Teacher\Teacher;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSchedule extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_schedules';

    protected $fillable = [
        'uuid',
        'exam_id',
        'subject_id',
        'teacher_id',
        'exam_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'hall_id',
        'hall_name',
        'total_seats',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'duration_minutes' => 'integer',
        'total_seats' => 'integer',
        'is_active' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(ExamHall::class, 'hall_id');
    }
}
