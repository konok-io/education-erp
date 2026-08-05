<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmSurvey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_surveys';

    const TYPE_COURSE = 'course';
    const TYPE_TEACHER_EVALUATION = 'teacher_evaluation';
    const TYPE_CAMPUS_FEEDBACK = 'campus_feedback';
    const TYPE_SERVICE_QUALITY = 'service_quality';
    const TYPE_CUSTOM = 'custom';

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'uuid',
        'survey_no',
        'title',
        'description',
        'survey_type',
        'questions',
        'status',
        'created_by',
        'start_date',
        'end_date',
        'is_anonymous',
        'allow_multiple',
        'show_results',
        'target_audience',
        'total_responses',
        'average_rating',
    ];

    protected $casts = [
        'questions' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_anonymous' => 'boolean',
        'allow_multiple' => 'boolean',
        'show_results' => 'boolean',
        'target_audience' => 'array',
        'average_rating' => 'decimal:2',
    ];

    public static function generateSurveyNo(): string
    {
        $prefix = 'SVR';
        $year = date('Y');
        $lastSurvey = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastSurvey ? ((int) substr($lastSurvey->survey_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public static function surveyTypes(): array
    {
        return [
            self::TYPE_COURSE => 'Course Survey',
            self::TYPE_TEACHER_EVALUATION => 'Teacher Evaluation',
            self::TYPE_CAMPUS_FEEDBACK => 'Campus Feedback',
            self::TYPE_SERVICE_QUALITY => 'Service Quality',
            self::TYPE_CUSTOM => 'Custom Survey',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(CrmSurveyResponse::class, 'survey_id');
    }

    public function calculateAverageRating(): void
    {
        $responses = $this->responses()->whereNotNull('average_rating')->get();
        if ($responses->count() > 0) {
            $avg = $responses->avg('average_rating');
            $this->update(['average_rating' => $avg]);
        }
    }
}
