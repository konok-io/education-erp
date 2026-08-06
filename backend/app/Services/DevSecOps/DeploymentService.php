<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\DTO\DevSecOps\DeploymentDTO;
use App\Models\DevSecOps\DevSecOpsDeployment;
use App\Models\DevSecOps\DevSecOpsEnvironment;
use Illuminate\Support\Collection;

class DeploymentService extends DevSecOpsBaseService
{
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = DevSecOpsDeployment::with('environment', 'pipelineRun');

        if (isset($filters['environment_id'])) {
            $query->where('environment_id', $filters['environment_id']);
        }

        if (isset($filters['status'])) {
            $query->ofStatus($filters['status']);
        }

        if (isset($filters['strategy'])) {
            $query->ofStrategy($filters['strategy']);
        }

        if (isset($filters['version'])) {
            $query->where('version', $filters['version']);
        }

        $query->orderBy('created_at', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function getActive(): Collection
    {
        return DevSecOpsDeployment::with('environment')
            ->whereIn('status', ['deployed', 'monitoring'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getById(string $id): ?DevSecOpsDeployment
    {
        return DevSecOpsDeployment::with('environment', 'pipelineRun', 'deployedBy', 'rollbackBy')->find($id);
    }

    public function getByEnvironment(string $environmentId): Collection
    {
        return DevSecOpsDeployment::where('environment_id', $environmentId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(DeploymentDTO $dto): DevSecOpsDeployment
    {
        $data = $dto->toArray();
        $data['status'] = 'pending';
        $data['deployed_by'] = auth()->id();

        $deployment = DevSecOpsDeployment::create($data);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_DEPLOYMENT,
            action: 'created',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $deployment->id,
            resourceType: DevSecOpsDeployment::class,
            resourceName: "Deployment to {$deployment->environment->name}",
            metadata: ['version' => $deployment->version, 'strategy' => $deployment->strategy],
            message: "Deployment #{$deployment->id} created for {$deployment->version}",
        );

        return $deployment;
    }

    public function updateStatus(string $id, string $status, ?string $errorMessage = null): ?DevSecOpsDeployment
    {
        $deployment = $this->getById($id);

        if (!$deployment) {
            return null;
        }

        $updateData = ['status' => $status];

        if ($status === 'deploying' && !$deployment->started_at) {
            $updateData['started_at'] = now();
        }

        if (in_array($status, ['deployed', 'failed', 'rolled_back'])) {
            $updateData['completed_at'] = now();
            if ($deployment->started_at) {
                $updateData['duration'] = now()->diffInSeconds($deployment->started_at);
            }
        }

        if ($errorMessage) {
            $updateData['error_message'] = $errorMessage;
        }

        $deployment->update($updateData);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_DEPLOYMENT,
            action: $status,
            status: in_array($status, ['deployed']) ? DevSecOpsActivityLog::STATUS_SUCCESS : DevSecOpsActivityLog::STATUS_FAILED,
            resourceId: $deployment->id,
            resourceType: DevSecOpsDeployment::class,
            resourceName: "Deployment to {$deployment->environment->name}",
            metadata: ['status' => $status, 'duration' => $deployment->duration],
            message: "Deployment {$status}: {$errorMessage ?? 'No errors'}",
        );

        return $deployment->fresh();
    }

    public function rollback(string $id): ?DevSecOpsDeployment
    {
        $deployment = $this->getById($id);

        if (!$deployment || $deployment->isRolledBack()) {
            return null;
        }

        $previousDeployment = DevSecOpsDeployment::where('environment_id', $deployment->environment_id)
            ->where('id', '!=', $deployment->id)
            ->where('status', 'deployed')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$previousDeployment) {
            $this->logActivity(
                type: DevSecOpsActivityLog::TYPE_ROLLBACK,
                action: 'failed',
                status: DevSecOpsActivityLog::STATUS_FAILED,
                resourceId: $deployment->id,
                resourceType: DevSecOpsDeployment::class,
                resourceName: "Deployment to {$deployment->environment->name}",
                message: "Rollback failed: No previous deployment found",
            );
            return null;
        }

        $deployment->update([
            'status' => 'rolled_back',
            'rollback_by' => auth()->id(),
            'completed_at' => now(),
            'error_message' => 'Rolled back to version: ' . $previousDeployment->version,
        ]);

        if ($deployment->started_at) {
            $deployment->update(['duration' => now()->diffInSeconds($deployment->started_at)]);
        }

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_ROLLBACK,
            action: 'executed',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $deployment->id,
            resourceType: DevSecOpsDeployment::class,
            resourceName: "Deployment to {$deployment->environment->name}",
            metadata: [
                'rolled_back_to' => $previousDeployment->version,
                'previous_version' => $deployment->version,
            ],
            message: "Rolled back from {$deployment->version} to {$previousDeployment->version}",
        );

        return $deployment->fresh();
    }

    public function getLatestByEnvironment(string $environmentId): ?DevSecOpsDeployment
    {
        return DevSecOpsDeployment::where('environment_id', $environmentId)
            ->where('status', 'deployed')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function getHistory(string $environmentId, int $perPage = 15)
    {
        return DevSecOpsDeployment::where('environment_id', $environmentId)
            ->with('pipelineRun')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
