<?php

declare(strict_types=1);

namespace App\Services\Backup;

use App\Enums\Backup\BackupStatus;
use App\Models\Backup\BackupJob;
use App\Models\Backup\BackupSnapshot;
use App\Models\Backup\BackupAuditLog;
use App\DTO\Backup\BackupJobDTO;
use App\DTO\Backup\BackupSnapshotDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BackupService
{
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BackupJob::query();

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['environment'])) {
            $query->byEnvironment($filters['environment']);
        }

        if (isset($filters['source_type'])) {
            $query->where('source_type', $filters['source_type']);
        }

        if (isset($filters['is_immutable'])) {
            $query->where('is_immutable', $filters['is_immutable']);
        }

        if (isset($filters['verified'])) {
            $query->where('verified', $filters['verified']);
        }

        $query->orderByDesc('created_at');

        return $query->paginate($perPage);
    }

    public function getActiveJobs(): Collection
    {
        return BackupJob::whereIn('status', [
            BackupStatus::PENDING->value,
            BackupStatus::RUNNING->value,
        ])->orderBy('scheduled_at')->get();
    }

    public function getRecentJobs(int $limit = 10): Collection
    {
        return BackupJob::orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getFailedJobs(int $limit = 10): Collection
    {
        return BackupJob::failed()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function findOrFail(string $id): BackupJob
    {
        return BackupJob::findOrFail($id);
    }

    public function create(array $data): BackupJob
    {
        $backupJob = BackupJob::create($data);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_BACKUP_STARTED,
            severity: 'info',
            category: BackupAuditLog::CATEGORY_BACKUP,
            message: "Backup job '{$backupJob->name}' created",
            referenceId: $backupJob->id,
            referenceType: 'backup_job',
            eventData: ['type' => $backupJob->type, 'environment' => $backupJob->environment],
        );

        return $backupJob;
    }

    public function start(string $id): BackupJob
    {
        $backupJob = $this->findOrFail($id);
        $backupJob->start();

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_BACKUP_STARTED,
            severity: 'info',
            category: BackupAuditLog::CATEGORY_BACKUP,
            message: "Backup job '{$backupJob->name}' started",
            referenceId: $backupJob->id,
            referenceType: 'backup_job',
            eventData: ['type' => $backupJob->type],
        );

        return $backupJob;
    }

    public function complete(
        string $id,
        ?string $checksum = null,
        int $sizeBytes = 0,
        int $fileCount = 0
    ): BackupJob {
        $backupJob = $this->findOrFail($id);
        $backupJob->complete($checksum, $sizeBytes, $fileCount);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_BACKUP_COMPLETED,
            severity: 'info',
            category: BackupAuditLog::CATEGORY_BACKUP,
            message: "Backup job '{$backupJob->name}' completed successfully",
            referenceId: $backupJob->id,
            referenceType: 'backup_job',
            eventData: [
                'type' => $backupJob->type,
                'size_bytes' => $sizeBytes,
                'file_count' => $fileCount,
                'duration_seconds' => $backupJob->duration_seconds,
            ],
        );

        return $backupJob->fresh();
    }

    public function fail(string $id, string $errorMessage): BackupJob
    {
        $backupJob = $this->findOrFail($id);
        $backupJob->fail($errorMessage);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_BACKUP_FAILED,
            severity: 'error',
            category: BackupAuditLog::CATEGORY_BACKUP,
            message: "Backup job '{$backupJob->name}' failed: {$errorMessage}",
            referenceId: $backupJob->id,
            referenceType: 'backup_job',
            eventData: ['error' => $errorMessage],
        );

        return $backupJob->fresh();
    }

    public function cancel(string $id): BackupJob
    {
        $backupJob = $this->findOrFail($id);
        $backupJob->cancel();

        return $backupJob->fresh();
    }

    public function markVerified(string $id): BackupJob
    {
        $backupJob = $this->findOrFail($id);
        $backupJob->markVerified();

        return $backupJob->fresh();
    }

    public function update(string $id, array $data): BackupJob
    {
        $backupJob = $this->findOrFail($id);
        $backupJob->update($data);

        return $backupJob->fresh();
    }

    public function delete(string $id): bool
    {
        $backupJob = $this->findOrFail($id);

        if ($backupJob->is_immutable && $backupJob->isImmutable()) {
            throw new \Exception('Cannot delete immutable backup');
        }

        return $backupJob->delete();
    }

    public function getSummary(string $environment = 'production'): array
    {
        $jobs = BackupJob::byEnvironment($environment)->get();

        return [
            'total' => $jobs->count(),
            'successful' => $jobs->where('status', BackupStatus::COMPLETED->value)->count(),
            'failed' => $jobs->where('status', BackupStatus::FAILED->value)->count(),
            'pending' => $jobs->where('status', BackupStatus::PENDING->value)->count(),
            'running' => $jobs->where('status', BackupStatus::RUNNING->value)->count(),
            'verified' => $jobs->where('verified', true)->count(),
            'by_type' => [
                'full' => $jobs->where('type', 'full')->count(),
                'incremental' => $jobs->where('type', 'incremental')->count(),
                'differential' => $jobs->where('type', 'differential')->count(),
                'snapshot' => $jobs->where('type', 'snapshot')->count(),
            ],
        ];
    }

    // Snapshot methods
    public function getSnapshots(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = BackupSnapshot::query();

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['backup_job_id'])) {
            $query->where('backup_job_id', $filters['backup_job_id']);
        }

        if (isset($filters['storage_provider'])) {
            $query->byStorageProvider($filters['storage_provider']);
        }

        $query->orderByDesc('created_at');

        return $query->paginate($perPage);
    }

    public function findSnapshotOrFail(string $id): BackupSnapshot
    {
        return BackupSnapshot::findOrFail($id);
    }
}
