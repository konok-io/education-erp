<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttendance extends Model
{
    use HasFactory;

    protected $table = 'exam_attendances';

    const STATUS_PRESENT = 'present';
    const STATUS_ABSENT = 'absent';
    const STATUS_LATE = 'late';
    const STATUS_DISQUALIFIED = 'disqualified';

    protected $fillable = [
        'uuid',
        'exam_id',
        'student_id',
        'student_name',
        'roll_number',
        'status',
        'entry_time',
        'exit_time',
        'seat_number',
        'remarks',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }
}
