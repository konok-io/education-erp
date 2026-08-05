<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamAttendance extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_attendances';

    protected $fillable = [
        'uuid',
        'exam_subject_id',
        'exam_hall_id',
        'student_id',
        'student_name',
        'student_roll',
        'registration_no',
        'seat_number',
        'status',
        'arrival_time',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'arrival_time' => 'datetime:H:i',
    ];

    // ===================== STATUS =====================
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_LATE = 'late';
    public const STATUS_EXEMPTED = 'exempted';

    // ===================== RELATIONSHIPS =====================

    public function subject(): BelongsTo
    {
        return $this->belongsTo(ExamSubject::class, 'exam_subject_id');
    }

    public function hall(): BelongsTo
    {
        return $this->belongsTo(ExamHall::class, 'exam_hall_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    // ===================== SCOPES =====================

    public function scopePresent($query)
    {
        return $query->where('status', self::STATUS_PRESENT);
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', self::STATUS_ABSENT);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LATE => 'Late',
            self::STATUS_EXEMPTED => 'Exempted',
        ];
    }

    public function markPresent(): void
    {
        $this->update([
            'status' => self::STATUS_PRESENT,
            'arrival_time' => now(),
        ]);
    }

    public function markAbsent(): void
    {
        $this->update(['status' => self::STATUS_ABSENT]);
    }

    public function markLate(): void
    {
        $this->update([
            'status' => self::STATUS_LATE,
            'arrival_time' => now(),
        ]);
    }
}
