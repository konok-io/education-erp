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

class DevSecOpsEnvironment extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_environments';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'cluster',
        'namespace',
        'config',
        'variables',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'config' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function pipelines(): HasMany
    {
        return $this->hasMany(DevSecOpsPipeline::class, 'environment_id');
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(DevSecOpsDeployment::class, 'environment_id');
    }

    public function gitopsConfigs(): HasMany
    {
        return $this->hasMany(DevSecOpsGitopsConfig::class, 'environment_id');
    }

    public function releases(): HasMany
    {
        return $this->hasMany(DevSecOpsRelease::class, 'environment_id');
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
}
