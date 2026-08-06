<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Library;

use App\Http\Controllers\Controller;
use App\Services\Library\IssueService;
use App\Models\Library\LibraryIssue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    public function __construct(
        private readonly IssueService $issueService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'member_id', 'overdue']);
        $perPage = (int) $request->get('per_page', 20);

        $issues = $this->issueService->getIssues($perPage, $filters);

        return response()->json([
            'data' => $issues->items(),
            'meta' => [
                'current_page' => $issues->currentPage(),
                'last_page' => $issues->lastPage(),
                'per_page' => $issues->perPage(),
                'total' => $issues->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:library_members,id',
            'book_copy_uuid' => 'required|exists:library_book_copies,uuid',
            'notes' => 'nullable|string',
        ]);

        try {
            $validated['issued_by'] = auth()->id();
            $issue = $this->issueService->issueBook($validated);
            return response()->json(['data' => $issue], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(string $uuid): JsonResponse
    {
        $issue = LibraryIssue::with(['member', 'bookCopy.book', 'issuer', 'returner', 'fine'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $issue]);
    }

    public function return(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'return_condition' => 'nullable|in:excellent,good,fair,poor,damaged',
            'notes' => 'nullable|string',
        ]);

        try {
            $issue = $this->issueService->returnBook($uuid, $validated);
            return response()->json(['data' => $issue]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function renew(string $uuid): JsonResponse
    {
        try {
            $issue = $this->issueService->renewBook($uuid);
            return response()->json(['data' => $issue]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function reserve(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:library_members,id',
            'book_uuid' => 'required|exists:library_books,uuid',
            'notes' => 'nullable|string',
        ]);

        try {
            $reservation = $this->issueService->reserveBook($validated);
            return response()->json(['data' => $reservation], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function getTodayStats(): JsonResponse
    {
        $stats = $this->issueService->getTodayStats();
        return response()->json(['data' => $stats]);
    }
}
