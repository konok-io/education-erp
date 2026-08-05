<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSeatPlan extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_seat_plans';

    protected $fillable = [
        'uuid',
        'exam_id',
        'exam_subject_id',
        'exam_hall_id',
        'row_number',
        'column_number',
        'seat_number',
        'student_type',
        'student_id',
        'student_name',
        'student_roll',
        'registration_no',
        'remarks',
    ];

    protected $casts = [
        'row_number' => 'integer',
        'column_number' => 'integer',
    ];

    // ===================== RELATIONSHIPS =====================

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class, 'exam_subject_id');
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(ExamHall::class, 'exam_hall_id');
    }

    // ===================== METHODS =====================

    public static function generateSeatNumber(ExamHall $hall, int $row, int $column): string
    {
        return sprintf('%s-%d%02d', $hall->hall_code, $row, $column);
    }
}
