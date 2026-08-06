<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BackupSnapshot extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'backup_snapshots';

    protected $fillable = [
        'backup_job_id',
        'name',
        'type',
        'status',
        'size_bytes',
        'checksum',
        'location',
        'storage_provider',
        'region',
        'expires_at',
        'archived_at',
        'is_immutable',
        'immutable_until',
        'metadata',
        'environment',
        'tenant_id',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size_bytes' => 'integer',
        'is_immutable' => 'boolean',
        'expires_at' => 'datetime',
        'archived_at' => 'datetime',
        'immutable_until' => 'datetime',
    ];

    public function backupJob(): BelongsTo
    {
        return $this->belongsTo(BackupJob::class, 'backup_job_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(BackupVerification::class, 'backup_snapshot_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    public function scopeImmutable($query)
    {
        return $query->where('is_immutable', true);
    }

    public function scopeByStorageProvider($query, string $provider)
    {
        return $query->where('storage_provider', $provider);
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

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isImmutable(): bool
    {
        if (!$this->is_immutable) {
            return false;
        }

        if (!$this->immutable_until) {
            return true;
        }

        return $this->immutable_until->isFuture();
    }
}
