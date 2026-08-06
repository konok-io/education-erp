<?php

declare(strict_types=1);

namespace App\Services\DevSecOps;

use App\DTO\DevSecOps\ReleaseDTO;
use App\Models\DevSecOps\DevSecOpsRelease;
use Illuminate\Support\Collection;

class ReleaseService extends DevSecOpsBaseService
{
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = DevSecOpsRelease::with('environment');

        if (isset($filters['status'])) {
            $query->ofStatus($filters['status']);
        }

        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['channel'])) {
            $query->ofChannel($filters['channel']);
        }

        if (isset($filters['is_draft'])) {
            $query->where('is_draft', $filters['is_draft']);
        }

        $query->orderBy('released_at', 'desc');

        return $this->paginate($query, $perPage);
    }

    public function getPublished(int $perPage = 15)
    {
        return DevSecOpsRelease::published()
            ->with('environment')
            ->orderBy('released_at', 'desc')
            ->paginate($perPage);
    }

    public function getLts(): Collection
    {
        return DevSecOpsRelease::lts()
            ->with('environment')
            ->orderBy('released_at', 'desc')
            ->get();
    }

    public function getById(string $id): ?DevSecOpsRelease
    {
        return DevSecOpsRelease::with('environment', 'deployments', 'creator', 'releasedBy')->find($id);
    }

    public function getByVersion(string $version): ?DevSecOpsRelease
    {
        return DevSecOpsRelease::where('version', $version)->first();
    }

    public function create(ReleaseDTO $dto): DevSecOpsRelease
    {
        $data = $dto->toArray();
        $data['created_by'] = auth()->id();

        $release = DevSecOpsRelease::create($data);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_RELEASE,
            action: 'created',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $release->id,
            resourceType: DevSecOpsRelease::class,
            resourceName: "{$release->name} v{$release->version}",
            metadata: ['type' => $release->type, 'status' => $release->status],
            message: "Release {$release->version} created",
        );

        return $release;
    }

    public function update(string $id, ReleaseDTO $dto): ?DevSecOpsRelease
    {
        $release = $this->getById($id);

        if (!$release) {
            return null;
        }

        $oldValues = $release->toArray();
        $release->update($dto->toArray());

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_RELEASE,
            action: 'updated',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $release->id,
            resourceType: DevSecOpsRelease::class,
            resourceName: "{$release->name} v{$release->version}",
            changes: [
                'old' => $oldValues,
                'new' => $release->toArray(),
            ],
            message: "Release {$release->version} updated",
        );

        return $release->fresh();
    }

    public function publish(string $id): ?DevSecOpsRelease
    {
        $release = $this->getById($id);

        if (!$release || $release->isPublished()) {
            return null;
        }

        $release->update([
            'status' => 'stable',
            'is_draft' => false,
            'is_prerelease' => false,
            'released_at' => now(),
            'released_by' => auth()->id(),
        ]);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_RELEASE,
            action: 'published',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $release->id,
            resourceType: DevSecOpsRelease::class,
            resourceName: "{$release->name} v{$release->version}",
            message: "Release {$release->version} published",
        );

        return $release->fresh();
    }

    public function deprecate(string $id, ?string $reason = null): ?DevSecOpsRelease
    {
        $release = $this->getById($id);

        if (!$release) {
            return null;
        }

        $release->update(['status' => 'deprecated']);

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_RELEASE,
            action: 'deprecated',
            status: DevSecOpsActivityLog::STATUS_WARNING,
            resourceId: $release->id,
            resourceType: DevSecOpsRelease::class,
            resourceName: "{$release->name} v{$release->version}",
            message: "Release {$release->version} deprecated" . ($reason ? ": {$reason}" : ''),
        );

        return $release->fresh();
    }

    public function delete(string $id): bool
    {
        $release = $this->getById($id);

        if (!$release) {
            return false;
        }

        $this->logActivity(
            type: DevSecOpsActivityLog::TYPE_RELEASE,
            action: 'deleted',
            status: DevSecOpsActivityLog::STATUS_SUCCESS,
            resourceId: $release->id,
            resourceType: DevSecOpsRelease::class,
            resourceName: "{$release->name} v{$release->version}",
            message: "Release {$release->version} deleted",
        );

        return $release->delete();
    }

    public function bumpVersion(string $currentVersion, string $type): string
    {
        $parts = explode('.', $currentVersion);
        $major = (int) ($parts[0] ?? 1);
        $minor = (int) ($parts[1] ?? 0);
        $patch = (int) ($parts[2] ?? 0);

        switch ($type) {
            case 'major':
                $major++;
                $minor = 0;
                $patch = 0;
                break;
            case 'minor':
                $minor++;
                $patch = 0;
                break;
            case 'patch':
                $patch++;
                break;
        }

        return "{$major}.{$minor}.{$patch}";
    }

    public function getLatestReleased(): ?DevSecOpsRelease
    {
        return DevSecOpsRelease::where('status', '!=', 'draft')
            ->orderBy('released_at', 'desc')
            ->first();
    }
}
