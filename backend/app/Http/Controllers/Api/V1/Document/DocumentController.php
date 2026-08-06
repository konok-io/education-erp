<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Document;

use App\Http\Controllers\Controller;
use App\Services\Document\DocumentService;
use App\Models\Document\DocumentVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService
    ) {}

    // ===================== DASHBOARD =====================

    public function getDashboard(): JsonResponse
    {
        $stats = $this->documentService->getDashboardStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== VERIFICATIONS =====================

    public function getVerifications(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'document_type', 'verification_type', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $verifications = $this->documentService->getVerifications($perPage, $filters);

        return response()->json([
            'data' => $verifications->items(),
            'meta' => [
                'current_page' => $verifications->currentPage(),
                'last_page' => $verifications->lastPage(),
                'total' => $verifications->total(),
            ],
        ]);
    }

    public function createVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'certificate_id' => 'nullable|exists:certificates,id',
            'student_id' => 'nullable|exists:students,id',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'nullable|email',
            'applicant_phone' => 'nullable|string|max:20',
            'document_type' => 'required|in:certificate,transcript,marksheet,nid,passport,other',
            'document_name' => 'nullable|string',
            'document_number' => 'nullable|string|max:100',
            'document_path' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'verification_type' => 'nullable|in:self,third_party,employer,institution',
            'verifier_name' => 'nullable|string|max:150',
            'verifier_email' => 'nullable|email',
            'verifier_organization' => 'nullable|string|max:200',
        ]);

        $verification = $this->documentService->createVerification($validated);
        return response()->json(['data' => $verification], 201);
    }

    public function showVerification(string $uuid): JsonResponse
    {
        $verification = DocumentVerification::with(['certificate', 'student', 'verifier'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $verification]);
    }

    public function verifyDocument(string $uuid): JsonResponse
    {
        $verification = $this->documentService->verifyDocument($uuid, auth()->id());
        return response()->json(['data' => $verification]);
    }

    public function rejectVerification(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string',
        ]);

        $verification = $this->documentService->rejectVerification(
            $uuid,
            auth()->id(),
            $validated['reason'] ?? null
        );
        return response()->json(['data' => $verification]);
    }

    // ===================== PUBLIC VERIFICATION =====================

    public function publicVerify(string $code): JsonResponse
    {
        $result = $this->documentService->getPublicVerification($code);
        return response()->json($result);
    }
}
