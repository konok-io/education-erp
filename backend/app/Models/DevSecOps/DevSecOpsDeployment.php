<?php

declare(strict_types=1);

namespace App\Models\DevSecOps;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevSecOpsDeployment extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_deployments';

    protected $fillable = [
        'environment_id',
        'pipeline_run_id',
        'release_id',
        'version',
        'strategy',
        'status',
        'namespace',
        'config',
        'replicas',
        'resources',
        'health_checks',
        'rollback_config',
        'previous_version',
        'commit_sha',
        'started_at',
        'completed_at',
        'duration',
        'metrics',
        'error_message',
        'auto_rollback',
        'deployed_by',
        'rollback_by',
    ];

    protected $casts = [
        'config' => 'array',
        'replicas' => 'array',
        'resources' => 'array',
        'health_checks' => 'array',
        'rollback_config' => 'array',
        'metrics' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration' => 'integer',
        'auto_rollback' => 'boolean',
    ];

    public function environment(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsEnvironment::class, 'environment_id');
    }

    public function pipelineRun(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsPipelineRun::class, 'pipeline_run_id');
    }

    public function deployedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deployed_by');
    }

    public function rollbackBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rollback_by');
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfStrategy($query, string $strategy)
    {
        return $query->where('strategy', $strategy);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function isActive(): bool
    {
        return $this->status === 'deployed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRolledBack(): bool
    {
        return $this->status === 'rolled_back';
    }
}
