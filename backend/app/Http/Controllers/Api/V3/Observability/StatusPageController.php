<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Observability;

use App\Http\Controllers\Controller;
use App\Services\Observability\StatusPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatusPageController extends Controller
{
    public function __construct(
        protected StatusPageService $statusPageService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['is_active']);
        $perPage = (int) $request->input('per_page', 15);

        $pages = $this->statusPageService->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $pages->items(),
            'meta' => [
                'current_page' => $pages->currentPage(),
                'last_page' => $pages->lastPage(),
                'per_page' => $pages->perPage(),
                'total' => $pages->total(),
            ],
        ]);
    }

    public function active(): JsonResponse
    {
        $pages = $this->statusPageService->getActivePages();

        return response()->json([
            'success' => true,
            'data' => $pages,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $page = $this->statusPageService->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $page,
        ]);
    }

    public function public(string $slug): JsonResponse
    {
        $page = $this->statusPageService->getPublicStatusPage($slug);

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Status page not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $page,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'timezone' => 'nullable|string',
            'status' => 'nullable|string',
            'header_settings' => 'nullable|array',
            'footer_settings' => 'nullable|array',
            'custom_css' => 'nullable|array',
            'show_incident_history' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $page = $this->statusPageService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Status page created successfully',
            'data' => $page,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'timezone' => 'nullable|string',
            'status' => 'nullable|string',
            'header_settings' => 'nullable|array',
            'footer_settings' => 'nullable|array',
            'custom_css' => 'nullable|array',
            'show_incident_history' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $page = $this->statusPageService->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Status page updated successfully',
            'data' => $page,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->statusPageService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Status page deleted successfully',
        ]);
    }

    public function addComponent(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'service_id' => 'nullable|uuid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|integer',
            'status' => 'nullable|string',
            'show_history' => 'nullable|boolean',
        ]);

        $component = $this->statusPageService->addComponent($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Component added successfully',
            'data' => $component,
        ], 201);
    }

    public function updateComponent(Request $request, string $componentId): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'position' => 'nullable|integer',
            'status' => 'nullable|string',
            'show_history' => 'nullable|boolean',
        ]);

        $component = $this->statusPageService->updateComponent($componentId, $data);

        return response()->json([
            'success' => true,
            'message' => 'Component updated successfully',
            'data' => $component,
        ]);
    }

    public function updateComponentStatus(Request $request, string $componentId): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|string',
        ]);

        $component = $this->statusPageService->updateComponentStatus($componentId, $data['status']);

        return response()->json([
            'success' => true,
            'message' => 'Component status updated successfully',
            'data' => $component,
        ]);
    }

    public function deleteComponent(string $componentId): JsonResponse
    {
        $this->statusPageService->deleteComponent($componentId);

        return response()->json([
            'success' => true,
            'message' => 'Component deleted successfully',
        ]);
    }

    public function addIncident(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'incident_id' => 'required|uuid',
            'is_visible' => 'nullable|boolean',
        ]);

        $this->statusPageService->addIncident(
            $id,
            $data['incident_id'],
            $data['is_visible'] ?? true
        );

        return response()->json([
            'success' => true,
            'message' => 'Incident added to status page successfully',
        ]);
    }

    public function removeIncident(string $id, string $incidentId): JsonResponse
    {
        $result = $this->statusPageService->removeIncident($id, $incidentId);

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found on status page',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Incident removed from status page successfully',
        ]);
    }

    public function refresh(string $id): JsonResponse
    {
        $page = $this->statusPageService->refreshStatus($id);

        return response()->json([
            'success' => true,
            'message' => 'Status page refreshed successfully',
            'data' => $page,
        ]);
    }
}
