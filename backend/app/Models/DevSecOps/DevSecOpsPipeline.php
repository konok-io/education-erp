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

class DevSecOpsPipeline extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_pipelines';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'provider',
        'repository',
        'branch',
        'yaml_path',
        'stages',
        'config',
        'status',
        'timeout',
        'auto_trigger',
        'require_approval',
        'approval_roles',
        'min_coverage',
        'is_active',
        'environment_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'stages' => 'array',
        'config' => 'array',
        'approval_roles' => 'array',
        'auto_trigger' => 'boolean',
        'require_approval' => 'boolean',
        'min_coverage' => 'integer',
        'timeout' => 'integer',
        'is_active' => 'boolean',
    ];

    public function environment(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsEnvironment::class, 'environment_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(DevSecOpsPipelineRun::class, 'pipeline_id');
    }

    public function latestRun()
    {
        return $this->runs()->latest()->first();
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

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }
}
