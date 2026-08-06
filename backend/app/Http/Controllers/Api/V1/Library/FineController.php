<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Library;

use App\Http\Controllers\Controller;
use App\Services\Library\FineService;
use App\Models\Library\LibraryFine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function __construct(
        private readonly FineService $fineService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'fine_type', 'member_id']);
        $perPage = (int) $request->get('per_page', 20);

        $fines = $this->fineService->getFines($perPage, $filters);

        return response()->json([
            'data' => $fines->items(),
            'meta' => [
                'current_page' => $fines->currentPage(),
                'last_page' => $fines->lastPage(),
                'per_page' => $fines->perPage(),
                'total' => $fines->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:library_members,id',
            'fine_type' => 'required|in:late_return,lost_book,damaged_book,membership_violation',
            'reason' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'issue_id' => 'nullable|exists:library_issues,id',
            'notes' => 'nullable|string',
        ]);

        $fine = $this->fineService->createFine($validated);
        return response()->json(['data' => $fine], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $fine = LibraryFine::with(['member', 'issue', 'collector'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $fine]);
    }

    public function collect(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $fine = $this->fineService->collectFine($uuid, (float) $validated['amount']);
            return response()->json(['data' => $fine]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function waive(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $fine = $this->fineService->waiveFine($uuid, (float) $validated['amount']);
            return response()->json(['data' => $fine]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->fineService->getFineStats();
        return response()->json(['data' => $stats]);
    }
}
