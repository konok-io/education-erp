<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReplicationJob extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'replication_jobs';

    protected $fillable = [
        'name',
        'type',
        'status',
        'source_type',
        'source_config',
        'destination_type',
        'destination_config',
        'source_region',
        'destination_region',
        'replication_mode',
        'lag_seconds',
        'data_transferred_bytes',
        'last_sync_at',
        'started_at',
        'stopped_at',
        'error_message',
        'metadata',
        'environment',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'source_config' => 'array',
        'destination_config' => 'array',
        'metadata' => 'array',
        'lag_seconds' => 'integer',
        'data_transferred_bytes' => 'integer',
        'last_sync_at' => 'datetime',
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    public function scopeByRegion($query, string $sourceRegion, string $destinationRegion)
    {
        return $query->where('source_region', $sourceRegion)
            ->where('destination_region', $destinationRegion);
    }

    public function start(): void
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function pause(): void
    {
        $this->update([
            'status' => 'paused',
        ]);
    }

    public function resume(): void
    {
        $this->update([
            'status' => 'active',
        ]);
    }

    public function stop(): void
    {
        $this->update([
            'status' => 'stopped',
            'stopped_at' => now(),
        ]);
    }

    public function fail(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'stopped_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    public function updateSyncStatus(int $lagSeconds, int $dataTransferredBytes): void
    {
        $this->update([
            'lag_seconds' => $lagSeconds,
            'data_transferred_bytes' => $dataTransferredBytes,
            'last_sync_at' => now(),
        ]);
    }

    public function isHealthy(): bool
    {
        return $this->status === 'active' && $this->lag_seconds < 300;
    }

    public function getFormattedDataTransferredAttribute(): string
    {
        $bytes = $this->data_transferred_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
