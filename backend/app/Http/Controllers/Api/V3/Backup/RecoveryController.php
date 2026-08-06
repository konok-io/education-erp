<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Backup;

use App\DTO\Backup\RecoveryJobDTO;
use App\Http\Controllers\Controller;
use App\Services\Backup\RecoveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecoveryController extends Controller
{
    public function __construct(
        protected RecoveryService $recoveryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status', 'type', 'environment', 'backup_snapshot_id'
        ]);
        $perPage = (int) $request->input('per_page', 15);

        $recoveries = $this->recoveryService->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => RecoveryJobDTO::fromCollection($recoveries->items()),
            'meta' => [
                'current_page' => $recoveries->currentPage(),
                'last_page' => $recoveries->lastPage(),
                'per_page' => $recoveries->perPage(),
                'total' => $recoveries->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $recovery = $this->recoveryService->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => RecoveryJobDTO::fromModel($recovery),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'backup_snapshot_id' => 'required|uuid',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:full,partial,file,database,point_in_time,table',
            'destination_type' => 'nullable|string',
            'destination_config' => 'nullable|array',
            'point_in_time' => 'nullable|date',
            'restore_options' => 'nullable|array',
            'target_database' => 'nullable|string',
            'target_path' => 'nullable|string',
            'environment' => 'nullable|string',
        ]);

        $recovery = $this->recoveryService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Recovery job created successfully',
            'data' => RecoveryJobDTO::fromModel($recovery),
        ], 201);
    }

    public function createFromSnapshot(Request $request): JsonResponse
    {
        $data = $request->validate([
            'snapshot_id' => 'required|uuid',
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:full,partial,file,database,point_in_time,table',
            'destination_type' => 'nullable|string',
            'destination_config' => 'nullable|array',
            'point_in_time' => 'nullable|date',
            'restore_options' => 'nullable|array',
            'target_database' => 'nullable|string',
            'target_path' => 'nullable|string',
        ]);

        $recovery = $this->recoveryService->createFromSnapshot(
            $data['snapshot_id'],
            $data['name'],
            $data['type'],
            [
                'destination_type' => $data['destination_type'] ?? 'original',
                'destination_config' => $data['destination_config'] ?? null,
                'point_in_time' => $data['point_in_time'] ?? null,
                'restore_options' => $data['restore_options'] ?? null,
                'target_database' => $data['target_database'] ?? null,
                'target_path' => $data['target_path'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Recovery job created from snapshot successfully',
            'data' => RecoveryJobDTO::fromModel($recovery),
        ], 201);
    }

    public function start(string $id): JsonResponse
    {
        $recovery = $this->recoveryService->start($id);

        return response()->json([
            'success' => true,
            'message' => 'Recovery job started successfully',
            'data' => RecoveryJobDTO::fromModel($recovery),
        ]);
    }

    public function complete(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'size_restored' => 'nullable|integer',
            'files_restored' => 'nullable|integer',
            'records_restored' => 'nullable|integer',
        ]);

        $recovery = $this->recoveryService->complete(
            $id,
            $data['size_restored'] ?? 0,
            $data['files_restored'] ?? 0,
            $data['records_restored'] ?? 0
        );

        return response()->json([
            'success' => true,
            'message' => 'Recovery job completed successfully',
            'data' => RecoveryJobDTO::fromModel($recovery),
        ]);
    }

    public function fail(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'error_message' => 'required|string',
        ]);

        $recovery = $this->recoveryService->fail($id, $data['error_message']);

        return response()->json([
            'success' => true,
            'message' => 'Recovery job marked as failed',
            'data' => RecoveryJobDTO::fromModel($recovery),
        ]);
    }

    public function verify(string $id): JsonResponse
    {
        $recovery = $this->recoveryService->verify($id);

        return response()->json([
            'success' => true,
            'message' => 'Recovery job verified successfully',
            'data' => RecoveryJobDTO::fromModel($recovery),
        ]);
    }

    public function cancel(string $id): JsonResponse
    {
        $recovery = $this->recoveryService->cancel($id);

        return response()->json([
            'success' => true,
            'message' => 'Recovery job cancelled successfully',
            'data' => RecoveryJobDTO::fromModel($recovery),
        ]);
    }

    public function addLog(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'message' => 'required|string',
            'level' => 'nullable|string|in:info,warning,error,debug',
        ]);

        $recovery = $this->recoveryService->addLog(
            $id,
            $data['message'],
            $data['level'] ?? 'info'
        );

        return response()->json([
            'success' => true,
            'message' => 'Log entry added successfully',
            'data' => RecoveryJobDTO::fromModel($recovery),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->recoveryService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Recovery job deleted successfully',
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');
        $summary = $this->recoveryService->getSummary($environment);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }
}
