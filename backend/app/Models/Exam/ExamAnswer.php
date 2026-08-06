<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $table = 'exam_answers';

    const STATUS_ANSWERED = 'answered';
    const STATUS_NOT_ANSWERED = 'not_answered';
    const STATUS_MARKED_REVIEW = 'marked_review';

    protected $fillable = [
        'uuid',
        'exam_id',
        'student_id',
        'question_id',
        'answer',
        'correct_answer',
        'is_correct',
        'obtained_marks',
        'negative_marks',
        'time_taken',
        'status',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'obtained_marks' => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'time_taken' => 'integer',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
