<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V3\Backup;

use App\DTO\Backup\BackupJobDTO;
use App\DTO\Backup\BackupSnapshotDTO;
use App\Http\Controllers\Controller;
use App\Services\Backup\BackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct(
        protected BackupService $backupService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status', 'type', 'environment', 'source_type',
            'is_immutable', 'verified'
        ]);
        $perPage = (int) $request->input('per_page', 15);

        $backups = $this->backupService->index($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => BackupJobDTO::fromCollection($backups->items()),
            'meta' => [
                'current_page' => $backups->currentPage(),
                'last_page' => $backups->lastPage(),
                'per_page' => $backups->perPage(),
                'total' => $backups->total(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $backup = $this->backupService->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => BackupJobDTO::fromModel($backup),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:full,incremental,differential,snapshot',
            'source_type' => 'required|string|in:database,files,media,configuration,logs,all',
            'source_config' => 'nullable|array',
            'destination_type' => 'required|string',
            'destination_config' => 'nullable|array',
            'encryption' => 'nullable|string',
            'encryption_key_id' => 'nullable|uuid',
            'compression_algorithm' => 'nullable|string',
            'compression_level' => 'nullable|integer',
            'retention_policy' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
            'is_immutable' => 'nullable|boolean',
            'environment' => 'nullable|string',
            'region' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $backup = $this->backupService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Backup job created successfully',
            'data' => BackupJobDTO::fromModel($backup),
        ], 201);
    }

    public function start(string $id): JsonResponse
    {
        $backup = $this->backupService->start($id);

        return response()->json([
            'success' => true,
            'message' => 'Backup job started successfully',
            'data' => BackupJobDTO::fromModel($backup),
        ]);
    }

    public function complete(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'checksum' => 'nullable|string',
            'size_bytes' => 'nullable|integer',
            'file_count' => 'nullable|integer',
        ]);

        $backup = $this->backupService->complete(
            $id,
            $data['checksum'] ?? null,
            $data['size_bytes'] ?? 0,
            $data['file_count'] ?? 0
        );

        return response()->json([
            'success' => true,
            'message' => 'Backup job completed successfully',
            'data' => BackupJobDTO::fromModel($backup),
        ]);
    }

    public function fail(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'error_message' => 'required|string',
        ]);

        $backup = $this->backupService->fail($id, $data['error_message']);

        return response()->json([
            'success' => true,
            'message' => 'Backup job marked as failed',
            'data' => BackupJobDTO::fromModel($backup),
        ]);
    }

    public function cancel(string $id): JsonResponse
    {
        $backup = $this->backupService->cancel($id);

        return response()->json([
            'success' => true,
            'message' => 'Backup job cancelled successfully',
            'data' => BackupJobDTO::fromModel($backup),
        ]);
    }

    public function markVerified(string $id): JsonResponse
    {
        $backup = $this->backupService->markVerified($id);

        return response()->json([
            'success' => true,
            'message' => 'Backup job marked as verified',
            'data' => BackupJobDTO::fromModel($backup),
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string',
            'source_type' => 'nullable|string',
            'source_config' => 'nullable|array',
            'destination_type' => 'nullable|string',
            'destination_config' => 'nullable|array',
            'environment' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $backup = $this->backupService->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Backup job updated successfully',
            'data' => BackupJobDTO::fromModel($backup),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->backupService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Backup job deleted successfully',
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $environment = $request->input('environment', 'production');
        $summary = $this->backupService->getSummary($environment);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function snapshots(Request $request, string $id): JsonResponse
    {
        $filters = $request->only(['status', 'type', 'storage_provider']);
        $filters['backup_job_id'] = $id;
        $perPage = (int) $request->input('per_page', 15);

        $snapshots = $this->backupService->getSnapshots($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => BackupSnapshotDTO::fromCollection($snapshots->items()),
            'meta' => [
                'current_page' => $snapshots->currentPage(),
                'last_page' => $snapshots->lastPage(),
                'per_page' => $snapshots->perPage(),
                'total' => $snapshots->total(),
            ],
        ]);
    }
}
