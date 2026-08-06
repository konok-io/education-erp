<?php

declare(strict_types=1);

namespace App\Models\DevSecOps;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevSecOpsPipelineRun extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_pipeline_runs';

    protected $fillable = [
        'pipeline_id',
        'run_number',
        'status',
        'trigger',
        'branch',
        'commit_sha',
        'commit_message',
        'author',
        'stages',
        'jobs',
        'logs',
        'started_at',
        'completed_at',
        'duration',
        'artifacts',
        'metadata',
        'triggered_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'stages' => 'array',
        'jobs' => 'array',
        'artifacts' => 'array',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'approved_at' => 'datetime',
        'duration' => 'integer',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsPipeline::class, 'pipeline_id');
    }

    public function securityScans(): HasMany
    {
        return $this->hasMany(DevSecOpsSecurityScan::class, 'pipeline_run_id');
    }

    public function artifacts(): HasMany
    {
        return $this->hasMany(DevSecOpsArtifact::class, 'pipeline_run_id');
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(DevSecOpsDeployment::class, 'pipeline_run_id');
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
