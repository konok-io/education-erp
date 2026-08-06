<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CbtSession extends Model
{
    use HasFactory;

    protected $table = 'cbt_sessions';

    const STATUS_NOT_STARTED = 'not_started';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';
    const STATUS_TERMINATED = 'terminated';

    protected $fillable = [
        'uuid',
        'session_token',
        'exam_id',
        'student_id',
        'ip_address',
        'user_agent',
        'started_at',
        'completed_at',
        'total_time',
        'remaining_time',
        'status',
        'tab_switches',
        'fullscreen_active',
        'answered_questions',
        'flags',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_time' => 'integer',
        'remaining_time' => 'integer',
        'tab_switches' => 'integer',
        'fullscreen_active' => 'boolean',
        'answered_questions' => 'array',
        'flags' => 'array',
    ];

    public static function generateSessionToken(): string
    {
        return Str::random(64);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function start(): void
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'started_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function terminate(): void
    {
        $this->update([
            'status' => self::STATUS_TERMINATED,
            'completed_at' => now(),
        ]);
    }
}
