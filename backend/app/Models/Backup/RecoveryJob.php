<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Enums\Backup\RecoveryStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecoveryJob extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'recovery_jobs';

    protected $fillable = [
        'backup_snapshot_id',
        'name',
        'type',
        'status',
        'destination_type',
        'destination_config',
        'point_in_time',
        'restore_options',
        'target_database',
        'target_path',
        'size_restored',
        'files_restored',
        'records_restored',
        'started_at',
        'completed_at',
        'duration_seconds',
        'error_message',
        'logs',
        'metadata',
        'environment',
        'tenant_id',
        'initiated_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'destination_config' => 'array',
        'restore_options' => 'array',
        'logs' => 'array',
        'metadata' => 'array',
        'size_restored' => 'integer',
        'files_restored' => 'integer',
        'records_restored' => 'integer',
        'duration_seconds' => 'integer',
        'point_in_time' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function backupSnapshot(): BelongsTo
    {
        return $this->belongsTo(BackupSnapshot::class, 'backup_snapshot_id');
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
        return $query->where('status', RecoveryStatus::PENDING->value);
    }

    public function scopeRunning($query)
    {
        return $query->where('status', RecoveryStatus::RUNNING->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', RecoveryStatus::COMPLETED->value);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', RecoveryStatus::FAILED->value);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', RecoveryStatus::VERIFIED->value);
    }

    public function start(): void
    {
        $this->update([
            'status' => RecoveryStatus::RUNNING->value,
            'started_at' => now(),
        ]);
    }

    public function complete(
        int $sizeRestored = 0,
        int $filesRestored = 0,
        int $recordsRestored = 0
    ): void {
        $this->update([
            'status' => RecoveryStatus::COMPLETED->value,
            'completed_at' => now(),
            'duration_seconds' => $this->started_at ? $this->started_at->diffInSeconds(now()) : 0,
            'size_restored' => $sizeRestored,
            'files_restored' => $filesRestored,
            'records_restored' => $recordsRestored,
        ]);
    }

    public function fail(string $errorMessage): void
    {
        $this->update([
            'status' => RecoveryStatus::FAILED->value,
            'completed_at' => now(),
            'duration_seconds' => $this->started_at ? $this->started_at->diffInSeconds(now()) : 0,
            'error_message' => $errorMessage,
        ]);
    }

    public function verify(): void
    {
        $this->update([
            'status' => RecoveryStatus::VERIFIED->value,
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => RecoveryStatus::CANCELLED->value,
            'completed_at' => now(),
        ]);
    }

    public function addLog(string $message, string $level = 'info'): void
    {
        $logs = $this->logs ?? [];
        $logs[] = [
            'timestamp' => now()->toIso8601String(),
            'level' => $level,
            'message' => $message,
        ];
        $this->update(['logs' => $logs]);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size_restored;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
