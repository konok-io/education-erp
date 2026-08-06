<?php

declare(strict_types=1);

namespace App\Services\Document;

use App\Models\Document\DocumentVerification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentService
{
    public function getVerifications(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = DocumentVerification::with(['certificate', 'student']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['document_type'])) {
            $query->where('document_type', $filters['document_type']);
        }

        if (!empty($filters['verification_type'])) {
            $query->where('verification_type', $filters['verification_type']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('verification_no', 'like', "%{$filters['search']}%")
                    ->orWhere('applicant_name', 'like', "%{$filters['search']}%")
                    ->orWhere('document_number', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createVerification(array $data): DocumentVerification
    {
        return DB::transaction(function () use ($data) {
            $uuid = (string) Str::uuid();

            return DocumentVerification::create([
                'uuid' => $uuid,
                'verification_no' => DocumentVerification::generateVerificationNo(),
                'certificate_id' => $data['certificate_id'] ?? null,
                'student_id' => $data['student_id'] ?? null,
                'applicant_name' => $data['applicant_name'],
                'applicant_email' => $data['applicant_email'] ?? null,
                'applicant_phone' => $data['applicant_phone'] ?? null,
                'document_type' => $data['document_type'],
                'document_name' => $data['document_name'] ?? null,
                'document_number' => $data['document_number'] ?? null,
                'document_path' => $data['document_path'] ?? null,
                'issue_date' => $data['issue_date'] ?? null,
                'verification_type' => $data['verification_type'] ?? 'self',
                'verifier_name' => $data['verifier_name'] ?? null,
                'verifier_email' => $data['verifier_email'] ?? null,
                'verifier_organization' => $data['verifier_organization'] ?? null,
                'qr_code' => $uuid,
                'verification_link' => DocumentVerification::generateVerificationLink($uuid),
                'status' => 'pending',
            ]);
        });
    }

    public function verifyDocument(string $uuid, int $verifiedBy, ?string $details = null): DocumentVerification
    {
        $verification = DocumentVerification::where('uuid', $uuid)->firstOrFail();
        $verification->markAsVerified($verifiedBy, $details);
        return $verification->fresh();
    }

    public function rejectVerification(string $uuid, int $verifiedBy, ?string $reason = null): DocumentVerification
    {
        $verification = DocumentVerification::where('uuid', $uuid)->firstOrFail();
        $verification->markAsRejected($verifiedBy, $reason);
        return $verification->fresh();
    }

    public function verifyByPublic(string $verificationNo): ?DocumentVerification
    {
        $verification = DocumentVerification::where('verification_no', $verificationNo)
            ->orWhere('qr_code', $verificationNo)
            ->first();

        if ($verification && $verification->status === 'verified') {
            return $verification;
        }

        return null;
    }

    public function getPublicVerification(string $code): array
    {
        $verification = $this->verifyByPublic($code);

        if (!$verification) {
            return ['found' => false];
        }

        return [
            'found' => true,
            'verified' => $verification->status === 'verified',
            'document_type' => $verification->document_type,
            'document_number' => $verification->document_number,
            'applicant_name' => $verification->applicant_name,
            'issue_date' => $verification->issue_date?->format('Y-m-d'),
            'verified_at' => $verification->verified_at?->format('Y-m-d H:i:s'),
            'verification_no' => $verification->verification_no,
        ];
    }

    public function getDashboardStats(): array
    {
        return [
            'total_verifications' => DocumentVerification::count(),
            'pending' => DocumentVerification::where('status', 'pending')->count(),
            'verified' => DocumentVerification::where('status', 'verified')->count(),
            'rejected' => DocumentVerification::where('status', 'rejected')->count(),
        ];
    }
}
