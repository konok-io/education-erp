<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityRunbook extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_runbooks';

    protected $fillable = [
        'service_id',
        'title',
        'slug',
        'description',
        'content',
        'steps',
        'playbook_steps',
        'related_alerts',
        'tags',
        'severity',
        'is_active',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'steps' => 'array',
        'playbook_steps' => 'array',
        'related_alerts' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ObservabilityService::class, 'service_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByService($query, string $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }
}
