<?php

declare(strict_types=1);

namespace App\Models\Routine;

use App\Models\Academic\AcademicSession;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicCalendar extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'academic_calendars';

    protected $fillable = [
        'uuid',
        'session_id',
        'title',
        'title_bn',
        'description',
        'event_type',
        'start_date',
        'end_date',
        'is_all_day',
        'is_recurring',
        'recurrence_rule',
        'color',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_all_day' => 'boolean',
        'is_recurring' => 'boolean',
    ];

    // ===================== EVENT TYPES =====================
    public const TYPE_CLASS_START = 'class_start';
    public const TYPE_SEMESTER_START = 'semester_start';
    public const TYPE_SEMESTER_END = 'semester_end';
    public const TYPE_EXAM = 'exam';
    public const TYPE_HOLIDAY = 'holiday';
    public const TYPE_ADMISSION = 'admission';
    public const TYPE_REGISTRATION = 'registration';
    public const TYPE_RESULT = 'result';
    public const TYPE_EVENT = 'event';
    public const TYPE_OTHER = 'other';

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public static function eventTypes(): array
    {
        return [
            self::TYPE_CLASS_START => 'Class Start',
            self::TYPE_SEMESTER_START => 'Semester Start',
            self::TYPE_SEMESTER_END => 'Semester End',
            self::TYPE_EXAM => 'Exam',
            self::TYPE_HOLIDAY => 'Holiday',
            self::TYPE_ADMISSION => 'Admission',
            self::TYPE_REGISTRATION => 'Registration',
            self::TYPE_RESULT => 'Result',
            self::TYPE_EVENT => 'Event',
            self::TYPE_OTHER => 'Other',
        ];
    }
}
