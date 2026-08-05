<?php

declare(strict_types=1);

namespace App\Services\Certificate;

use App\Models\Certificate\Certificate;
use App\Models\Certificate\CertificateActivity;
use App\Models\Certificate\CertificateArchive;
use App\Models\Certificate\CertificateTemplate;
use App\Models\Certificate\CertificateVerification;
use App\Models\Certificate\DigitalSeal;
use App\Models\Certificate\DigitalSignature;
use App\Models\Certificate\DuplicateCertificateRequest;
use App\Models\Certificate\Marksheet;
use App\Models\Certificate\Transcript;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateService
{
    // ===================== CERTIFICATE METHODS =====================

    public function getCertificates(array $filters = []): LengthAwarePaginator
    {
        $query = Certificate::with(['template', 'signature', 'seal']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('certificate_number', 'like', "%{$filters['search']}%")
                  ->orWhere('student_name', 'like', "%{$filters['search']}%")
                  ->orWhere('student_roll', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['certificate_type'])) {
            $query->where('certificate_type', $filters['certificate_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderByDesc('created_at');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createCertificate(array $data): Certificate
    {
        return DB::transaction(function () use ($data) {
            $data['certificate_number'] = Certificate::generateCertificateNumber($data['certificate_type']);
            $data['verification_token'] = Certificate::generateVerificationToken();
            $data['digital_hash'] = Certificate::generateDigitalHash();
            $data['created_by'] = auth()->id();

            if (empty($data['status'])) {
                $data['status'] = Certificate::STATUS_DRAFT;
            }

            $certificate = Certificate::create($data);

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_CERTIFICATE_CREATED,
                'certificates',
                $certificate->id,
                null,
                ['certificate_number' => $certificate->certificate_number]
            );

            return $certificate;
        });
    }

    public function updateCertificate(Certificate $certificate, array $data): Certificate
    {
        $oldValues = $certificate->toArray();

        return DB::transaction(function () use ($certificate, $data, $oldValues) {
            $certificate->update($data);

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_CERTIFICATE_CREATED,
                'certificates',
                $certificate->id,
                $oldValues,
                $certificate->fresh()->toArray()
            );

            return $certificate->fresh();
        });
    }

    public function approveCertificate(Certificate $certificate): Certificate
    {
        return DB::transaction(function () use ($certificate) {
            $certificate->approve(auth()->id());

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_CERTIFICATE_APPROVED,
                'certificates',
                $certificate->id
            );

            return $certificate->fresh();
        });
    }

    public function issueCertificate(Certificate $certificate): Certificate
    {
        return DB::transaction(function () use ($certificate) {
            $certificate->issue(auth()->id());

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_CERTIFICATE_ISSUED,
                'certificates',
                $certificate->id
            );

            return $certificate->fresh();
        });
    }

    public function rejectCertificate(Certificate $certificate, ?string $reason = null): Certificate
    {
        return DB::transaction(function () use ($certificate, $reason) {
            $certificate->reject();
            if ($reason) {
                $certificate->update(['metadata' => array_merge($certificate->metadata ?? [], ['rejection_reason' => $reason])]);
            }

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_CERTIFICATE_REJECTED,
                'certificates',
                $certificate->id
            );

            return $certificate->fresh();
        });
    }

    public function verifyCertificate(string $token): ?Certificate
    {
        $certificate = Certificate::where('verification_token', $token)->first();

        if ($certificate && $certificate->status === Certificate::STATUS_ISSUED) {
            CertificateVerification::create([
                'certificate_number' => $certificate->certificate_number,
                'verification_token' => $token,
                'verification_method' => CertificateVerification::METHOD_QR,
                'verifier_ip' => request()->ip(),
                'verified_at' => now(),
                'status' => CertificateVerification::STATUS_SUCCESS,
            ]);

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_QR_VERIFIED,
                'certificates',
                $certificate->id
            );

            return $certificate;
        }

        return null;
    }

    // ===================== TEMPLATE METHODS =====================

    public function getTemplates(array $filters = []): LengthAwarePaginator
    {
        $query = CertificateTemplate::query();

        if (!empty($filters['certificate_type'])) {
            $query->where('certificate_type', $filters['certificate_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderBy('template_name');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createTemplate(array $data): CertificateTemplate
    {
        $data['template_code'] = $data['template_code'] ?? 'TPL-' . strtoupper(Str::random(6));

        return CertificateTemplate::create($data);
    }

    public function updateTemplate(CertificateTemplate $template, array $data): CertificateTemplate
    {
        $template->update($data);

        CertificateActivity::log(
            CertificateActivity::ACTIVITY_TEMPLATE_UPDATED,
            'certificate_templates',
            $template->id
        );

        return $template->fresh();
    }

    // ===================== TRANSCRIPT METHODS =====================

    public function getTranscripts(array $filters = []): LengthAwarePaginator
    {
        $query = Transcript::with(['signature', 'seal']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('transcript_number', 'like', "%{$filters['search']}%")
                  ->orWhere('student_name', 'like', "%{$filters['search']}%")
                  ->orWhere('student_roll', 'like', "%{$filters['search']}%");
            });
        }

        $query->orderByDesc('created_at');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createTranscript(array $data): Transcript
    {
        return DB::transaction(function () use ($data) {
            $data['transcript_number'] = Transcript::generateTranscriptNumber();
            $data['verification_token'] = Transcript::generateVerificationToken();
            $data['created_by'] = auth()->id();

            if (empty($data['status'])) {
                $data['status'] = Transcript::STATUS_DRAFT;
            }

            $transcript = Transcript::create($data);

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_TRANSCRIPT_GENERATED,
                'transcripts',
                $transcript->id,
                null,
                ['transcript_number' => $transcript->transcript_number]
            );

            return $transcript;
        });
    }

    public function approveTranscript(Transcript $transcript): Transcript
    {
        return DB::transaction(function () use ($transcript) {
            $transcript->approve(auth()->id());
            return $transcript->fresh();
        });
    }

    public function issueTranscript(Transcript $transcript): Transcript
    {
        return DB::transaction(function () use ($transcript) {
            $transcript->issue();
            return $transcript->fresh();
        });
    }

    // ===================== MARKSHEET METHODS =====================

    public function getMarksheets(array $filters = []): LengthAwarePaginator
    {
        $query = Marksheet::with(['signature', 'seal']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('marksheet_number', 'like', "%{$filters['search']}%")
                  ->orWhere('student_name', 'like', "%{$filters['search']}%")
                  ->orWhere('student_roll', 'like', "%{$filters['search']}%");
            });
        }

        $query->orderByDesc('created_at');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createMarksheet(array $data): Marksheet
    {
        return DB::transaction(function () use ($data) {
            $data['marksheet_number'] = Marksheet::generateMarksheetNumber();
            $data['verification_token'] = Marksheet::generateVerificationToken();
            $data['created_by'] = auth()->id();

            if (empty($data['status'])) {
                $data['status'] = Marksheet::STATUS_DRAFT;
            }

            $marksheet = Marksheet::create($data);

            if (isset($data['subject_marks'])) {
                $marksheet->calculateGrade();
                $marksheet->save();
            }

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_MARKESHEET_GENERATED,
                'marksheets',
                $marksheet->id,
                null,
                ['marksheet_number' => $marksheet->marksheet_number]
            );

            return $marksheet;
        });
    }

    public function approveMarksheet(Marksheet $marksheet): Marksheet
    {
        return DB::transaction(function () use ($marksheet) {
            $marksheet->approve(auth()->id());
            return $marksheet->fresh();
        });
    }

    public function issueMarksheet(Marksheet $marksheet): Marksheet
    {
        return DB::transaction(function () use ($marksheet) {
            $marksheet->issue();
            return $marksheet->fresh();
        });
    }

    // ===================== DIGITAL SIGNATURE METHODS =====================

    public function getSignatures(array $filters = []): LengthAwarePaginator
    {
        $query = DigitalSignature::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->active();
        $query->orderBy('signatory_name');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createSignature(array $data): DigitalSignature
    {
        return DigitalSignature::create($data);
    }

    public function updateSignature(DigitalSignature $signature, array $data): DigitalSignature
    {
        $signature->update($data);
        return $signature->fresh();
    }

    // ===================== DIGITAL SEAL METHODS =====================

    public function getSeals(array $filters = []): LengthAwarePaginator
    {
        $query = DigitalSeal::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->active();
        $query->orderBy('seal_name');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createSeal(array $data): DigitalSeal
    {
        $data['seal_code'] = $data['seal_code'] ?? 'SEAL-' . strtoupper(Str::random(6));
        return DigitalSeal::create($data);
    }

    public function updateSeal(DigitalSeal $seal, array $data): DigitalSeal
    {
        $seal->update($data);
        return $seal->fresh();
    }

    // ===================== ARCHIVE METHODS =====================

    public function getArchive(array $filters = []): LengthAwarePaginator
    {
        $query = CertificateArchive::with(['uploader']);

        if (!empty($filters['document_type'])) {
            $query->where('document_type', $filters['document_type']);
        }

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        $query->active();
        $query->orderByDesc('created_at');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function archiveDocument(array $data): CertificateArchive
    {
        $data['file_hash'] = hash_file('sha256', $data['file_path']);

        $archive = CertificateArchive::create($data);

        CertificateActivity::log(
            CertificateActivity::ACTIVITY_DOCUMENT_ARCHIVED,
            'certificate_archive',
            $archive->id
        );

        return $archive;
    }

    // ===================== DUPLICATE REQUEST METHODS =====================

    public function getDuplicateRequests(array $filters = []): LengthAwarePaginator
    {
        $query = DuplicateCertificateRequest::with(['reviewer']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderByDesc('created_at');
        return $query->paginate($filters['per_page'] ?? 20);
    }

    public function createDuplicateRequest(array $data): DuplicateCertificateRequest
    {
        $data['request_number'] = DuplicateCertificateRequest::generateRequestNumber();
        return DuplicateCertificateRequest::create($data);
    }

    public function approveDuplicateRequest(DuplicateCertificateRequest $request): DuplicateCertificateRequest
    {
        return DB::transaction(function () use ($request) {
            $request->approve();

            CertificateActivity::log(
                CertificateActivity::ACTIVITY_DUPLICATE_APPROVED,
                'duplicate_certificate_requests',
                $request->id
            );

            return $request->fresh();
        });
    }

    // ===================== VERIFICATION METHODS =====================

    public function getVerifications(array $filters = []): LengthAwarePaginator
    {
        $query = CertificateVerification::query();

        if (!empty($filters['certificate_number'])) {
            $query->where('certificate_number', $filters['certificate_number']);
        }

        $query->orderByDesc('verified_at');
        return $query->paginate($filters['per_page'] ?? 50);
    }

    // ===================== DASHBOARD METHODS =====================

    public function getDashboardData(): array
    {
        return [
            'total_certificates' => Certificate::count(),
            'certificates_issued' => Certificate::issued()->count(),
            'pending_approval' => Certificate::pending()->count(),
            'total_transcripts' => Transcript::count(),
            'transcripts_issued' => Transcript::issued()->count(),
            'total_marksheets' => Marksheet::count(),
            'marksheets_issued' => Marksheet::issued()->count(),
            'today_downloads' => CertificateVerification::whereDate('verified_at', now())->count(),
            'verifications_today' => CertificateVerification::whereDate('verified_at', now())->count(),
            'pending_duplicates' => DuplicateCertificateRequest::pending()->count(),
            'active_templates' => CertificateTemplate::where('status', CertificateTemplate::STATUS_ACTIVE)->count(),
            'active_signatures' => DigitalSignature::active()->count(),
            'active_seals' => DigitalSeal::active()->count(),
        ];
    }
}
