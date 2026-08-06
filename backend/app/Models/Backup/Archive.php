<?php

declare(strict_types=1);

namespace App\Models\Backup;

use App\Enums\Backup\ArchiveType;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Archive extends Model
{
    use HasUuids, HasUuid, SoftDeletes;

    protected $table = 'archives';

    protected $fillable = [
        'name',
        'type',
        'status',
        'source_type',
        'source_config',
        'archive_location',
        'storage_provider_type',
        'storage_provider_id',
        'size_bytes',
        'record_count',
        'format',
        'is_encrypted',
        'encryption_algorithm',
        'archived_at',
        'expires_at',
        'is_legal_hold',
        'legal_hold_reason',
        'retention_policy',
        'metadata',
        'environment',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'source_config' => 'array',
        'retention_policy' => 'array',
        'metadata' => 'array',
        'size_bytes' => 'integer',
        'record_count' => 'integer',
        'is_encrypted' => 'boolean',
        'is_legal_hold' => 'boolean',
        'archived_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function storageProvider(): BelongsTo
    {
        return $this->belongsTo(StorageProvider::class, 'storage_provider_id');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    public function scopeLegalHold($query)
    {
        return $query->where('is_legal_hold', true);
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

    public function placeOnLegalHold(string $reason): void
    {
        $this->update([
            'is_legal_hold' => true,
            'legal_hold_reason' => $reason,
        ]);
    }

    public function releaseLegalHold(): void
    {
        $this->update([
            'is_legal_hold' => false,
            'legal_hold_reason' => null,
        ]);
    }
}
