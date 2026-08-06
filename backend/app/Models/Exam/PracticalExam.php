<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticalExam extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'practical_exams';

    const STATUS_PENDING = 'pending';
    const STATUS_CONDUCTED = 'conducted';
    const STATUS_EVALUATED = 'evaluated';

    protected $fillable = [
        'uuid',
        'exam_code',
        'exam_id',
        'student_id',
        'lab_name',
        'instructor_name',
        'instructor_id',
        'exam_date',
        'start_time',
        'end_time',
        'practical_marks',
        'obtained_marks',
        'observation',
        'remarks',
        'status',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'practical_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'evaluated_at' => 'datetime',
    ];

    public static function generateExamCode(): string
    {
        $prefix = 'PRAC';
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

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'instructor_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'evaluated_by');
    }
}
