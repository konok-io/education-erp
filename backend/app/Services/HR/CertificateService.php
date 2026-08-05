<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\ExperienceCertificate;
use App\Models\HR\NocCertificate;
use App\Models\HR\ServiceBook;
use App\Models\Employee\Employee;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateService
{
    // ===================== EXPERIENCE CERTIFICATE =====================

    public function getExperienceCertificates(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = ExperienceCertificate::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['is_verified'])) {
            $query->where('is_verified', $filters['is_verified']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createExperienceCertificate(array $data): ExperienceCertificate
    {
        $employee = Employee::findOrFail($data['employee_id']);
        $joiningDate = $employee->joining_date;
        $endDate = $data['end_date'] ?? now();

        // Calculate duration
        $totalDays = $joiningDate->diffInDays($endDate);
        $totalMonths = $joiningDate->diffInMonths($endDate);
        $totalYears = floor($totalMonths / 12);
        $remainingMonths = $totalMonths % 12;

        return ExperienceCertificate::create([
            'uuid' => (string) Str::uuid(),
            'certificate_no' => ExperienceCertificate::generateCertificateNo(),
            'employee_id' => $data['employee_id'],
            'issue_date' => $data['issue_date'] ?? now(),
            'start_date' => $joiningDate,
            'end_date' => $endDate,
            'total_years' => $totalYears,
            'total_months' => $remainingMonths,
            'experience_summary' => $data['experience_summary'] ?? null,
            'performance_remarks' => $data['performance_remarks'] ?? null,
            'reason_for_leaving' => $data['reason_for_leaving'] ?? null,
            'is_verified' => false,
            'verification_code' => ExperienceCertificate::generateVerificationCode(),
            'pdf_file' => $data['pdf_file'] ?? null,
            'issued_by' => $data['issued_by'] ?? null,
            'authorized_by' => $data['authorized_by'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    public function verifyExperienceCertificate(string $code): ?ExperienceCertificate
    {
        $certificate = ExperienceCertificate::where('verification_code', $code)->first();
        if ($certificate) {
            $certificate->markVerified();
        }
        return $certificate;
    }

    // ===================== NOC CERTIFICATE =====================

    public function getNocCertificates(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = NocCertificate::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['noc_type'])) {
            $query->where('noc_type', $filters['noc_type']);
        }

        if (!empty($filters['is_verified'])) {
            $query->where('is_verified', $filters['is_verified']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createNocCertificate(array $data): NocCertificate
    {
        return NocCertificate::create([
            'uuid' => (string) Str::uuid(),
            'certificate_no' => NocCertificate::generateCertificateNo(),
            'employee_id' => $data['employee_id'],
            'noc_type' => $data['noc_type'],
            'issue_date' => $data['issue_date'] ?? now(),
            'purpose' => $data['purpose'] ?? null,
            'content' => $data['content'] ?? null,
            'is_verified' => false,
            'verification_code' => NocCertificate::generateVerificationCode(),
            'pdf_file' => $data['pdf_file'] ?? null,
            'issued_by' => $data['issued_by'] ?? null,
            'authorized_by' => $data['authorized_by'] ?? null,
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    public function verifyNocCertificate(string $code): ?NocCertificate
    {
        $certificate = NocCertificate::where('verification_code', $code)->first();
        if ($certificate) {
            $certificate->markVerified();
        }
        return $certificate;
    }

    // ===================== GENERATE CERTIFICATE PDF =====================

    public function generateExperiencePdf(string $uuid): string
    {
        // This would typically use a PDF library like DomPDF
        // For now, return a placeholder path
        $certificate = ExperienceCertificate::where('uuid', $uuid)->firstOrFail();
        $path = "certificates/experience/{$certificate->certificate_no}.pdf";

        // In production, you would generate actual PDF here
        // $pdf = PDF::loadView('certificates.experience', ['certificate' => $certificate]);
        // Storage::put($path, $pdf->output());

        $certificate->update(['pdf_file' => $path]);
        return $path;
    }

    public function generateNocPdf(string $uuid): string
    {
        $certificate = NocCertificate::where('uuid', $uuid)->firstOrFail();
        $path = "certificates/noc/{$certificate->certificate_no}.pdf";

        $certificate->update(['pdf_file' => $path]);
        return $path;
    }
}
