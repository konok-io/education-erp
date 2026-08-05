<?php

declare(strict_types=1);

namespace App\Models\Routine;

use App\Models\Academic\AcademicSession;
use App\Models\Academic\AcademicClass;
use App\Models\Academic\Section;
use App\Models\Academic\Subject;
use App\Models\Teacher\Teacher;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Routine extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'routines';

    protected $fillable = [
        'uuid',
        'routine_code',
        'session_id',
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'room_id',
        'time_slot_id',
        'period_id',
        'day_of_week',
        'routine_type',
        'version',
        'is_published',
        'published_at',
        'published_by',
        'status',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'version' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    // ===================== ROUTINE TYPES =====================
    public const TYPE_CLASS = 'class';
    public const TYPE_TEACHER = 'teacher';
    public const TYPE_STUDENT = 'student';
    public const TYPE_EXAM = 'exam';
    public const TYPE_PRACTICAL = 'practical';
    public const TYPE_LABORATORY = 'laboratory';
    public const TYPE_SPECIAL = 'special';

    // ===================== DAYS =====================
    public const DAY_SATURDAY = 0;
    public const DAY_SUNDAY = 1;
    public const DAY_MONDAY = 2;
    public const DAY_TUESDAY = 3;
    public const DAY_WEDNESDAY = 4;
    public const DAY_THURSDAY = 5;
    public const DAY_FRIDAY = 6;

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED = 'archived';

    // ===================== RELATIONSHIPS =====================

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class, 'period_id');
    }

    // ===================== SCOPES =====================

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByClass($query, string $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeByTeacher($query, string $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByDay($query, int $day)
    {
        return $query->where('day_of_week', $day);
    }

    // ===================== METHODS =====================

    public static function generateRoutineCode(): string
    {
        $prefix = 'RTN';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public static function routineTypes(): array
    {
        return [
            self::TYPE_CLASS => 'Class Routine',
            self::TYPE_TEACHER => 'Teacher Routine',
            self::TYPE_STUDENT => 'Student Routine',
            self::TYPE_EXAM => 'Exam Routine',
            self::TYPE_PRACTICAL => 'Practical Routine',
            self::TYPE_LABORATORY => 'Laboratory Routine',
            self::TYPE_SPECIAL => 'Special Routine',
        ];
    }

    public static function days(): array
    {
        return [
            self::DAY_SATURDAY => 'Saturday',
            self::DAY_SUNDAY => 'Sunday',
            self::DAY_MONDAY => 'Monday',
            self::DAY_TUESDAY => 'Tuesday',
            self::DAY_WEDNESDAY => 'Wednesday',
            self::DAY_THURSDAY => 'Thursday',
            self::DAY_FRIDAY => 'Friday',
        ];
    }

    public function publish(int $userId): void
    {
        $this->update([
            'is_published' => true,
            'status' => self::STATUS_PUBLISHED,
            'published_at' => now(),
            'published_by' => $userId,
        ]);
    }

    public function hasConflict(): bool
    {
        // Check teacher conflict
        $teacherConflict = self::where('teacher_id', $this->teacher_id)
            ->where('day_of_week', $this->day_of_week)
            ->where('time_slot_id', $this->time_slot_id)
            ->where('id', '!=', $this->id ?? 0)
            ->exists();

        if ($teacherConflict) {
            return true;
        }

        // Check room conflict
        $roomConflict = self::where('room_id', $this->room_id)
            ->where('day_of_week', $this->day_of_week)
            ->where('time_slot_id', $this->time_slot_id)
            ->where('id', '!=', $this->id ?? 0)
            ->exists();

        return $roomConflict;
    }
}
