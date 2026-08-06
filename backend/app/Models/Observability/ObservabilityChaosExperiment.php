<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\ChaosExperimentType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityChaosExperiment extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_chaos_experiments';

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'type',
        'status',
        'target_config',
        'experiment_config',
        'duration_seconds',
        'scheduled_at',
        'started_at',
        'completed_at',
        'experimenter_user_id',
        'result',
        'is_active',
    ];

    protected $casts = [
        'target_config' => 'array',
        'experiment_config' => 'array',
        'result' => 'array',
        'is_active' => 'boolean',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'type' => ChaosExperimentType::class,
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function experimenter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'experimenter_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, ChaosExperimentType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function start(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function complete(?array $result = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'result' => $result,
        ]);
    }

    public function fail(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'result' => ['error' => $reason],
        ]);
    }
}
