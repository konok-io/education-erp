<?php

declare(strict_types=1);

namespace App\DTO\Backup;

use App\Models\Backup\RecoveryJob;
use Spatie\DataTransferObject\DataTransferObject;

class RecoveryJobDTO extends DataTransferObject
{
    public string $id;
    public ?string $backup_snapshot_id;
    public string $name;
    public string $type;
    public string $status;
    public string $destination_type;
    public ?array $destination_config;
    public ?string $point_in_time;
    public ?array $restore_options;
    public ?string $target_database;
    public ?string $target_path;
    public int $size_restored;
    public int $files_restored;
    public int $records_restored;
    public ?string $started_at;
    public ?string $completed_at;
    public int $duration_seconds;
    public ?string $error_message;
    public ?array $logs;
    public ?array $metadata;
    public string $environment;
    public string $created_at;
    public string $updated_at;
    public string $formatted_size;

    public static function fromModel(RecoveryJob $recoveryJob): self
    {
        return new self(
            id: $recoveryJob->id,
            backup_snapshot_id: $recoveryJob->backup_snapshot_id,
            name: $recoveryJob->name,
            type: $recoveryJob->type,
            status: $recoveryJob->status,
            destination_type: $recoveryJob->destination_type,
            destination_config: $recoveryJob->destination_config,
            point_in_time: $recoveryJob->point_in_time?->toIso8601String(),
            restore_options: $recoveryJob->restore_options,
            target_database: $recoveryJob->target_database,
            target_path: $recoveryJob->target_path,
            size_restored: $recoveryJob->size_restored,
            files_restored: $recoveryJob->files_restored,
            records_restored: $recoveryJob->records_restored,
            started_at: $recoveryJob->started_at?->toIso8601String(),
            completed_at: $recoveryJob->completed_at?->toIso8601String(),
            duration_seconds: $recoveryJob->duration_seconds,
            error_message: $recoveryJob->error_message,
            logs: $recoveryJob->logs,
            metadata: $recoveryJob->metadata,
            environment: $recoveryJob->environment,
            created_at: $recoveryJob->created_at->toIso8601String(),
            updated_at: $recoveryJob->updated_at->toIso8601String(),
            formatted_size: $recoveryJob->formatted_size,
        );
    }

    public static function fromCollection(array $recoveryJobs): array
    {
        return array_map(fn($job) => self::fromModel($job), $recoveryJobs);
    }
}
