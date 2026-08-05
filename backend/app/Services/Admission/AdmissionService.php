<?php

declare(strict_types=1);

namespace App\Services\Admission;

use App\Models\Admission\AdmissionCampaign;
use App\Models\Admission\AdmissionApplication;
use App\Models\Admission\AdmissionDocument;
use App\Models\Admission\AdmissionPayment;
use App\Models\Admission\QuotaConfiguration;
use App\Models\Student\Student;
use App\Models\User;
use App\Models\Student\StudentProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdmissionService
{
    // ===================== CAMPAIGNS =====================

    public function getCampaigns(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = AdmissionCampaign::with(['session', 'academicLevel']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['academic_level_id'])) {
            $level = \App\Models\Academic\AcademicLevel::where('uuid', $filters['academic_level_id'])->first();
            if ($level) {
                $query->where('academic_level_id', $level->id);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createCampaign(array $data): AdmissionCampaign
    {
        return DB::transaction(function () use ($data) {
            return AdmissionCampaign::create([
                'uuid' => (string) Str::uuid(),
                'title' => $data['title'],
                'title_bn' => $data['title_bn'] ?? null,
                'academic_session_id' => $this->getModelId(\App\Models\Academic\AcademicSession::class, $data['session_id']),
                'academic_level_id' => $this->getModelId(\App\Models\Academic\AcademicLevel::class, $data['academic_level_id'] ?? null),
                'program_id' => $this->getModelId(\App\Models\Academic\Program::class, $data['program_id'] ?? null),
                'department_id' => $this->getModelId(\App\Models\Academic\Department::class, $data['department_id'] ?? null),
                'application_fee' => $data['application_fee'] ?? 0,
                'late_fee' => $data['late_fee'] ?? 0,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'result_date' => $data['result_date'] ?? null,
                'admission_date' => $data['admission_date'] ?? null,
                'total_seats' => $data['total_seats'] ?? 100,
                'status' => AdmissionCampaign::STATUS_DRAFT,
                'description' => $data['description'] ?? null,
                'requirements' => $data['requirements'] ?? null,
                'eligibility_criteria' => $data['eligibility_criteria'] ?? null,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function updateCampaign(string $uuid, array $data): AdmissionCampaign
    {
        $campaign = AdmissionCampaign::where('uuid', $uuid)->firstOrFail();

        $campaign->update(array_intersect_key($data, array_flip([
            'title', 'title_bn', 'start_date', 'end_date', 'result_date',
            'admission_date', 'application_fee', 'late_fee', 'total_seats',
            'status', 'description', 'requirements', 'eligibility_criteria'
        ])));

        return $campaign->fresh();
    }

    public function toggleCampaign(string $uuid): AdmissionCampaign
    {
        $campaign = AdmissionCampaign::where('uuid', $uuid)->firstOrFail();
        $campaign->update(['is_active' => !$campaign->is_active]);
        return $campaign->fresh();
    }

    // ===================== APPLICATIONS =====================

    public function getApplications(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = AdmissionApplication::with(['campaign', 'documents']);

        if (!empty($filters['campaign_id'])) {
            $campaign = AdmissionCampaign::where('uuid', $filters['campaign_id'])->first();
            if ($campaign) {
                $query->where('campaign_id', $campaign->id);
            }
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['quota'])) {
            $query->where('quota', $filters['quota']);
        }

        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('application_no', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('applicant_name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('mobile', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getApplication(string $uuid): ?AdmissionApplication
    {
        return AdmissionApplication::where('uuid', $uuid)
            ->with(['campaign', 'documents', 'payments'])
            ->first();
    }

    public function createApplication(array $data): AdmissionApplication
    {
        return DB::transaction(function () use ($data) {
            $campaign = AdmissionCampaign::findOrFail($data['campaign_id']);

            return AdmissionApplication::create([
                'uuid' => (string) Str::uuid(),
                'application_no' => AdmissionApplication::generateApplicationNo(),
                'campaign_id' => $campaign->id,
                'applicant_name' => $data['applicant_name'],
                'father_name' => $data['father_name'],
                'mother_name' => $data['mother_name'],
                'guardian_name' => $data['guardian_name'] ?? $data['father_name'],
                'guardian_relation' => $data['guardian_relation'] ?? 'Father',
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'religion' => $data['religion'] ?? null,
                'nationality' => $data['nationality'] ?? 'Bangladeshi',
                'blood_group' => $data['blood_group'] ?? null,
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'present_address' => $data['present_address'] ?? null,
                'permanent_address' => $data['permanent_address'] ?? null,
                'ssc_gpa' => $data['ssc_gpa'] ?? null,
                'ssc_board' => $data['ssc_board'] ?? null,
                'ssc_group' => $data['ssc_group'] ?? null,
                'ssc_passing_year' => $data['ssc_passing_year'] ?? null,
                'hsc_gpa' => $data['hsc_gpa'] ?? null,
                'hsc_board' => $data['hsc_board'] ?? null,
                'hsc_group' => $data['hsc_group'] ?? null,
                'hsc_passing_year' => $data['hsc_passing_year'] ?? null,
                'quota' => $data['quota'] ?? AdmissionApplication::QUOTA_GENERAL,
                'selected_program_id' => $data['selected_program_id'] ?? null,
                'selected_shift' => $data['selected_shift'] ?? 'day',
                'status' => AdmissionApplication::STATUS_DRAFT,
                'payment_status' => 'unpaid',
                'created_by' => auth()->id(),
            ]);
        });
    }

    public function updateApplication(string $uuid, array $data): AdmissionApplication
    {
        $application = AdmissionApplication::where('uuid', $uuid)->firstOrFail();

        $fillableFields = [
            'applicant_name', 'father_name', 'mother_name', 'guardian_name',
            'date_of_birth', 'gender', 'religion', 'nationality', 'blood_group',
            'email', 'mobile', 'present_address', 'permanent_address',
            'ssc_gpa', 'ssc_board', 'ssc_group', 'ssc_passing_year',
            'hsc_gpa', 'hsc_board', 'hsc_group', 'hsc_passing_year',
            'quota', 'selected_shift', 'remarks'
        ];

        $application->update(array_intersect_key($data, array_flip($fillableFields)));

        return $application->fresh(['campaign', 'documents', 'payments']);
    }

    public function submitApplication(string $uuid): void
    {
        $application = AdmissionApplication::where('uuid', $uuid)->firstOrFail();

        // Validate required documents
        $requiredTypes = [AdmissionDocument::TYPE_PHOTO, AdmissionDocument::TYPE_SIGNATURE];
        foreach ($requiredTypes as $type) {
            if (!$application->documents()->where('document_type', $type)->exists()) {
                throw new \Exception("Required document missing: " . AdmissionDocument::documentTypes()[$type]);
            }
        }

        $application->update([
            'status' => AdmissionApplication::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    // ===================== DOCUMENTS =====================

    public function uploadDocument(int $applicationId, string $documentType, UploadedFile $file): AdmissionDocument
    {
        $path = $file->store('admission/documents/' . $applicationId, 'public');

        return AdmissionDocument::create([
            'uuid' => (string) Str::uuid(),
            'application_id' => $applicationId,
            'document_type' => $documentType,
            'document_name' => AdmissionDocument::documentTypes()[$documentType] ?? $documentType,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'is_verified' => false,
        ]);
    }

    public function verifyDocument(string $uuid, bool $isVerified, ?string $reason, int $userId): void
    {
        $document = AdmissionDocument::where('uuid', $uuid)->firstOrFail();

        $document->update([
            'is_verified' => $isVerified,
            'verified_by' => $userId,
            'verified_at' => now(),
            'rejection_reason' => $isVerified ? null : $reason,
        ]);
    }

    // ===================== PAYMENTS =====================

    public function initiatePayment(int $applicationId, float $amount, string $method, ?string $transactionId = null): AdmissionPayment
    {
        $application = AdmissionApplication::findOrFail($applicationId);

        return AdmissionPayment::create([
            'uuid' => (string) Str::uuid(),
            'application_id' => $applicationId,
            'payment_no' => AdmissionPayment::generatePaymentNo(),
            'amount' => $amount,
            'payment_type' => AdmissionPayment::TYPE_APPLICATION,
            'payment_method' => $method,
            'transaction_id' => $transactionId ?? null,
            'payment_date' => now(),
            'status' => AdmissionPayment::STATUS_PENDING,
        ]);
    }

    public function verifyPayment(string $uuid, int $userId): void
    {
        $payment = AdmissionPayment::where('uuid', $uuid)->firstOrFail();

        $payment->update([
            'status' => AdmissionPayment::STATUS_PAID,
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);

        // Update application payment status
        $payment->application->update([
            'payment_status' => 'paid',
            'payment_amount' => $payment->amount,
            'payment_date' => now(),
            'payment_method' => $payment->payment_method,
            'transaction_id' => $payment->transaction_id,
        ]);
    }

    // ===================== MERIT & APPROVAL =====================

    public function generateMeritList(int $campaignId): array
    {
        $campaign = AdmissionCampaign::findOrFail($campaignId);

        $applications = AdmissionApplication::where('campaign_id', $campaignId)
            ->whereIn('status', [
                AdmissionApplication::STATUS_DOCUMENT_VERIFIED,
                AdmissionApplication::STATUS_TEST_COMPLETED,
                AdmissionApplication::STATUS_INTERVIEW_SCHEDULED,
            ])
            ->orderByDesc('hsc_gpa')
            ->orderBy('ssc_gpa', 'desc')
            ->get();

        $meritList = [];
        $position = 1;

        foreach ($applications as $app) {
            if ($position <= $campaign->total_seats) {
                $app->update([
                    'status' => AdmissionApplication::STATUS_MERIT,
                    'merit_position' => $position,
                    'is_waiting' => false,
                ]);
            } else {
                $app->update([
                    'status' => AdmissionApplication::STATUS_WAITING,
                    'merit_position' => $position,
                    'is_waiting' => true,
                    'waiting_position' => $position - $campaign->total_seats,
                ]);
            }

            $meritList[] = [
                'position' => $position++,
                'application_no' => $app->application_no,
                'name' => $app->applicant_name,
                'gpa' => $app->hsc_gpa,
                'status' => $app->status,
            ];
        }

        return $meritList;
    }

    public function updateMeritPosition(string $uuid, int $position): AdmissionApplication
    {
        $application = AdmissionApplication::where('uuid', $uuid)->firstOrFail();
        $application->update(['merit_position' => $position]);
        return $application->fresh();
    }

    public function approveApplication(string $uuid, int $userId): array
    {
        return DB::transaction(function () use ($uuid, $userId) {
            $application = AdmissionApplication::where('uuid', $uuid)
                ->with('campaign')
                ->firstOrFail();

            // Update application status
            $application->update([
                'status' => AdmissionApplication::STATUS_APPROVED,
            ]);

            // Create student
            $student = Student::create([
                'uuid' => (string) Str::uuid(),
                'student_no' => Student::generateStudentNo(),
                'admission_session_id' => $application->campaign->academic_session_id,
                'academic_level_id' => $application->campaign->academic_level_id,
                'class_id' => $application->selected_program_id,
                'admission_date' => now(),
                'status' => 'active',
            ]);

            // Create student profile
            StudentProfile::create([
                'uuid' => (string) Str::uuid(),
                'student_id' => $student->id,
                'first_name' => $application->applicant_name,
                'father_name' => $application->father_name,
                'mother_name' => $application->mother_name,
                'date_of_birth' => $application->date_of_birth,
                'gender' => $application->gender,
                'religion' => $application->religion,
                'email' => $application->email,
                'mobile' => $application->mobile,
                'present_address' => $application->present_address,
                'permanent_address' => $application->permanent_address,
            ]);

            // Create user account
            $user = User::create([
                'uuid' => (string) Str::uuid(),
                'name' => $application->applicant_name,
                'email' => $application->email,
                'password' => Hash::make('Student@123'),
                'user_type' => 'student',
                'reference_id' => $student->id,
                'status' => 'active',
            ]);

            return [
                'student' => [
                    'id' => $student->uuid,
                    'student_no' => $student->student_no,
                ],
                'user' => [
                    'id' => $user->uuid,
                    'email' => $user->email,
                ],
            ];
        });
    }

    public function rejectApplication(string $uuid, string $reason): void
    {
        $application = AdmissionApplication::where('uuid', $uuid)->firstOrFail();
        $application->update([
            'status' => AdmissionApplication::STATUS_REJECTED,
            'remarks' => $reason,
        ]);
    }

    // ===================== INTERVIEW =====================

    public function scheduleInterview(string $uuid, string $date, string $time, string $venue): AdmissionApplication
    {
        $application = AdmissionApplication::where('uuid', $uuid)->firstOrFail();

        $application->update([
            'interview_date' => $date,
            'interview_time' => $time,
            'interview_venue' => $venue,
            'status' => AdmissionApplication::STATUS_INTERVIEW_SCHEDULED,
        ]);

        return $application->fresh();
    }

    // ===================== DASHBOARD =====================

    public function getDashboard(?int $campaignId = null): array
    {
        $query = AdmissionApplication::query();

        if ($campaignId) {
            $query->where('campaign_id', $campaignId);
        }

        $total = $query->count();
        $pending = (clone $query)->where('status', '!=', AdmissionApplication::STATUS_APPROVED)->count();
        $approved = (clone $query)->where('status', AdmissionApplication::STATUS_APPROVED)->count();
        $paid = (clone $query)->where('payment_status', 'paid')->count();
        $merit = (clone $query)->where('status', AdmissionApplication::STATUS_MERIT)->count();

        return [
            'total_applications' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'paid' => $paid,
            'merit' => $merit,
        ];
    }

    public function getApplicantDashboard(string $applicationNo): array
    {
        $application = AdmissionApplication::where('application_no', $applicationNo)
            ->with(['campaign', 'documents', 'payments'])
            ->firstOrFail();

        return [
            'application' => [
                'no' => $application->application_no,
                'status' => $application->status,
                'payment_status' => $application->payment_status,
                'merit_position' => $application->merit_position,
                'is_waiting' => $application->is_waiting,
                'interview_date' => $application->interview_date,
                'interview_venue' => $application->interview_venue,
            ],
            'campaign' => $application->campaign ? [
                'title' => $application->campaign->title,
                'application_fee' => $application->campaign->application_fee,
            ] : null,
            'documents' => $application->documents->map(fn($d) => [
                'type' => $d->document_type,
                'name' => $d->document_name,
                'is_verified' => $d->is_verified,
            ]),
            'payments' => $application->payments->map(fn($p) => [
                'amount' => $p->amount,
                'method' => $p->payment_method,
                'status' => $p->status,
            ]),
        ];
    }

    // ===================== REPORTS =====================

    public function getReport(int $campaignId, array $filters = []): array
    {
        $query = AdmissionApplication::where('campaign_id', $campaignId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['quota'])) {
            $query->where('quota', $filters['quota']);
        }

        $applications = $query->get();

        $byStatus = $applications->groupBy('status')->map->count();
        $byQuota = $applications->groupBy('quota')->map->count();
        $byGender = $applications->groupBy('gender')->map->count();

        return [
            'total' => $applications->count(),
            'by_status' => $byStatus,
            'by_quota' => $byQuota,
            'by_gender' => $byGender,
        ];
    }

    // ===================== EXPORT =====================

    public function exportApplications(int $campaignId, string $format, array $filters = []): string
    {
        $filename = "admission_applications_{$campaignId}_" . now()->format('Ymd_His');
        return url("storage/exports/{$filename}.{$format}");
    }

    // ===================== HELPERS =====================

    private function getModelId(string $model, ?string $uuid): ?int
    {
        if (!$uuid) {
            return null;
        }

        $record = $model::where('uuid', $uuid)->first();
        return $record?->id;
    }
}
