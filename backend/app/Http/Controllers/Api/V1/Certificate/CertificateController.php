<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Certificate;

use App\Http\Controllers\Controller;
use App\Services\Certificate\CertificateService;
use App\Models\Certificate\Certificate;
use App\Models\Certificate\CertificateRequest;
use App\Models\Certificate\CertificateTemplate;
use App\Models\Certificate\DigitalSignature;
use App\Models\Certificate\Transcript;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct(
        private readonly CertificateService $certificateService
    ) {}

    // ===================== DASHBOARD =====================

    public function getDashboard(): JsonResponse
    {
        $stats = $this->certificateService->getDashboardStats();
        return response()->json(['data' => $stats]);
    }

    // ===================== REQUESTS =====================

    public function getRequests(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'certificate_type', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $requests = $this->certificateService->getRequests($perPage, $filters);

        return response()->json([
            'data' => $requests->items(),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    public function createRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'student_name' => 'required|string|max:255',
            'student_email' => 'nullable|email',
            'student_phone' => 'nullable|string|max:20',
            'certificate_type' => 'required|in:character,transfer,testimonial,bonafide,experience,course_completion,training,internship,migration,scholarship,achievement,other',
            'purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
            'fee' => 'nullable|numeric|min:0',
        ]);

        $requestData = $this->certificateService->createRequest($validated);
        return response()->json(['data' => $requestData], 201);
    }

    public function showRequest(string $uuid): JsonResponse
    {
        $requestData = CertificateRequest::with(['student', 'certificate', 'reviewer', 'approver'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $requestData]);
    }

    public function approveRequest(string $uuid): JsonResponse
    {
        $requestData = $this->certificateService->approveRequest($uuid, auth()->id());
        return response()->json(['data' => $requestData]);
    }

    public function rejectRequest(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $requestData = $this->certificateService->rejectRequest($uuid, auth()->id(), $validated['reason']);
        return response()->json(['data' => $requestData]);
    }

    // ===================== CERTIFICATES =====================

    public function getCertificates(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'certificate_type', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $query = Certificate::with(['template', 'student']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['certificate_type'])) {
            $query->where('certificate_type', $filters['certificate_type']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('certificate_no', 'like', "%{$filters['search']}%")
                    ->orWhere('student_name', 'like', "%{$filters['search']}%");
            });
        }

        $certificates = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $certificates->items(),
            'meta' => [
                'current_page' => $certificates->currentPage(),
                'last_page' => $certificates->lastPage(),
                'total' => $certificates->total(),
            ],
        ]);
    }

    public function generateCertificate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_id' => 'nullable|exists:certificate_templates,id',
            'certificate_type' => 'required|string',
            'student_id' => 'nullable|exists:students,id',
            'student_name' => 'required|string|max:255',
            'student_name_bn' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:150',
            'mother_name' => 'nullable|string|max:150',
            'roll_number' => 'nullable|string|max:50',
            'registration_no' => 'nullable|string|max:50',
            'class' => 'nullable|string|max:50',
            'section' => 'nullable|string|max:50',
            'group' => 'nullable|string|max:50',
            'passing_year' => 'nullable|integer|min:2000|max:2100',
            'subjects' => 'nullable|array',
            'gpa' => 'nullable|numeric|min:0|max:5',
            'grades' => 'nullable|array',
            'purpose' => 'nullable|string',
            'remarks' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'valid_until' => 'nullable|date',
        ]);

        $certificate = $this->certificateService->generateCertificate($validated);
        return response()->json(['data' => $certificate], 201);
    }

    public function showCertificate(string $uuid): JsonResponse
    {
        $certificate = Certificate::with(['template', 'student', 'issuer'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $certificate]);
    }

    public function approveCertificate(string $uuid): JsonResponse
    {
        $certificate = $this->certificateService->approveCertificate($uuid);
        return response()->json(['data' => $certificate]);
    }

    public function issueCertificate(string $uuid): JsonResponse
    {
        $certificate = $this->certificateService->issueCertificate($uuid, auth()->id());
        return response()->json(['data' => $certificate]);
    }

    public function cancelCertificate(string $uuid): JsonResponse
    {
        $certificate = Certificate::where('uuid', $uuid)->firstOrFail();
        $certificate->update(['status' => 'cancelled']);
        return response()->json(['data' => $certificate->fresh()]);
    }

    // ===================== TEMPLATES =====================

    public function getTemplates(Request $request): JsonResponse
    {
        $templates = CertificateTemplate::when($request->get('type'), function ($q) use ($request) {
            $q->where('certificate_type', $request->get('type'));
        })
        ->when($request->get('status'), function ($q) use ($request) {
            $q->where('status', $request->get('status'));
        })
        ->orderBy('name')
        ->get();

        return response()->json(['data' => $templates]);
    }

    public function createTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'certificate_type' => 'required|string',
            'template_content' => 'required|string',
            'variables' => 'nullable|array',
            'background_image' => 'nullable|string',
            'logo' => 'nullable|string',
            'signature' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $template = CertificateTemplate::create(array_merge($validated, [
            'uuid' => \Illuminate\Support\Str::uuid(),
            'template_code' => CertificateTemplate::generateTemplateCode(),
        ]));

        return response()->json(['data' => $template], 201);
    }

    public function showTemplate(string $uuid): JsonResponse
    {
        $template = CertificateTemplate::where('uuid', $uuid)->firstOrFail();
        return response()->json(['data' => $template]);
    }

    public function updateTemplate(Request $request, string $uuid): JsonResponse
    {
        $template = CertificateTemplate::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'certificate_type' => 'sometimes|string',
            'template_content' => 'sometimes|string',
            'variables' => 'nullable|array',
            'background_image' => 'nullable|string',
            'logo' => 'nullable|string',
            'signature' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $template->update($validated);
        return response()->json(['data' => $template->fresh()]);
    }

    // ===================== TRANSCRIPTS =====================

    public function getTranscripts(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $transcripts = $this->certificateService->getTranscripts($perPage, $filters);

        return response()->json([
            'data' => $transcripts->items(),
            'meta' => [
                'current_page' => $transcripts->currentPage(),
                'last_page' => $transcripts->lastPage(),
                'total' => $transcripts->total(),
            ],
        ]);
    }

    public function createTranscript(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'student_name' => 'required|string|max:255',
            'roll_number' => 'nullable|string|max:50',
            'registration_no' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'program' => 'nullable|string|max:100',
            'admission_year' => 'nullable|integer|min:2000|max:2100',
            'passing_year' => 'nullable|integer|min:2000|max:2100',
            'degree' => 'nullable|string|max:100',
            'total_credits' => 'nullable|numeric|min:0',
            'cgpa' => 'nullable|numeric|min:0|max:5',
            'scale' => 'nullable|numeric|min:0|max:5',
            'result_summary' => 'nullable|string',
            'details' => 'nullable|array',
            'details.*.semester' => 'required_with:details|string',
            'details.*.course_code' => 'nullable|string',
            'details.*.course_name' => 'nullable|string',
            'details.*.credits' => 'nullable|numeric',
            'details.*.grade' => 'nullable|string',
            'details.*.grade_point' => 'nullable|numeric',
            'details.*.marks' => 'nullable|numeric',
            'details.*.semester_gpa' => 'nullable|numeric',
        ]);

        $transcript = $this->certificateService->createTranscript($validated);
        return response()->json(['data' => $transcript->load('details')], 201);
    }

    public function showTranscript(string $uuid): JsonResponse
    {
        $transcript = Transcript::with(['student', 'details', 'verifier', 'approver'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $transcript]);
    }

    public function verifyTranscript(string $uuid): JsonResponse
    {
        $transcript = Transcript::where('uuid', $uuid)->firstOrFail();
        $transcript->verify(auth()->id());
        return response()->json(['data' => $transcript->fresh()]);
    }

    public function approveTranscript(string $uuid): JsonResponse
    {
        $transcript = Transcript::where('uuid', $uuid)->firstOrFail();
        $transcript->approve(auth()->id());
        return response()->json(['data' => $transcript->fresh()]);
    }

    public function issueTranscript(string $uuid): JsonResponse
    {
        $transcript = Transcript::where('uuid', $uuid)->firstOrFail();
        $transcript->issue();
        return response()->json(['data' => $transcript->fresh()]);
    }

    // ===================== DIGITAL SIGNATURES =====================

    public function getSignatures(Request $request): JsonResponse
    {
        $filters = $request->only(['signature_type', 'status']);
        $signatures = DigitalSignature::when($filters['signature_type'] ?? null, function ($q) use ($filters) {
            $q->where('signature_type', $filters['signature_type']);
        })
        ->when($filters['status'] ?? null, function ($q) use ($filters) {
            $q->where('status', $filters['status']);
        })
        ->orderBy('name')
        ->get();

        return response()->json(['data' => $signatures]);
    }

    public function createSignature(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'designation' => 'required|string|max:150',
            'department' => 'nullable|string|max:100',
            'signature_image' => 'nullable|string',
            'seal_image' => 'nullable|string',
            'certificate_path' => 'nullable|string',
            'certificate_data' => 'nullable|string',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'signature_type' => 'required|in:principal,controller,registrar,dean,hod,authorized',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $signature = DigitalSignature::create(array_merge($validated, [
            'uuid' => \Illuminate\Support\Str::uuid(),
        ]));

        return response()->json(['data' => $signature], 201);
    }

    public function showSignature(string $uuid): JsonResponse
    {
        $signature = DigitalSignature::where('uuid', $uuid)->firstOrFail();
        return response()->json(['data' => $signature]);
    }

    public function updateSignature(Request $request, string $uuid): JsonResponse
    {
        $signature = DigitalSignature::where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:150',
            'designation' => 'sometimes|string|max:150',
            'department' => 'nullable|string|max:100',
            'signature_image' => 'nullable|string',
            'seal_image' => 'nullable|string',
            'certificate_path' => 'nullable|string',
            'certificate_data' => 'nullable|string',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date',
            'signature_type' => 'nullable|in:principal,controller,registrar,dean,hod,authorized',
            'status' => 'nullable|in:active,inactive,expired,revoked',
        ]);

        $signature->update($validated);
        return response()->json(['data' => $signature->fresh()]);
    }

    public function getActiveSignatures(): JsonResponse
    {
        $signatures = $this->certificateService->getActiveSignature ??
            DigitalSignature::where('status', 'active')
                ->where(function ($q) {
                    $q->whereNull('valid_until')
                        ->orWhere('valid_until', '>=', now()->toDateString());
                })
                ->get();

        return response()->json(['data' => $signatures]);
    }

    // ===================== DUPLICATES =====================

    public function getDuplicateRequests(Request $request): JsonResponse
    {
        $duplicates = CertificateRequest::where('certificate_type', 'other')
            ->where('remarks', 'like', '%duplicate%')
            ->with(['student'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'data' => $duplicates->items(),
            'meta' => [
                'current_page' => $duplicates->currentPage(),
                'last_page' => $duplicates->lastPage(),
                'total' => $duplicates->total(),
            ],
        ]);
    }

    public function requestDuplicate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'student_name' => 'required|string|max:255',
            'student_email' => 'nullable|email',
            'student_phone' => 'nullable|string|max:20',
            'original_certificate_no' => 'required|string|max:50',
            'reason' => 'required|string',
            'fee' => 'nullable|numeric|min:0',
        ]);

        $data = array_merge($validated, [
            'certificate_type' => 'other',
            'purpose' => 'Duplicate of: ' . $validated['original_certificate_no'],
            'remarks' => $validated['reason'],
        ]);

        $duplicateRequest = $this->certificateService->createRequest($data);
        return response()->json(['data' => $duplicateRequest], 201);
    }

    public function approveDuplicate(string $uuid): JsonResponse
    {
        $duplicateRequest = $this->certificateService->approveRequest($uuid, auth()->id());
        return response()->json(['data' => $duplicateRequest]);
    }

    // ===================== REPORTS =====================

    public function getIssuedReport(Request $request): JsonResponse
    {
        $certificates = Certificate::where('status', 'issued')
            ->when($request->get('from'), function ($q) use ($request) {
                $q->whereDate('issue_date', '>=', $request->get('from'));
            })
            ->when($request->get('to'), function ($q) use ($request) {
                $q->whereDate('issue_date', '<=', $request->get('to'));
            })
            ->with(['template'])
            ->orderBy('issue_date', 'desc')
            ->get();

        return response()->json(['data' => $certificates]);
    }

    public function getPendingReport(): JsonResponse
    {
        $pending = CertificateRequest::whereIn('status', ['pending', 'under_review'])
            ->with(['student'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $pending]);
    }

    public function getVerificationReport(): JsonResponse
    {
        $verifications = Certificate::whereNotNull('verified_at')
            ->orWhereNotNull('qr_code')
            ->orderBy('updated_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json(['data' => $verifications]);
    }

    // ===================== PUBLIC VERIFICATION =====================

    public function publicVerify(string $code): JsonResponse
    {
        $certificate = Certificate::where('certificate_no', $code)
            ->orWhere('certificate_code', $code)
            ->orWhere('qr_code', $code)
            ->first();

        if (!$certificate) {
            $transcript = Transcript::where('transcript_no', $code)->first();

            if ($transcript) {
                return response()->json([
                    'found' => true,
                    'type' => 'transcript',
                    'verified' => $transcript->status === 'issued',
                    'student_name' => $transcript->student_name,
                    'roll_number' => $transcript->roll_number,
                    'department' => $transcript->department,
                    'degree' => $transcript->degree,
                    'cgpa' => $transcript->cgpa,
                    'issue_date' => $transcript->issue_date?->format('Y-m-d'),
                ]);
            }

            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'type' => 'certificate',
            'verified' => $certificate->status === 'issued',
            'certificate_no' => $certificate->certificate_no,
            'certificate_type' => $certificate->certificate_type,
            'student_name' => $certificate->student_name,
            'issue_date' => $certificate->issue_date?->format('Y-m-d'),
            'qr_code' => $certificate->qr_code,
        ]);
    }
}
