<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $table = 'exam_questions';

    protected $fillable = [
        'uuid',
        'exam_id',
        'question_id',
        'order',
        'marks',
        'is_randomized',
    ];

    protected $casts = [
        'order' => 'integer',
        'marks' => 'decimal:2',
        'is_randomized' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
