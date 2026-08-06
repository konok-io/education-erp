<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Backup;

use App\DTO\Backup\DRSummaryDTO;
use App\Http\Controllers\Controller;
use App\Models\Backup\BackupJob;
use App\Models\Backup\BackupSnapshot;
use App\Models\Backup\RecoveryJob;
use App\Models\Backup\ReplicationJob;
use App\Models\Backup\StorageProvider;
use App\Models\Backup\FailoverEvent;
use App\Services\Backup\BackupService;
use App\Services\Backup\RecoveryService;
use App\Services\Backup\FailoverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DRController extends Controller
{
    public function __construct(
        protected BackupService $backupService,
        protected RecoveryService $recoveryService,
        protected FailoverService $failoverService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');

        $summary = $this->getDRSummary($environment);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');

        $backupSummary = $this->backupService->getSummary($environment);
        $recoverySummary = $this->recoveryService->getSummary($environment);
        $failoverSummary = $this->failoverService->getSummary();

        $storageProviders = StorageProvider::active()->get();
        $totalStorageUsed = $storageProviders->sum('used_capacity_bytes');
        $totalStorageAvailable = $storageProviders->sum('available_capacity_bytes');
        $storageUsagePercentage = $totalStorageAvailable > 0
            ? round(($totalStorageUsed / ($totalStorageUsed + $totalStorageAvailable)) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'healthy',
                'backup' => $backupSummary,
                'recovery' => $recoverySummary,
                'failover' => $failoverSummary,
                'storage' => [
                    'total_used_bytes' => $totalStorageUsed,
                    'total_available_bytes' => $totalStorageAvailable,
                    'usage_percentage' => $storageUsagePercentage,
                    'providers_count' => $storageProviders->count(),
                ],
                'last_check' => now()->toIso8601String(),
            ],
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');

        $summary = $this->getDRSummary($environment);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    protected function getDRSummary(string $environment): array
    {
        $backupJobs = BackupJob::byEnvironment($environment)->get();
        $recoveries = RecoveryJob::byEnvironment($environment)->get();
        $replications = ReplicationJob::all();
        $failovers = FailoverEvent::orderByDesc('created_at')->limit(10)->get();
        $scheduledBackups = BackupJob::where('environment', $environment)
            ->where('status', 'pending')
            ->whereNotNull('scheduled_at')
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();
        $storageProviders = StorageProvider::active()->get();

        $totalStorageUsed = $storageProviders->sum('used_capacity_bytes');
        $totalStorageAvailable = $storageProviders->sum('available_capacity_bytes');
        $storageUsagePercentage = $totalStorageAvailable > 0
            ? round(($totalStorageUsed / ($totalStorageUsed + $totalStorageAvailable)) * 100, 2)
            : 0;

        return [
            'total_backups' => $backupJobs->count(),
            'successful_backups' => $backupJobs->where('status', 'completed')->count(),
            'failed_backups' => $backupJobs->where('status', 'failed')->count(),
            'pending_backups' => $backupJobs->where('status', 'pending')->count(),
            'running_backups' => $backupJobs->where('status', 'running')->count(),
            'verified_backups' => $backupJobs->where('verified', true)->count(),
            'total_recoveries' => $recoveries->count(),
            'successful_recoveries' => $recoveries->where('status', 'completed')->count(),
            'failed_recoveries' => $recoveries->where('status', 'failed')->count(),
            'active_replications' => $replications->where('status', 'active')->count(),
            'healthy_replications' => $replications->filter(fn($r) => $r->isHealthy())->count(),
            'failed_replications' => $replications->where('status', 'failed')->count(),
            'total_storage_used_bytes' => $totalStorageUsed,
            'total_storage_available_bytes' => $totalStorageAvailable,
            'storage_usage_percentage' => $storageUsagePercentage,
            'backup_by_type' => [
                'full' => $backupJobs->where('type', 'full')->count(),
                'incremental' => $backupJobs->where('type', 'incremental')->count(),
                'differential' => $backupJobs->where('type', 'differential')->count(),
                'snapshot' => $backupJobs->where('type', 'snapshot')->count(),
            ],
            'backup_by_status' => [
                'completed' => $backupJobs->where('status', 'completed')->count(),
                'failed' => $backupJobs->where('status', 'failed')->count(),
                'pending' => $backupJobs->where('status', 'pending')->count(),
                'running' => $backupJobs->where('status', 'running')->count(),
            ],
            'recent_failovers' => $failovers->map(fn($f) => [
                'id' => $f->id,
                'name' => $f->name,
                'type' => $f->type,
                'status' => $f->status,
                'initiated_at' => $f->initiated_at?->toIso8601String(),
                'completed_at' => $f->completed_at?->toIso8601String(),
            ])->toArray(),
            'next_scheduled_backups' => $scheduledBackups->map(fn($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'type' => $b->type,
                'scheduled_at' => $b->scheduled_at?->toIso8601String(),
            ])->toArray(),
        ];
    }
}
