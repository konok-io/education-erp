<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservabilityOncallShift extends Model
{
    use HasUuids, HasUuid;

    protected $table = 'observability_oncall_shifts';

    protected $fillable = [
        'schedule_id',
        'user_id',
        'start_time',
        'end_time',
        'is_override',
        'override_by_user_id',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_override' => 'boolean',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ObservabilityOncallSchedule::class, 'schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function overrideBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'override_by_user_id');
    }

    public function scopeBySchedule($query, string $scheduleId)
    {
        return $query->where('schedule_id', $scheduleId);
    }

    public function scopeByUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeCurrent($query)
    {
        $now = now();
        return $query->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now);
    }

    public function scopeOverrides($query)
    {
        return $query->where('is_override', true);
    }

    public function isActive(): bool
    {
        $now = now();
        return $this->start_time <= $now && $this->end_time >= $now;
    }
}
