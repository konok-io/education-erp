<?php

declare(strict_types=1);

namespace App\Models\Observability;

use App\Enums\Observability\NotificationType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservabilityNotificationChannel extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'observability_notification_channels';

    protected $fillable = [
        'name',
        'type',
        'status',
        'config',
        'settings',
        'allowed_severities',
        'allowed_services',
        'is_default',
        'environment',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'settings' => 'array',
        'allowed_severities' => 'array',
        'allowed_services' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'type' => NotificationType::class,
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(ObservabilityNotificationTemplate::class, 'channel_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(ObservabilityNotification::class, 'channel_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, NotificationType $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function isActive(): bool
    {
        return $this->is_active && $this->status === 'active';
    }

    public function supportsSeverity(string $severity): bool
    {
        if (empty($this->allowed_severities)) {
            return true;
        }
        
        return in_array($severity, $this->allowed_severities);
    }
}
