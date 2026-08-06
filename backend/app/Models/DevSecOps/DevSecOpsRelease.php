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

class DevSecOpsRelease extends Model
{
    use HasFactory, HasUuids, HasUuid;

    protected $table = 'devsecops_releases';

    protected $fillable = [
        'name',
        'version',
        'type',
        'description',
        'status',
        'channel',
        'git_tag',
        'git_commit',
        'changelog',
        'breaking_changes',
        'known_issues',
        'upgrade_guide',
        'artifacts',
        'metadata',
        'released_at',
        'eol_at',
        'is_prerelease',
        'is_draft',
        'created_by',
        'released_by',
        'environment_id',
    ];

    protected $casts = [
        'changelog' => 'array',
        'breaking_changes' => 'array',
        'known_issues' => 'array',
        'upgrade_guide' => 'array',
        'artifacts' => 'array',
        'metadata' => 'array',
        'released_at' => 'datetime',
        'eol_at' => 'datetime',
        'is_prerelease' => 'boolean',
        'is_draft' => 'boolean',
    ];

    public function environment(): BelongsTo
    {
        return $this->belongsTo(DevSecOpsEnvironment::class, 'environment_id');
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(DevSecOpsDeployment::class, 'release_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOfChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    public function scopePublished($query)
    {
        return $query->where('status', '!=', 'draft');
    }

    public function scopeLts($query)
    {
        return $query->where('status', 'lts');
    }

    public function isPublished(): bool
    {
        return $this->status !== 'draft';
    }

    public function isStable(): bool
    {
        return $this->status === 'stable' || $this->status === 'lts';
    }

    public function isDeprecated(): bool
    {
        return $this->status === 'deprecated' || $this->status === 'archived';
    }
}
