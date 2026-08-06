<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\DTO\DevSecOps\PipelineDTO;
use App\DTO\DevSecOps\PipelineRunDTO;
use App\Models\DevSecOps\DevSecOpsPipeline;
use App\Models\DevSecOps\DevSecOpsPipelineRun;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PipelineService extends DevSecOpsBaseService
{
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = DevSecOpsPipeline::with('environment', 'runs');

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['provider'])) {
            $query->ofProvider($filters['provider']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('name');

        return $this->paginate($query, $perPage);
    }

    public function getActive(): Collection
    {
        return DevSecOpsPipeline::active()
            ->with('environment')
            ->orderBy('name')
            ->get();
    }

    public function getById(string $id): ?DevSecOpsPipeline
    {
        return DevSecOpsPipeline::with('environment', 'runs')->find($id);
    }

    public function getBySlug(string $slug): ?DevSecOpsPipeline
    {
        return DevSecOpsPipeline::where('slug', $slug)->first();
    }

    public function create(PipelineDTO $dto): DevSecOpsPipeline
    {
        $data = $dto->toArray();
        $data['slug'] = $this->generateUniqueSlug($data['slug'], 'devsecops_pipelines');
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $pipeline = DevSecOpsPipeline::create($data);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_PIPELINE,
            action: 'created',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $pipeline->id,
            resourceType: DevSecOpsPipeline::class,
            resourceName: $pipeline->name,
            message: "Pipeline '{$pipeline->name}' created",
        );

        return $pipeline;
    }

    public function update(string $id, PipelineDTO $dto): ?DevSecOpsPipeline
    {
        $pipeline = $this->getById($id);

        if (!$pipeline) {
            return null;
        }

        $oldValues = $pipeline->toArray();
        $pipeline->update($dto->toArray());
        $pipeline->update(['updated_by' => auth()->id()]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_PIPELINE,
            action: 'updated',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $pipeline->id,
            resourceType: DevSecOpsPipeline::class,
            resourceName: $pipeline->name,
            changes: [
                'old' => $oldValues,
                'new' => $pipeline->toArray(),
            ],
            message: "Pipeline '{$pipeline->name}' updated",
        );

        return $pipeline->fresh();
    }

    public function delete(string $id): bool
    {
        $pipeline = $this->getById($id);

        if (!$pipeline) {
            return false;
        }

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_PIPELINE,
            action: 'deleted',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $pipeline->id,
            resourceType: DevSecOpsPipeline::class,
            resourceName: $pipeline->name,
            message: "Pipeline '{$pipeline->name}' deleted",
        );

        return $pipeline->delete();
    }

    public function toggleActive(string $id): ?DevSecOpsPipeline
    {
        $pipeline = $this->getById($id);

        if (!$pipeline) {
            return null;
        }

        $pipeline->update([
            'is_active' => !$pipeline->is_active,
            'updated_by' => auth()->id(),
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_PIPELINE,
            action: $pipeline->is_active ? 'activated' : 'deactivated',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $pipeline->id,
            resourceType: DevSecOpsPipeline::class,
            resourceName: $pipeline->name,
            message: "Pipeline '{$pipeline->name}' " . ($pipeline->is_active ? 'activated' : 'deactivated'),
        );

        return $pipeline->fresh();
    }

    public function trigger(string $id, array $params = []): ?DevSecOpsPipelineRun
    {
        $pipeline = $this->getById($id);

        if (!$pipeline || !$pipeline->is_active) {
            return null;
        }

        $lastRun = $pipeline->runs()->max('run_number') ?? 0;
        $runNumber = $lastRun + 1;

        $run = $pipeline->runs()->create([
            'run_number' => (string) $runNumber,
            'status' => 'pending',
            'trigger' => $params['trigger'] ?? 'api',
            'branch' => $params['branch'] ?? $pipeline->branch,
            'commit_sha' => $params['commit_sha'] ?? null,
            'commit_message' => $params['commit_message'] ?? null,
            'author' => $params['author'] ?? auth()->user()?->name,
            'triggered_by' => auth()->id(),
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_PIPELINE,
            action: 'triggered',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $run->id,
            resourceType: DevSecOpsPipelineRun::class,
            resourceName: "Run #{$runNumber} of {$pipeline->name}",
            metadata: ['pipeline_id' => $pipeline->id, 'trigger' => $run->trigger],
            message: "Pipeline '{$pipeline->name}' triggered manually",
        );

        return $run;
    }

    public function updateRunStatus(string $runId, string $status, array $metadata = []): ?DevSecOpsPipelineRun
    {
        $run = DevSecOpsPipelineRun::find($runId);

        if (!$run) {
            return null;
        }

        $updateData = ['status' => $status];

        if ($status === 'running' && !$run->started_at) {
            $updateData['started_at'] = now();
        }

        if (in_array($status, ['success', 'failed', 'cancelled', 'timeout'])) {
            $updateData['completed_at'] = now();
            if ($run->started_at) {
                $updateData['duration'] = now()->diffInSeconds($run->started_at);
            }
        }

        if (!empty($metadata)) {
            $updateData['metadata'] = array_merge($run->metadata ?? [], $metadata);
        }

        $run->update($updateData);

        if (in_array($status, ['success', 'failed', 'cancelled', 'timeout'])) {
            $this->logActivity(
                type: DevSecOpsActivityLog::TYPE_PIPELINE,
                action: 'completed',
                status: $status === 'success' ? DevSecOpsActivityLog::STATUS_SUCCESS : DevSecOpsActivityLog::STATUS_FAILED,
                resourceId: $run->id,
                resourceType: DevSecOpsPipelineRun::class,
                resourceName: "Run #{$run->run_number}",
                metadata: ['status' => $status, 'duration' => $run->duration],
                message: "Pipeline run #{$run->run_number} {$status}",
            );
        }

        return $run->fresh();
    }

    public function getRuns(string $pipelineId, int $perPage = 15)
    {
        return DevSecOpsPipelineRun::where('pipeline_id', $pipelineId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getRunById(string $id): ?DevSecOpsPipelineRun
    {
        return DevSecOpsPipelineRun::with('pipeline', 'securityScans', 'artifacts')->find($id);
    }
}
