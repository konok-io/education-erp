<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VivaExam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'viva_exams';

    const STATUS_PENDING = 'pending';
    const STATUS_CONDUCTED = 'conducted';
    const STATUS_EVALUATED = 'evaluated';

    protected $fillable = [
        'uuid',
        'exam_code',
        'exam_id',
        'student_id',
        'panel_members',
        'exam_date',
        'start_time',
        'end_time',
        'total_marks',
        'obtained_marks',
        'question_marks',
        'remarks',
        'status',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'panel_members' => 'array',
        'question_marks' => 'array',
        'evaluated_at' => 'datetime',
    ];

    public static function generateExamCode(): string
    {
        $prefix = 'VIVA';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->exam_code, -4)) + 1 : 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'evaluated_by');
    }
}
