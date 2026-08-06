<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Enums\Backup\DRSiteType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DRSite extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'dr_sites';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'status',
        'region',
        'zone',
        'infrastructure_config',
        'endpoint',
        'connection_config',
        'role',
        'is_primary',
        'auto_failover_enabled',
        'health_check_endpoint',
        'health_check_interval_seconds',
        'health_status',
        'last_health_check',
        'recovery_time_target_seconds',
        'recovery_point_target_seconds',
        'metadata',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'infrastructure_config' => 'array',
        'connection_config' => 'array',
        'metadata' => 'array',
        'health_check_interval_seconds' => 'integer',
        'recovery_time_target_seconds' => 'integer',
        'recovery_point_target_seconds' => 'integer',
        'is_primary' => 'boolean',
        'auto_failover_enabled' => 'boolean',
        'last_health_check' => 'datetime',
    ];

    public function recoveryDrills(): HasMany
    {
        return $this->hasMany(RecoveryDrill::class, 'dr_site_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeStandby($query)
    {
        return $query->whereIn('type', [
            DRSiteType::SECONDARY->value,
            DRSiteType::DR->value,
            DRSiteType::HOT->value,
            DRSiteType::WARM->value,
            DRSiteType::COLD->value,
        ]);
    }

    public function updateHealthStatus(string $status): void
    {
        $this->update([
            'health_status' => $status,
            'last_health_check' => now(),
        ]);
    }

    public function enableAutoFailover(): void
    {
        $this->update(['auto_failover_enabled' => true]);
    }

    public function disableAutoFailover(): void
    {
        $this->update(['auto_failover_enabled' => false]);
    }

    public function isHealthy(): bool
    {
        return $this->health_status === 'healthy';
    }

    public function canFailover(): bool
    {
        return $this->status === 'active' && $this->isHealthy();
    }

    public function getFormattedRTOTargetAttribute(): string
    {
        $seconds = $this->recovery_time_target_seconds;
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }
        if ($seconds < 3600) {
            return round($seconds / 60) . ' minutes';
        }
        return round($seconds / 3600, 1) . ' hours';
    }

    public function getFormattedRPOTargetAttribute(): string
    {
        $seconds = $this->recovery_point_target_seconds;
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }
        return round($seconds / 60) . ' minutes';
    }
}
