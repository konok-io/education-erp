<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamSession extends Model
{
    use HasFactory;

    protected $table = 'exam_sessions';

    const STATUS_UPCOMING = 'upcoming';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'session',
        'academic_year',
        'term',
        'start_date',
        'end_date',
        'status',
        'description',
    ];

    protected $casts = [
        'academic_year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'session_id');
    }

    public function committeeMembers(): HasMany
    {
        return $this->hasMany(ExamCommitteeMember::class, 'session_id');
    }
}
