<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Observability;

use App\Http\Controllers\Controller;
use App\Services\Observability\MetricService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    public function __construct(
        protected MetricService $metricService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'service_id', 'name', 'type', 'environment',
            'start_date', 'end_date'
        ]);
        $perPage = (int) $request->input('per_page', 100);

        $metrics = $this->metricService->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $metrics->items(),
            'meta' => [
                'current_page' => $metrics->currentPage(),
                'last_page' => $metrics->lastPage(),
                'per_page' => $metrics->perPage(),
                'total' => $metrics->total(),
            ],
        ]);
    }

    public function latest(Request $request, string $serviceId): JsonResponse
    {
        $metricName = $request->input('name');
        $limit = (int) $request->input('limit', 100);

        $metrics = $this->metricService->getLatest($serviceId, $metricName, $limit);

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    public function timeSeries(Request $request, string $serviceId): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'interval' => 'nullable|integer|min:1',
        ]);

        $data = $this->metricService->getTimeSeries(
            $serviceId,
            $request->input('name'),
            $request->input('start_date'),
            $request->input('end_date'),
            (int) $request->input('interval', 60)
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function aggregates(Request $request, string $serviceId): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data = $this->metricService->getAggregates(
            $serviceId,
            $request->input('name'),
            $request->input('start_date'),
            $request->input('end_date')
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_id' => 'required|uuid',
            'name' => 'required|string',
            'type' => 'required|string',
            'value' => 'required|numeric',
            'unit' => 'nullable|string',
            'labels' => 'nullable|array',
            'tags' => 'nullable|array',
            'environment' => 'nullable|string',
            'timestamp' => 'nullable|date',
        ]);

        $metric = $this->metricService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Metric recorded successfully',
            'data' => $metric,
        ], 201);
    }

    public function cleanup(Request $request): JsonResponse
    {
        $daysToKeep = (int) $request->input('days', 90);

        $deleted = $this->metricService->deleteOldMetrics($daysToKeep);

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} old metrics",
            'data' => ['deleted_count' => $deleted],
        ]);
    }
}
