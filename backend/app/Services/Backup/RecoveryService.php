<?php

declare(strict_types=1);

namespace App\Services\Backup;

use App\Enums\Backup\RecoveryStatus;
use App\Models\Backup\RecoveryJob;
use App\Models\Backup\BackupSnapshot;
use App\Models\Backup\BackupAuditLog;
use App\DTO\Backup\RecoveryJobDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RecoveryService
{
    public function index(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = RecoveryJob::with('backupSnapshot');

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['type'])) {
            $query->byType($filters['type']);
        }

        if (isset($filters['environment'])) {
            $query->byEnvironment($filters['environment']);
        }

        if (isset($filters['backup_snapshot_id'])) {
            $query->where('backup_snapshot_id', $filters['backup_snapshot_id']);
        }

        $query->orderByDesc('created_at');

        return $query->paginate($perPage);
    }

    public function getActiveJobs(): Collection
    {
        return RecoveryJob::whereIn('status', [
            RecoveryStatus::PENDING->value,
            RecoveryStatus::RUNNING->value,
        ])->orderBy('created_at')->get();
    }

    public function getRecentJobs(int $limit = 10): Collection
    {
        return RecoveryJob::with('backupSnapshot')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getFailedJobs(int $limit = 10): Collection
    {
        return RecoveryJob::failed()
            ->with('backupSnapshot')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function findOrFail(string $id): RecoveryJob
    {
        return RecoveryJob::with('backupSnapshot')->findOrFail($id);
    }

    public function create(array $data): RecoveryJob
    {
        $recoveryJob = RecoveryJob::create($data);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_RESTORE_INITIATED,
            severity: 'info',
            category: BackupAuditLog::CATEGORY_RECOVERY,
            message: "Recovery job '{$recoveryJob->name}' initiated",
            referenceId: $recoveryJob->id,
            referenceType: 'recovery_job',
            eventData: [
                'type' => $recoveryJob->type,
                'backup_snapshot_id' => $recoveryJob->backup_snapshot_id,
                'environment' => $recoveryJob->environment,
            ],
        );

        return $recoveryJob;
    }

    public function createFromSnapshot(
        string $snapshotId,
        string $name,
        string $type,
        array $options = [],
        ?string $userId = null
    ): RecoveryJob {
        $snapshot = BackupSnapshot::findOrFail($snapshotId);

        $recoveryJob = RecoveryJob::create([
            'backup_snapshot_id' => $snapshotId,
            'name' => $name,
            'type' => $type,
            'status' => RecoveryStatus::PENDING->value,
            'destination_type' => $options['destination_type'] ?? 'original',
            'destination_config' => $options['destination_config'] ?? null,
            'point_in_time' => $options['point_in_time'] ?? null,
            'restore_options' => $options['restore_options'] ?? null,
            'target_database' => $options['target_database'] ?? null,
            'target_path' => $options['target_path'] ?? null,
            'environment' => $snapshot->environment,
            'initiated_by' => $userId,
        ]);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_RESTORE_INITIATED,
            severity: 'info',
            category: BackupAuditLog::CATEGORY_RECOVERY,
            message: "Recovery initiated from snapshot '{$snapshot->name}'",
            referenceId: $recoveryJob->id,
            referenceType: 'recovery_job',
            eventData: [
                'snapshot_id' => $snapshotId,
                'type' => $type,
            ],
        );

        return $recoveryJob;
    }

    public function start(string $id): RecoveryJob
    {
        $recoveryJob = $this->findOrFail($id);
        $recoveryJob->start();

        return $recoveryJob->fresh();
    }

    public function complete(
        string $id,
        int $sizeRestored = 0,
        int $filesRestored = 0,
        int $recordsRestored = 0
    ): RecoveryJob {
        $recoveryJob = $this->findOrFail($id);
        $recoveryJob->complete($sizeRestored, $filesRestored, $recordsRestored);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_RESTORE_COMPLETED,
            severity: 'info',
            category: BackupAuditLog::CATEGORY_RECOVERY,
            message: "Recovery job '{$recoveryJob->name}' completed successfully",
            referenceId: $recoveryJob->id,
            referenceType: 'recovery_job',
            eventData: [
                'size_restored' => $sizeRestored,
                'files_restored' => $filesRestored,
                'records_restored' => $recordsRestored,
                'duration_seconds' => $recoveryJob->duration_seconds,
            ],
        );

        return $recoveryJob->fresh();
    }

    public function fail(string $id, string $errorMessage): RecoveryJob
    {
        $recoveryJob = $this->findOrFail($id);
        $recoveryJob->fail($errorMessage);

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_RESTORE_COMPLETED,
            severity: 'error',
            category: BackupAuditLog::CATEGORY_RECOVERY,
            message: "Recovery job '{$recoveryJob->name}' failed: {$errorMessage}",
            referenceId: $recoveryJob->id,
            referenceType: 'recovery_job',
            eventData: ['error' => $errorMessage],
        );

        return $recoveryJob->fresh();
    }

    public function verify(string $id): RecoveryJob
    {
        $recoveryJob = $this->findOrFail($id);
        $recoveryJob->verify();

        BackupAuditLog::log(
            eventType: BackupAuditLog::EVENT_RECOVERY_VERIFIED,
            severity: 'info',
            category: BackupAuditLog::CATEGORY_RECOVERY,
            message: "Recovery job '{$recoveryJob->name}' verified",
            referenceId: $recoveryJob->id,
            referenceType: 'recovery_job',
        );

        return $recoveryJob->fresh();
    }

    public function cancel(string $id): RecoveryJob
    {
        $recoveryJob = $this->findOrFail($id);
        $recoveryJob->cancel();

        return $recoveryJob->fresh();
    }

    public function addLog(string $id, string $message, string $level = 'info'): RecoveryJob
    {
        $recoveryJob = $this->findOrFail($id);
        $recoveryJob->addLog($message, $level);

        return $recoveryJob->fresh();
    }

    public function delete(string $id): bool
    {
        $recoveryJob = $this->findOrFail($id);
        return $recoveryJob->delete();
    }

    public function getSummary(string $environment = 'production'): array
    {
        $jobs = RecoveryJob::byEnvironment($environment)->get();

        return [
            'total' => $jobs->count(),
            'successful' => $jobs->where('status', RecoveryStatus::COMPLETED->value)->count(),
            'failed' => $jobs->where('status', RecoveryStatus::FAILED->value)->count(),
            'pending' => $jobs->where('status', RecoveryStatus::PENDING->value)->count(),
            'running' => $jobs->where('status', RecoveryStatus::RUNNING->value)->count(),
            'verified' => $jobs->where('status', RecoveryStatus::VERIFIED->value)->count(),
            'by_type' => [
                'full' => $jobs->where('type', 'full')->count(),
                'partial' => $jobs->where('type', 'partial')->count(),
                'file' => $jobs->where('type', 'file')->count(),
                'database' => $jobs->where('type', 'database')->count(),
                'point_in_time' => $jobs->where('type', 'point_in_time')->count(),
            ],
        ];
    }
}
