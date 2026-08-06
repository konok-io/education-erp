<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Observability;

use App\Http\Controllers\Controller;
use App\Services\Observability\ObservabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        protected ObservabilityService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'environment', 'status', 'is_active', 'search']);
        $perPage = (int) $request->input('per_page', 15);

        $services = $this->service->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $services->items(),
            'meta' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
            ],
        ]);
    }

    public function active(Request $request): JsonResponse
    {
        $environment = $request->input('environment');

        if ($environment) {
            $services = $this->service->getByEnvironment($environment);
        } else {
            $services = $this->service->getAllActive();
        }

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $service = $this->service->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $service,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'environment' => 'required|string',
            'status' => 'nullable|string',
            'metadata' => 'nullable|array',
            'tags' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $service = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'environment' => 'nullable|string',
            'status' => 'nullable|string',
            'metadata' => 'nullable|array',
            'tags' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $service = $this->service->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service,
        ]);
    }

    public function toggle(string $id): JsonResponse
    {
        $service = $this->service->toggleActive($id);

        return response()->json([
            'success' => true,
            'message' => 'Service toggled successfully',
            'data' => $service,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }

    public function health(): JsonResponse
    {
        $summary = $this->service->getHealthSummary();

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
