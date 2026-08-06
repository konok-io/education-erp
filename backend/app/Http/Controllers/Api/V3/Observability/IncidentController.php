<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Observability;

use App\DTO\Observability\IncidentDTO;
use App\Http\Controllers\Controller;
use App\Services\Observability\IncidentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function __construct(
        protected IncidentService $incidentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'service_id', 'severity', 'status', 'environment',
            'start_date', 'end_date'
        ]);
        $perPage = (int) $request->input('per_page', 15);

        $incidents = $this->incidentService->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => IncidentDTO::fromCollection($incidents->items()),
            'meta' => [
                'current_page' => $incidents->currentPage(),
                'last_page' => $incidents->lastPage(),
                'per_page' => $incidents->perPage(),
                'total' => $incidents->total(),
            ],
        ]);
    }

    public function active(Request $request): JsonResponse
    {
        $environment = $request->input('environment');

        $filters = [];
        if ($environment) {
            $filters['environment'] = $environment;
        }

        $incidents = $this->incidentService->getActiveIncidents();

        return response()->json([
            'success' => true,
            'data' => IncidentDTO::fromCollection($incidents->toArray()),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $incident = $this->incidentService->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function showByNumber(string $incidentNumber): JsonResponse
    {
        $incident = $this->incidentService->findByNumber($incidentNumber);

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'required|string',
            'service_id' => 'nullable|uuid',
            'alert_id' => 'nullable|uuid',
            'environment' => 'nullable|string',
            'affected_components' => 'nullable|array',
            'impact' => 'nullable|array',
            'started_at' => 'nullable|date',
            'created_by_user_id' => 'nullable|uuid',
        ]);

        $incident = $this->incidentService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Incident created successfully',
            'data' => IncidentDTO::fromModel($incident),
        ], 201);
    }

    public function createFromAlert(Request $request, string $alertId): JsonResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'user_id' => 'nullable|uuid',
            'description' => 'nullable|string',
        ]);

        $incident = $this->incidentService->createFromAlert(
            $alertId,
            $data['title'] ?? 'Incident from alert',
            $data['user_id'] ?? null,
            $data['description'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Incident created from alert successfully',
            'data' => IncidentDTO::fromModel($incident),
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'severity' => 'nullable|string',
            'service_id' => 'nullable|uuid',
            'environment' => 'nullable|string',
            'affected_components' => 'nullable|array',
            'impact' => 'nullable|array',
            'metadata' => 'nullable|array',
        ]);

        $incident = $this->incidentService->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Incident updated successfully',
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function acknowledge(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|uuid',
        ]);

        $incident = $this->incidentService->acknowledge($id, $data['user_id']);

        return response()->json([
            'success' => true,
            'message' => 'Incident acknowledged successfully',
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function assign(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|uuid',
            'assigned_by_user_id' => 'required|uuid',
        ]);

        $incident = $this->incidentService->assign(
            $id,
            $data['user_id'],
            $data['assigned_by_user_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Incident assigned successfully',
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function resolve(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|uuid',
        ]);

        $incident = $this->incidentService->resolve($id, $data['user_id']);

        return response()->json([
            'success' => true,
            'message' => 'Incident resolved successfully',
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function close(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|uuid',
        ]);

        $incident = $this->incidentService->close($id, $data['user_id']);

        return response()->json([
            'success' => true,
            'message' => 'Incident closed successfully',
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function addTimelineEvent(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'event_type' => 'required|string',
            'title' => 'required|string|max:255',
            'user_id' => 'nullable|uuid',
            'description' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $incident = $this->incidentService->addTimelineEvent(
            $id,
            $data['event_type'],
            $data['title'],
            $data['user_id'] ?? null,
            $data['description'] ?? null,
            $data['metadata'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Timeline event added successfully',
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function addPostmortem(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'postmortem' => 'required|string',
            'user_id' => 'required|uuid',
        ]);

        $incident = $this->incidentService->addPostmortem(
            $id,
            $data['postmortem'],
            $data['user_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Postmortem added successfully',
            'data' => IncidentDTO::fromModel($incident),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->incidentService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Incident deleted successfully',
        ]);
    }

    public function summary(): JsonResponse
    {
        $summary = $this->incidentService->getSummary();

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
