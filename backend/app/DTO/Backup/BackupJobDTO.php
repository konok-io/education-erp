<?php

declare(strict_types=1);

namespace App\DTO\Backup;

use App\Models\Backup\BackupJob;
use Spatie\DataTransferObject\DataTransferObject;

class BackupJobDTO extends DataTransferObject
{
    public string $id;
    public string $name;
    public string $type;
    public string $status;
    public string $source_type;
    public ?array $source_config;
    public string $destination_type;
    public ?array $destination_config;
    public string $encryption;
    public ?string $encryption_key_id;
    public int $size_bytes;
    public int $file_count;
    public ?string $checksum;
    public string $compression_algorithm;
    public int $compression_level;
    public ?string $retention_policy;
    public ?string $scheduled_at;
    public ?string $started_at;
    public ?string $completed_at;
    public ?string $error_message;
    public ?array $metadata;
    public string $environment;
    public ?string $region;
    public bool $is_immutable;
    public bool $verified;
    public ?string $verified_at;
    public string $created_at;
    public string $updated_at;
    public string $formatted_size;
    public int $duration_seconds;

    public static function fromModel(BackupJob $backupJob): self
    {
        return new self(
            id: $backupJob->id,
            name: $backupJob->name,
            type: $backupJob->type,
            status: $backupJob->status,
            source_type: $backupJob->source_type,
            source_config: $backupJob->source_config,
            destination_type: $backupJob->destination_type,
            destination_config: $backupJob->destination_config,
            encryption: $backupJob->encryption,
            encryption_key_id: $backupJob->encryption_key_id,
            size_bytes: $backupJob->size_bytes,
            file_count: $backupJob->file_count,
            checksum: $backupJob->checksum,
            compression_algorithm: $backupJob->compression_algorithm,
            compression_level: $backupJob->compression_level,
            retention_policy: $backupJob->retention_policy,
            scheduled_at: $backupJob->scheduled_at?->toIso8601String(),
            started_at: $backupJob->started_at?->toIso8601String(),
            completed_at: $backupJob->completed_at?->toIso8601String(),
            error_message: $backupJob->error_message,
            metadata: $backupJob->metadata,
            environment: $backupJob->environment,
            region: $backupJob->region,
            is_immutable: $backupJob->is_immutable,
            verified: $backupJob->verified,
            verified_at: $backupJob->verified_at?->toIso8601String(),
            created_at: $backupJob->created_at->toIso8601String(),
            updated_at: $backupJob->updated_at->toIso8601String(),
            formatted_size: $backupJob->formatted_size,
            duration_seconds: $backupJob->duration_seconds,
        );
    }

    public static function fromCollection(array $backupJobs): array
    {
        return array_map(fn($job) => self::fromModel($job), $backupJobs);
    }
}
