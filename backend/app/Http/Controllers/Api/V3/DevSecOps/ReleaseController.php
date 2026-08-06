<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\DTO\DevSecOps\ReleaseDTO;
use App\Http\Requests\DevSecOps\ReleaseRequest;
use App\Services\DevSecOps\ReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReleaseController extends BaseController
{
    public function __construct(
        protected ReleaseService $releaseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'type', 'channel', 'is_draft']);
        $perPage = (int) $request->get('per_page', 15);
        
        $releases = $this->releaseService->getAll($filters, $perPage);
        
        return $this->paginated($releases, 'Releases retrieved successfully');
    }

    public function published(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 15);
        $releases = $this->releaseService->getPublished($perPage);
        
        return $this->paginated($releases, 'Published releases retrieved successfully');
    }

    public function lts(): JsonResponse
    {
        $releases = $this->releaseService->getLts();
        
        return $this->success($releases, 'LTS releases retrieved successfully');
    }

    public function latest(): JsonResponse
    {
        $release = $this->releaseService->getLatestReleased();
        
        if (!$release) {
            return $this->error('No releases found', 404);
        }
        
        return $this->success($release, 'Latest release retrieved successfully');
    }

    public function show(string $id): JsonResponse
    {
        $release = $this->releaseService->getById($id);
        
        if (!$release) {
            return $this->error('Release not found', 404);
        }
        
        return $this->success($release, 'Release retrieved successfully');
    }

    public function store(ReleaseRequest $request): JsonResponse
    {
        $dto = ReleaseDTO::fromArray($request->validated());
        $release = $this->releaseService->create($dto);
        
        return $this->created($release, 'Release created successfully');
    }

    public function update(ReleaseRequest $request, string $id): JsonResponse
    {
        $dto = ReleaseDTO::fromArray($request->validated());
        $release = $this->releaseService->update($id, $dto);
        
        if (!$release) {
            return $this->error('Release not found', 404);
        }
        
        return $this->success($release, 'Release updated successfully');
    }

    public function publish(string $id): JsonResponse
    {
        $release = $this->releaseService->publish($id);
        
        if (!$release) {
            return $this->error('Failed to publish release', 400);
        }
        
        return $this->success($release, 'Release published successfully');
    }

    public function deprecate(Request $request, string $id): JsonResponse
    {
        $reason = $request->get('reason');
        $release = $this->releaseService->deprecate($id, $reason);
        
        if (!$release) {
            return $this->error('Release not found', 404);
        }
        
        return $this->success($release, 'Release deprecated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->releaseService->delete($id);
        
        if (!$deleted) {
            return $this->error('Release not found', 404);
        }
        
        return $this->success(null, 'Release deleted successfully');
    }

    public function bumpVersion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_version' => 'required|string',
            'type' => 'required|string|in:major,minor,patch',
        ]);

        $newVersion = $this->releaseService->bumpVersion(
            $validated['current_version'],
            $validated['type']
        );
        
        return $this->success(['version' => $newVersion], 'Version bumped successfully');
    }
}
