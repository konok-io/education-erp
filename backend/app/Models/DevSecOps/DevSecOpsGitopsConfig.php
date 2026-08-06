<?php

declare(strict_types=1);

namespace App\Models\DevSecOps;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevSecOpsGitopsConfig extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_gitops_configs';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'provider',
        'repository',
        'path',
        'target_branch',
        'environment_id',
        'sync_policy',
        'auto_sync',
        'self_heal',
        'prune',
        'sync_interval',
        'kustomize',
        'helm',
        'values',
        'health_check_path',
        'last_sync_status',
        'last_synced_at',
        'drift_detection',
        'notifications',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'kustomize' => 'array',
        'helm' => 'array',
        'values' => 'array',
        'drift_detection' => 'array',
        'notifications' => 'array',
        'auto_sync' => 'boolean',
        'self_heal' => 'boolean',
        'prune' => 'boolean',
        'sync_interval' => 'integer',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function environment(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsEnvironment::class, 'environment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeAutomated($query)
    {
        return $query->where('sync_policy', 'automated');
    }

    public function isAutomated(): bool
    {
        return $this->sync_policy === 'automated';
    }

    public function isHealthy(): bool
    {
        return $this->last_sync_status === 'synced';
    }

    public function hasDrift(): bool
    {
        return $this->last_sync_status === 'drifted';
    }
}
