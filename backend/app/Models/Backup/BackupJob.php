<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Enums\Backup\BackupStatus;
use App\Enums\Backup\BackupType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BackupJob extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'backup_jobs';

    protected $fillable = [
        'name',
        'type',
        'status',
        'source_type',
        'source_config',
        'destination_type',
        'destination_config',
        'encryption',
        'encryption_key_id',
        'size_bytes',
        'file_count',
        'checksum',
        'compression_algorithm',
        'compression_level',
        'retention_policy',
        'scheduled_at',
        'started_at',
        'completed_at',
        'error_message',
        'metadata',
        'environment',
        'region',
        'is_immutable',
        'verified',
        'verified_at',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'source_config' => 'array',
        'destination_config' => 'array',
        'metadata' => 'array',
        'size_bytes' => 'integer',
        'file_count' => 'integer',
        'compression_level' => 'integer',
        'is_immutable' => 'boolean',
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function snapshots(): HasMany
    {
        return $this->hasMany(BackupSnapshot::class, 'backup_job_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByEnvironment($query, string $environment)
    {
        return $query->where('environment', $environment);
    }

    public function scopePending($query)
    {
        return $query->where('status', BackupStatus::PENDING->value);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', BackupStatus::RUNNING->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', BackupStatus::COMPLETED->value);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', BackupStatus::FAILED->value);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }

    public function scopeUnverified($query)
    {
        return $query->where('verified', false);
    }

    public function start(): void
    {
        $this->update([
            'status' => BackupStatus::RUNNING->value,
            'started_at' => now(),
        ]);
    }

    public function complete(string $checksum = null, int $sizeBytes = 0, int $fileCount = 0): void
    {
        $this->update([
            'status' => BackupStatus::COMPLETED->value,
            'completed_at' => now(),
            'checksum' => $checksum,
            'size_bytes' => $sizeBytes,
            'file_count' => $fileCount,
        ]);
    }

    public function fail(string $errorMessage): void
    {
        $this->update([
            'status' => BackupStatus::FAILED->value,
            'completed_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => BackupStatus::CANCELLED->value,
            'completed_at' => now(),
        ]);
    }

    public function markVerified(): void
    {
        $this->update([
            'verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function getDurationSecondsAttribute(): int
    {
        if (!$this->started_at) {
            return 0;
        }

        $endTime = $this->completed_at ?? now();
        return $this->started_at->diffInSeconds($endTime);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size_bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
