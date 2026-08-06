<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\ScheduleType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityOncallSchedule extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_oncall_schedules';

    protected $fillable = [
        'name',
        'service_id',
        'timezone',
        'rotation_type',
        'rotation_config',
        'is_active',
    ];

    protected $casts = [
        'rotation_config' => 'array',
        'is_active' => 'boolean',
        'rotation_type' => ScheduleType::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(ObservabilityOncallShift::class, 'schedule_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function getCurrentOnCallUser(): ?User
    {
        $now = now();
        $shift = $this->shifts()
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->where('is_override', false)
            ->first();

        return $shift?->user;
    }

    public function getNextOnCallUser(): ?User
    {
        $now = now();
        $shift = $this->shifts()
            ->where('start_time', '>', $now)
            ->where('is_override', false)
            ->orderBy('start_time', 'asc')
            ->first();

        return $shift?->user;
    }
}
