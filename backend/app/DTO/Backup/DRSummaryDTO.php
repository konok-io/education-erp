<?php

declare(strict_types=1);

namespace App\DTO\Backup;

use Spatie\DataTransferObject\DataTransferObject;

class DRSummaryDTO extends DataTransferObject
{
    public int $total_backups;
    public int $successful_backups;
    public int $failed_backups;
    public int $pending_backups;
    public int $running_backups;
    public int $verified_backups;
    public int $total_recoveries;
    public int $successful_recoveries;
    public int $failed_recoveries;
    public int $active_replications;
    public int $healthy_replications;
    public int $failed_replications;
    public int $total_storage_used_bytes;
    public int $total_storage_available_bytes;
    public float $storage_usage_percentage;
    public array $backup_by_type;
    public array $backup_by_status;
    public array $recent_failovers;
    public array $next_scheduled_backups;

    public static function fromArray(array $data): self
    {
        return new self(
            total_backups: $data['total_backups'] ?? 0,
            successful_backups: $data['successful_backups'] ?? 0,
            failed_backups: $data['failed_backups'] ?? 0,
            pending_backups: $data['pending_backups'] ?? 0,
            running_backups: $data['running_backups'] ?? 0,
            verified_backups: $data['verified_backups'] ?? 0,
            total_recoveries: $data['total_recoveries'] ?? 0,
            successful_recoveries: $data['successful_recoveries'] ?? 0,
            failed_recoveries: $data['failed_recoveries'] ?? 0,
            active_replications: $data['active_replications'] ?? 0,
            healthy_replications: $data['healthy_replications'] ?? 0,
            failed_replications: $data['failed_replications'] ?? 0,
            total_storage_used_bytes: $data['total_storage_used_bytes'] ?? 0,
            total_storage_available_bytes: $data['total_storage_available_bytes'] ?? 0,
            storage_usage_percentage: $data['storage_usage_percentage'] ?? 0.0,
            backup_by_type: $data['backup_by_type'] ?? [],
            backup_by_status: $data['backup_by_status'] ?? [],
            recent_failovers: $data['recent_failovers'] ?? [],
            next_scheduled_backups: $data['next_scheduled_backups'] ?? [],
        );
    }

    public function getFormattedStorageUsed(): string
    {
        return $this->formatBytes($this->total_storage_used_bytes);
    }

    public function getFormattedStorageAvailable(): string
    {
        return $this->formatBytes($this->total_storage_available_bytes);
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
