<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSession extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_sessions';

    protected $fillable = [
        'uuid',
        'session_name',
        'academic_year',
        'semester',
        'term',
        'start_date',
        'end_date',
        'description',
        'status',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    // ===================== STATUS =====================
    public const STATUS_UPCOMING = 'upcoming';
    public const STATUS_ONGOING = 'ongoing';
    public const STATUS_COMPLETED = 'completed';

    // ===================== RELATIONSHIPS =====================

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class, 'exam_session_id');
    }

    public function committees(): HasMany
    {
        return $this->hasMany(ExamCommittee::class, 'exam_session_id');
    }

    // ===================== SCOPES =====================

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_UPCOMING, self::STATUS_ONGOING]);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_UPCOMING => 'Upcoming',
            self::STATUS_ONGOING => 'Ongoing',
            self::STATUS_COMPLETED => 'Completed',
        ];
    }

    public function setCurrent(): void
    {
        self::where('is_current', true)->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }
}
