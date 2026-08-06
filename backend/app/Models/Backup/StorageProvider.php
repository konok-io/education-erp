<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageProvider extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'storage_providers';

    protected $fillable = [
        'name',
        'type',
        'status',
        'config',
        'region',
        'zone',
        'endpoint',
        'bucket',
        'path_prefix',
        'is_primary',
        'is_replicated',
        'total_capacity_bytes',
        'used_capacity_bytes',
        'available_capacity_bytes',
        'file_count',
        'latency_ms',
        'throughput_mbps',
        'encryption_config',
        'is_encrypted',
        'encryption_algorithm',
        'is_worm',
        'retention_days',
        'metadata',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'config' => 'array',
        'encryption_config' => 'array',
        'metadata' => 'array',
        'total_capacity_bytes' => 'integer',
        'used_capacity_bytes' => 'integer',
        'available_capacity_bytes' => 'integer',
        'file_count' => 'integer',
        'latency_ms' => 'integer',
        'throughput_mbps' => 'integer',
        'is_primary' => 'boolean',
        'is_replicated' => 'boolean',
        'is_encrypted' => 'boolean',
        'is_worm' => 'boolean',
        'retention_days' => 'integer',
    ];

    public function archives(): HasMany
    {
        return $this->hasMany(Archive::class, 'storage_provider_id');
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

    public function scopeReplicated($query)
    {
        return $query->where('is_replicated', true);
    }

    public function updateUsage(int $usedBytes, int $fileCount): void
    {
        $this->update([
            'used_capacity_bytes' => $usedBytes,
            'file_count' => $fileCount,
            'available_capacity_bytes' => $this->total_capacity_bytes
                ? $this->total_capacity_bytes - $usedBytes
                : null,
        ]);
    }

    public function getUsagePercentageAttribute(): float
    {
        if (!$this->total_capacity_bytes) {
            return 0;
        }

        return round(($this->used_capacity_bytes / $this->total_capacity_bytes) * 100, 2);
    }

    public function getFormattedTotalCapacityAttribute(): string
    {
        return $this->formatBytes($this->total_capacity_bytes);
    }

    public function getFormattedUsedCapacityAttribute(): string
    {
        return $this->formatBytes($this->used_capacity_bytes);
    }

    public function getFormattedAvailableCapacityAttribute(): string
    {
        return $this->formatBytes($this->available_capacity_bytes ?? 0);
    }

    protected function formatBytes(?int $bytes): string
    {
        if ($bytes === null) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
