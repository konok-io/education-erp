<?php

declare(strict_types=1);

namespace App\Models\Attendance;

use App\Models\Student\Student;
use App\Models\Teacher\Teacher;
use App\Models\Employee\Employee;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\AcademicClass;
use App\Models\Academic\Section;
use App\Models\Academic\Subject;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'attendances';

    protected $fillable = [
        'uuid',
        'attendance_no',
        'attendance_type',
        'attendance_date',
        'attendance_time',
        'campus_id',
        'session_id',
        'class_id',
        'section_id',
        'subject_id',
        'student_id',
        'teacher_id',
        'employee_id',
        'status',
        'entry_method',
        'entry_by',
        'late_minutes',
        'remarks',
        'is_approved',
        'approved_by',
        'approved_at',
        'qr_data',
        'latitude',
        'longitude',
        'device_info',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'attendance_time' => 'datetime',
        'late_minutes' => 'integer',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'qr_data' => 'array',
    ];

    // ===================== ATTENDANCE TYPES =====================
    public const TYPE_STUDENT = 'student';
    public const TYPE_TEACHER = 'teacher';
    public const TYPE_EMPLOYEE = 'employee';

    // ===================== ATTENDANCE STATUS =====================
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_LATE = 'late';
    public const STATUS_LEAVE = 'leave';
    public const STATUS_HALF_DAY = 'half_day';
    public const STATUS_HOLIDAY = 'holiday';
    public const STATUS_WEEKEND = 'weekend';
    public const STATUS_EXAM_DUTY = 'exam_duty';
    public const STATUS_OFFICIAL_TOUR = 'official_tour';
    public const STATUS_REMOTE = 'remote';

    // ===================== ENTRY METHODS =====================
    public const METHOD_MANUAL = 'manual';
    public const METHOD_QR = 'qr';
    public const METHOD_BARCODE = 'barcode';
    public const METHOD_RFID = 'rfid';
    public const METHOD_FINGERPRINT = 'fingerprint';
    public const METHOD_FACE = 'face';
    public const METHOD_GPS = 'gps';
    public const METHOD_API = 'api';
    public const METHOD_MOBILE = 'mobile';

    // ===================== RELATIONSHIPS =====================

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // ===================== SCOPES =====================

    public function scopeStudentAttendance($query)
    {
        return $query->where('attendance_type', self::TYPE_STUDENT);
    }

    public function scopeTeacherAttendance($query)
    {
        return $query->where('attendance_type', self::TYPE_TEACHER);
    }

    public function scopeEmployeeAttendance($query)
    {
        return $query->where('attendance_type', self::TYPE_EMPLOYEE);
    }

    public function scopeByDate($query, string $date)
    {
        return $query->whereDate('attendance_date', $date);
    }

    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByClass($query, string $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySection($query, string $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    // ===================== METHODS =====================

    public static function generateAttendanceNo(): string
    {
        $prefix = 'ATT';
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now()->toDateString())->count() + 1;
        return sprintf('%s-%s-%04d', $prefix, $date, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PRESENT => 'Present',
            self::STATUS_ABSENT => 'Absent',
            self::STATUS_LATE => 'Late',
            self::STATUS_LEAVE => 'Leave',
            self::STATUS_HALF_DAY => 'Half Day',
            self::STATUS_HOLIDAY => 'Holiday',
            self::STATUS_WEEKEND => 'Weekend',
            self::STATUS_EXAM_DUTY => 'Exam Duty',
            self::STATUS_OFFICIAL_TOUR => 'Official Tour',
            self::STATUS_REMOTE => 'Remote Duty',
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_STUDENT => 'Student',
            self::TYPE_TEACHER => 'Teacher',
            self::TYPE_EMPLOYEE => 'Employee',
        ];
    }

    public static function entryMethods(): array
    {
        return [
            self::METHOD_MANUAL => 'Manual',
            self::METHOD_QR => 'QR Code',
            self::METHOD_BARCODE => 'Barcode',
            self::METHOD_RFID => 'RFID',
            self::METHOD_FINGERPRINT => 'Fingerprint',
            self::METHOD_FACE => 'Face Recognition',
            self::METHOD_GPS => 'GPS',
            self::METHOD_API => 'API',
            self::METHOD_MOBILE => 'Mobile App',
        ];
    }

    public function approve(int $userId): void
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function isPresent(): bool
    {
        return in_array($this->status, [self::STATUS_PRESENT, self::STATUS_LATE]);
    }
}
