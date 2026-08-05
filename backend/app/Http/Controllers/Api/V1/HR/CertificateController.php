<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\Controller;
use App\Services\HR\CertificateService;
use App\Models\HR\ExperienceCertificate;
use App\Models\HR\NocCertificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(
        private readonly CertificateService $certificateService
    ) {}

    // ===================== EXPERIENCE CERTIFICATES =====================

    public function getExperienceCertificates(Request $request): JsonResponse
    {
        $filters = $request->only(['employee_id', 'is_verified']);
        $perPage = (int) $request->get('per_page', 20);

        $certificates = $this->certificateService->getExperienceCertificates($perPage, $filters);

        return response()->json([
            'data' => $certificates->items(),
            'meta' => [
                'current_page' => $certificates->currentPage(),
                'last_page' => $certificates->lastPage(),
                'per_page' => $certificates->perPage(),
                'total' => $certificates->total(),
            ],
        ]);
    }

    public function createExperienceCertificate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'end_date' => 'nullable|date',
            'issue_date' => 'nullable|date',
            'experience_summary' => 'nullable|string',
            'performance_remarks' => 'nullable|string',
            'reason_for_leaving' => 'nullable|string',
            'issued_by' => 'nullable|exists:users,id',
            'authorized_by' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string',
        ]);

        $certificate = $this->certificateService->createExperienceCertificate($validated);
        return response()->json(['data' => $certificate], 201);
    }

    public function generateExperiencePdf(string $uuid): JsonResponse
    {
        $path = $this->certificateService->generateExperiencePdf($uuid);
        return response()->json(['data' => ['path' => $path]]);
    }

    public function verifyExperienceCertificate(string $code): JsonResponse
    {
        $certificate = $this->certificateService->verifyExperienceCertificate($code);

        if (!$certificate) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }

        return response()->json([
            'data' => [
                'verified' => true,
                'certificate' => $certificate,
            ],
        ]);
    }

    // ===================== NOC CERTIFICATES =====================

    public function getNocCertificates(Request $request): JsonResponse
    {
        $filters = $request->only(['employee_id', 'noc_type', 'is_verified']);
        $perPage = (int) $request->get('per_page', 20);

        $certificates = $this->certificateService->getNocCertificates($perPage, $filters);

        return response()->json([
            'data' => $certificates->items(),
            'meta' => [
                'current_page' => $certificates->currentPage(),
                'last_page' => $certificates->lastPage(),
                'per_page' => $certificates->perPage(),
                'total' => $certificates->total(),
            ],
        ]);
    }

    public function createNocCertificate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'noc_type' => 'required|in:general,visa,immigration,employment,government',
            'issue_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'content' => 'nullable|string',
            'issued_by' => 'nullable|exists:users,id',
            'authorized_by' => 'nullable|exists:users,id',
            'remarks' => 'nullable|string',
        ]);

        $certificate = $this->certificateService->createNocCertificate($validated);
        return response()->json(['data' => $certificate], 201);
    }

    public function generateNocPdf(string $uuid): JsonResponse
    {
        $path = $this->certificateService->generateNocPdf($uuid);
        return response()->json(['data' => ['path' => $path]]);
    }

    public function verifyNocCertificate(string $code): JsonResponse
    {
        $certificate = $this->certificateService->verifyNocCertificate($code);

        if (!$certificate) {
            return response()->json(['error' => 'Certificate not found'], 404);
        }

        return response()->json([
            'data' => [
                'verified' => true,
                'certificate' => $certificate,
            ],
        ]);
    }

    // ===================== PUBLIC VERIFICATION =====================

    public function publicVerify(Request $request, string $type, string $code): JsonResponse
    {
        if ($type === 'experience') {
            $certificate = ExperienceCertificate::where('verification_code', $code)->first();
            if ($certificate) {
                $certificate->markVerified();
            }
        } elseif ($type === 'noc') {
            $certificate = NocCertificate::where('verification_code', $code)->first();
            if ($certificate) {
                $certificate->markVerified();
            }
        } else {
            return response()->json(['error' => 'Invalid certificate type'], 400);
        }

        if (!$certificate) {
            return response()->json([
                'verified' => false,
                'message' => 'Certificate not found',
            ], 404);
        }

        return response()->json([
            'verified' => true,
            'certificate' => [
                'type' => $type,
                'certificate_no' => $certificate->certificate_no,
                'issue_date' => $certificate->issue_date->format('Y-m-d'),
                'is_verified' => $certificate->is_verified,
                'employee' => $certificate->employee?->profile?->full_name,
            ],
        ]);
    }
}
