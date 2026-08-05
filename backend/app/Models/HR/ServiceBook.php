<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceBook extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'service_books';

    protected $fillable = [
        'uuid',
        'employee_id',
        'entry_no',
        'entry_date',
        'event_type',
        'title',
        'description',
        'metadata',
        'approved_by',
        'approved_date',
        'remarks',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'approved_date' => 'date',
        'metadata' => 'array',
    ];

    // ===================== EVENT TYPES =====================
    public const EVENT_JOINING = 'joining';
    public const EVENT_PROMOTION = 'promotion';
    public const EVENT_TRANSFER = 'transfer';
    public const EVENT_SALARY_REVISION = 'salary_revision';
    public const EVENT_LEAVE = 'leave';
    public const EVENT_AWARD = 'award';
    public const EVENT_PUNISHMENT = 'punishment';
    public const EVENT_TRAINING = 'training';
    public const EVENT_PERFORMANCE_REVIEW = 'performance_review';
    public const EVENT_CONFIRMATION = 'confirmation';
    public const EVENT_RESIGNATION = 'resignation';
    public const EVENT_RETIREMENT = 'retirement';
    public const EVENT_TERMINATION = 'termination';
    public const EVENT_OTHER = 'other';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ===================== METHODS =====================

    public static function generateEntryNo(): string
    {
        $prefix = 'SB';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function eventTypes(): array
    {
        return [
            self::EVENT_JOINING => 'Joining',
            self::EVENT_PROMOTION => 'Promotion',
            self::EVENT_TRANSFER => 'Transfer',
            self::EVENT_SALARY_REVISION => 'Salary Revision',
            self::EVENT_LEAVE => 'Leave',
            self::EVENT_AWARD => 'Award',
            self::EVENT_PUNISHMENT => 'Punishment',
            self::EVENT_TRAINING => 'Training',
            self::EVENT_PERFORMANCE_REVIEW => 'Performance Review',
            self::EVENT_CONFIRMATION => 'Confirmation',
            self::EVENT_RESIGNATION => 'Resignation',
            self::EVENT_RETIREMENT => 'Retirement',
            self::EVENT_TERMINATION => 'Termination',
            self::EVENT_OTHER => 'Other',
        ];
    }

    public function getEventIconAttribute(): string
    {
        return match ($this->event_type) {
            self::EVENT_JOINING => '🎉',
            self::EVENT_PROMOTION => '⬆️',
            self::EVENT_TRANSFER => '🔄',
            self::EVENT_SALARY_REVISION => '💰',
            self::EVENT_LEAVE => '🏖️',
            self::EVENT_AWARD => '🏆',
            self::EVENT_PUNISHMENT => '⚠️',
            self::EVENT_TRAINING => '📚',
            self::EVENT_PERFORMANCE_REVIEW => '📊',
            self::EVENT_CONFIRMATION => '✅',
            self::EVENT_RESIGNATION => '👋',
            self::EVENT_RETIREMENT => '🌅',
            self::EVENT_TERMINATION => '❌',
            default => '📝',
        };
    }
}
