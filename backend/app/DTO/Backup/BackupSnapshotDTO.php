<?php

declare(strict_types=1);

namespace App\DTO\Backup;

use App\Models\Backup\BackupSnapshot;
use Spatie\DataTransferObject\DataTransferObject;

class BackupSnapshotDTO extends DataTransferObject
{
    public string $id;
    public ?string $backup_job_id;
    public string $name;
    public string $type;
    public string $status;
    public int $size_bytes;
    public ?string $checksum;
    public string $location;
    public string $storage_provider;
    public ?string $region;
    public string $created_at;
    public ?string $expires_at;
    public ?string $archived_at;
    public bool $is_immutable;
    public ?string $immutable_until;
    public ?array $metadata;
    public string $environment;
    public string $formatted_size;
    public bool $is_expired;
    public bool $is_immutable_now;

    public static function fromModel(BackupSnapshot $snapshot): self
    {
        return new self(
            id: $snapshot->id,
            backup_job_id: $snapshot->backup_job_id,
            name: $snapshot->name,
            type: $snapshot->type,
            status: $snapshot->status,
            size_bytes: $snapshot->size_bytes,
            checksum: $snapshot->checksum,
            location: $snapshot->location,
            storage_provider: $snapshot->storage_provider,
            region: $snapshot->region,
            created_at: $snapshot->created_at->toIso8601String(),
            expires_at: $snapshot->expires_at?->toIso8601String(),
            archived_at: $snapshot->archived_at?->toIso8601String(),
            is_immutable: $snapshot->is_immutable,
            immutable_until: $snapshot->immutable_until?->toIso8601String(),
            metadata: $snapshot->metadata,
            environment: $snapshot->environment,
            formatted_size: $snapshot->formatted_size,
            is_expired: $snapshot->isExpired(),
            is_immutable_now: $snapshot->isImmutable(),
        );
    }

    public static function fromCollection(array $snapshots): array
    {
        return array_map(fn($snapshot) => self::fromModel($snapshot), $snapshots);
    }
}
