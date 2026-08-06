<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityDashboard extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_dashboards';

    protected $fillable = [
        'service_id',
        'name',
        'slug',
        'description',
        'type',
        'layout',
        'widgets',
        'filters',
        'environment',
        'tags',
        'is_default',
        'is_shared',
        'created_by_user_id',
    ];

    protected $casts = [
        'layout' => 'array',
        'widgets' => 'array',
        'filters' => 'array',
        'tags' => 'array',
        'is_default' => 'boolean',
        'is_shared' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('name');
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }
}
