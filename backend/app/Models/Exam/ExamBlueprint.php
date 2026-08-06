<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamBlueprint extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_blueprints';

    protected $fillable = [
        'uuid',
        'blueprint_code',
        'exam_id',
        'subject_id',
        'name',
        'description',
        'question_distribution',
        'difficulty_distribution',
        'total_marks',
        'total_questions',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'total_questions' => 'integer',
        'question_distribution' => 'array',
        'difficulty_distribution' => 'array',
        'approved_at' => 'datetime',
    ];

    public static function generateBlueprintCode(): string
    {
        $prefix = 'BP';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->blueprint_code, -4)) + 1 : 1;
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Subject::class, 'subject_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}
