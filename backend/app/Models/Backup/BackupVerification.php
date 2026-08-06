<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BackupVerification extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'backup_verifications';

    protected $fillable = [
        'backup_snapshot_id',
        'type',
        'status',
        'method',
        'verification_config',
        'details',
        'checksum_valid',
        'encryption_valid',
        'integrity_valid',
        'restore_successful',
        'error_message',
        'duration_seconds',
        'started_at',
        'completed_at',
        'metadata',
        'tenant_id',
        'verified_by',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'verification_config' => 'array',
        'metadata' => 'array',
        'checksum_valid' => 'boolean',
        'encryption_valid' => 'boolean',
        'integrity_valid' => 'boolean',
        'restore_successful' => 'boolean',
        'duration_seconds' => 'integer',
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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function start(): void
    {
        $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    public function pass(array $details = []): void
    {
        $this->update([
            'status' => 'passed',
            'completed_at' => now(),
            'duration_seconds' => $this->started_at ? $this->started_at->diffInSeconds(now()) : 0,
            'details' => $details,
        ]);
    }

    public function fail(string $errorMessage, array $details = []): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'duration_seconds' => $this->started_at ? $this->started_at->diffInSeconds(now()) : 0,
            'error_message' => $errorMessage,
            'details' => $details,
        ]);
    }

    public function isPassed(): bool
    {
        return $this->status === 'passed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
