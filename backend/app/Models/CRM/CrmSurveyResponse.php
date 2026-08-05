<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmSurveyResponse extends Model
{
    use HasFactory;

    protected $table = 'crm_survey_responses';

    protected $fillable = [
        'uuid',
        'survey_id',
        'respondent_id',
        'student_id',
        'employee_id',
        'responses',
        'total_score',
        'average_rating',
        'comments',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'responses' => 'array',
        'total_score' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(CrmSurvey::class, 'survey_id');
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'respondent_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Employee\Employee::class, 'employee_id');
    }
}
