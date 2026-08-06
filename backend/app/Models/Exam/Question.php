<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'questions';

    const TYPE_MCQ = 'mcq';
    const TYPE_CQ = 'cq';
    const TYPE_WRITTEN = 'written';
    const TYPE_SHORT = 'short';
    const TYPE_TRUE_FALSE = 'true_false';
    const TYPE_FILL_BLANK = 'fill_blank';
    const TYPE_MATCHING = 'matching';
    const TYPE_PROGRAMMING = 'programming';
    const TYPE_MATH = 'math';
    const TYPE_DIAGRAM = 'diagram';

    const DIFFICULTY_EASY = 'easy';
    const DIFFICULTY_MEDIUM = 'medium';
    const DIFFICULTY_HARD = 'hard';
    const DIFFICULTY_EXPERT = 'expert';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING_REVIEW = 'pending_review';

    protected $fillable = [
        'uuid',
        'question_code',
        'subject_id',
        'category_id',
        'chapter',
        'topic',
        'question_type',
        'difficulty',
        'marks',
        'question',
        'question_bn',
        'options',
        'correct_answer',
        'explanation',
        'attachments',
        'metadata',
        'usage_count',
        'success_rate',
        'created_by',
        'status',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
        'options' => 'array',
        'attachments' => 'array',
        'metadata' => 'array',
        'usage_count' => 'integer',
        'success_rate' => 'decimal:2',
    ];

    public static function generateQuestionCode(): string
    {
        $prefix = 'Q';
        $last = self::orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->question_code, 1)) + 1 : 1;
        return sprintf('%s%05d', $prefix, $sequence);
    }

    public static function questionTypes(): array
    {
        return [
            self::TYPE_MCQ => 'MCQ',
            self::TYPE_CQ => 'CQ (Creative)',
            self::TYPE_WRITTEN => 'Written',
            self::TYPE_SHORT => 'Short Question',
            self::TYPE_TRUE_FALSE => 'True/False',
            self::TYPE_FILL_BLANK => 'Fill in the Blank',
            self::TYPE_MATCHING => 'Matching',
            self::TYPE_PROGRAMMING => 'Programming',
            self::TYPE_MATH => 'Mathematics',
            self::TYPE_DIAGRAM => 'Diagram Based',
        ];
    }

    public static function difficultyLevels(): array
    {
        return [
            self::DIFFICULTY_EASY => 'Easy',
            self::DIFFICULTY_MEDIUM => 'Medium',
            self::DIFFICULTY_HARD => 'Hard',
            self::DIFFICULTY_EXPERT => 'Expert',
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Subject::class, 'subject_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function examQuestions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class, 'question_id');
    }

    public function isAutoEvaluable(): bool
    {
        return in_array($this->question_type, [
            self::TYPE_MCQ,
            self::TYPE_TRUE_FALSE,
            self::TYPE_FILL_BLANK,
            self::TYPE_MATCHING,
        ]);
    }
}
