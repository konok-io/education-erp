<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\Models\DevSecOps\DevSecOpsActivityLog;

class ActivityLogService extends DevSecOpsBaseService
{
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = DevSecOpsActivityLog::query();

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['action'])) {
            $query->ofAction($filters['action']);
        }

        if (isset($filters['status'])) {
            $query->ofStatus($filters['status']);
        }

        if (isset($filters['resource_type'])) {
            $query->where('resource_type', $filters['resource_type']);
        }

        if (isset($filters['resource_id'])) {
            $query->where('resource_id', $filters['resource_id']);
        }

        if (isset($filters['actor_id'])) {
            $query->byActor($filters['actor_id']);
        }

        if (isset($filters['days'])) {
            $query->recent($filters['days']);
        }

        $query->orderBy('created_at', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function getByResource(string $resourceType, string $resourceId, int $perPage = 15)
    {
        return DevSecOpsActivityLog::forResource($resourceType, $resourceId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getRecent(int $days = 7, int $perPage = 15)
    {
        return DevSecOpsActivityLog::recent($days)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getStats(int $days = 30): array
    {
        $query = DevSecOpsActivityLog::where('created_at', '>=', now()->subDays($days));

        $totalActions = $query->count();
        $successfulActions = (clone $query)->where('status', DevSecOpsActivityLog::STATUS_SUCCESS)->count();
        $failedActions = (clone $query)->where('status', DevSecOpsActivityLog::STATUS_FAILED)->count();
        $warningActions = (clone $query)->where('status', DevSecOpsActivityLog::STATUS_WARNING)->count();

        $byType = DevSecOpsActivityLog::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $byAction = DevSecOpsActivityLog::where('created_at', '>=', now()->subDays($days))
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'action')
            ->toArray();

        return [
            'total_actions' => $totalActions,
            'successful_actions' => $successfulActions,
            'failed_actions' => $failedActions,
            'warning_actions' => $warningActions,
            'by_type' => $byType,
            'top_actions' => $byAction,
            'success_rate' => $totalActions > 0 ? round(($successfulActions / $totalActions) * 100, 2) : 0,
        ];
    }

    public function getPipelineStats(int $days = 30): array
    {
        return [
            'total_runs' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_PIPELINE)
                ->where('action', 'completed')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'successful_runs' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_PIPELINE)
                ->where('action', 'completed')
                ->where('status', DevSecOpsActivityLog::STATUS_SUCCESS)
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'failed_runs' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_PIPELINE)
                ->where('action', 'completed')
                ->where('status', DevSecOpsActivityLog::STATUS_FAILED)
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
        ];
    }

    public function getDeploymentStats(int $days = 30): array
    {
        return [
            'total_deployments' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_DEPLOYMENT)
                ->where('action', 'deployed')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'failed_deployments' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_DEPLOYMENT)
                ->where('action', 'failed')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'rollbacks' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_ROLLBACK)
                ->where('action', 'executed')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
        ];
    }

    public function getSecurityStats(int $days = 30): array
    {
        return [
            'total_scans' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_SECURITY_SCAN)
                ->where('action', 'completed')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'scans_with_vulnerabilities' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_SECURITY_SCAN)
                ->where('action', 'completed')
                ->where('status', DevSecOpsActivityLog::STATUS_WARNING)
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
            'failed_scans' => DevSecOpsActivityLog::ofType(DevSecOpsActivityLog::TYPE_SECURITY_SCAN)
                ->where('action', 'failed')
                ->where('created_at', '>=', now()->subDays($days))
                ->count(),
        ];
    }
}
