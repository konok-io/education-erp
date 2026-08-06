<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\DevSecOps;

use App\DTO\DevSecOps\ArtifactDTO;
use App\Http\Requests\DevSecOps\ArtifactRequest;
use App\Services\DevSecOps\ArtifactService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtifactController extends BaseController
{
    public function __construct(
        protected ArtifactService $artifactService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'registry', 'scan_status', 'name']);
        $perPage = (int) $request->get('per_page', 15);
        
        $artifacts = $this->artifactService->getAll($filters, $perPage);
        
        return $this->paginated($artifacts, 'Artifacts retrieved successfully');
    }

    public function show(string $id): JsonResponse
    {
        $artifact = $this->artifactService->getById($id);
        
        if (!$artifact) {
            return $this->error('Artifact not found', 404);
        }
        
        return $this->success($artifact, 'Artifact retrieved successfully');
    }

    public function store(ArtifactRequest $request): JsonResponse
    {
        $dto = ArtifactDTO::fromArray($request->validated());
        $artifact = $this->artifactService->create($dto);
        
        return $this->created($artifact, 'Artifact created successfully');
    }

    public function updateScanResults(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'scan_results' => 'required|array',
            'vulnerabilities' => 'required|array',
        ]);

        $artifact = $this->artifactService->updateScanResults(
            $id,
            $validated['scan_results'],
            $validated['vulnerabilities']
        );
        
        if (!$artifact) {
            return $this->error('Artifact not found', 404);
        }
        
        return $this->success($artifact, 'Scan results updated successfully');
    }

    public function updateSbom(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'sbom' => 'required|array',
        ]);

        $artifact = $this->artifactService->updateSbom($id, $validated['sbom']);
        
        if (!$artifact) {
            return $this->error('Artifact not found', 404);
        }
        
        return $this->success($artifact, 'SBOM updated successfully');
    }

    public function updateProvenance(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'provenance' => 'required|array',
        ]);

        $artifact = $this->artifactService->updateProvenance($id, $validated['provenance']);
        
        if (!$artifact) {
            return $this->error('Artifact not found', 404);
        }
        
        return $this->success($artifact, 'Provenance updated successfully');
    }

    public function sign(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'signature' => 'required|string',
        ]);

        $artifact = $this->artifactService->sign($id, $validated['signature']);
        
        if (!$artifact) {
            return $this->error('Artifact not found', 404);
        }
        
        return $this->success($artifact, 'Artifact signed successfully');
    }

    public function latestByType(Request $request): JsonResponse
    {
        $type = $request->get('type');
        $limit = (int) $request->get('limit', 10);
        
        if (!$type) {
            return $this->error('Type is required', 400);
        }
        
        $artifacts = $this->artifactService->getLatestByType($type, $limit);
        
        return $this->success($artifacts, 'Artifacts retrieved successfully');
    }

    public function vulnerable(): JsonResponse
    {
        $artifacts = $this->artifactService->getVulnerable();
        
        return $this->success($artifacts, 'Vulnerable artifacts retrieved successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->artifactService->delete($id);
        
        if (!$deleted) {
            return $this->error('Artifact not found', 404);
        }
        
        return $this->success(null, 'Artifact deleted successfully');
    }
}
