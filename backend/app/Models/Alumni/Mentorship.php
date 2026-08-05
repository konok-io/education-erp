<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mentorship extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'mentorships';

    protected $fillable = [
        'uuid',
        'mentorship_number',
        'mentor_id',
        'mentee_id',
        'student_id',
        'mentee_name',
        'mentee_email',
        'mentee_phone',
        'expertise_area',
        'goals',
        'background',
        'start_date',
        'end_date',
        'meeting_frequency',
        'status',
        'notes',
        'feedback',
        'sessions_completed',
        'assigned_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sessions_completed' => 'integer',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'mentor_id');
    }

    public function mentee(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'mentee_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }

    // ===================== METHODS =====================

    public static function generateMentorshipNumber(): string
    {
        $prefix = 'MTR';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function incrementSessions(): void
    {
        $this->increment('sessions_completed');
    }
}
