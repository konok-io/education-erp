<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'certificates';

    protected $fillable = [
        'uuid',
        'certificate_number',
        'certificate_type',
        'template_id',
        'student_id',
        'student_name',
        'student_roll',
        'registration_no',
        'father_name',
        'mother_name',
        'department',
        'class_name',
        'section',
        'session',
        'semester',
        'academic_year',
        'content',
        'metadata',
        'qr_code',
        'barcode',
        'verification_token',
        'digital_hash',
        'pdf_path',
        'signature_id',
        'seal_id',
        'issue_date',
        'valid_until',
        'reason',
        'conduct',
        'status',
        'approved_by',
        'approved_at',
        'issued_by',
        'issued_at',
        'created_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'issue_date' => 'date',
        'valid_until' => 'date',
        'approved_at' => 'datetime',
        'issued_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_APPROVAL = 'pending_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REVOKED = 'revoked';

    // ===================== TYPES =====================
    public const TYPE_TRANSFER = 'transfer';
    public const TYPE_CHARACTER = 'character';
    public const TYPE_TESTIMONIAL = 'testimonial';
    public const TYPE_BONAFIDE = 'bonafide';
    public const TYPE_COURSE_COMPLETION = 'course_completion';
    public const TYPE_INTERNSHIP = 'internship';
    public const TYPE_EXPERIENCE = 'experience';
    public const TYPE_MIGRATION = 'migration';
    public const TYPE_PROVISIONAL = 'provisional';
    public const TYPE_PASSING = 'passing';
    public const TYPE_MERIT = 'merit';
    public const TYPE_APPRECIATION = 'appreciation';
    public const TYPE_PARTICIPATION = 'participation';

    // ===================== RELATIONSHIPS =====================

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function signature(): BelongsTo
    {
        return $this->belongsTo(DigitalSignature::class, 'signature_id');
    }

    public function seal(): BelongsTo
    {
        return $this->belongsTo(DigitalSeal::class, 'seal_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'issued_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // ===================== SCOPES =====================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING_APPROVAL);
    }

    public function scopeIssued($query)
    {
        return $query->where('status', self::STATUS_ISSUED);
    }

    public function scopeValid($query)
    {
        return $query->where('status', self::STATUS_ISSUED)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            });
    }

    // ===================== METHODS =====================

    public static function generateCertificateNumber(string $type): string
    {
        $prefix = self::getTypePrefix($type);
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)
            ->where('certificate_type', $type)
            ->count() + 1;
        return sprintf('%s-%s-%s-%06d', $prefix, $year, strtoupper(Str::random(4)), $count);
    }

    public static function generateVerificationToken(): string
    {
        return Str::random(32);
    }

    public static function generateDigitalHash(): string
    {
        return hash('sha256', Str::random(64) . now()->timestamp);
    }

    public static function certificateTypes(): array
    {
        return [
            self::TYPE_TRANSFER => 'Transfer Certificate',
            self::TYPE_CHARACTER => 'Character Certificate',
            self::TYPE_TESTIMONIAL => 'Testimonial',
            self::TYPE_BONAFIDE => 'Bonafide Certificate',
            self::TYPE_COURSE_COMPLETION => 'Course Completion Certificate',
            self::TYPE_INTERNSHIP => 'Internship Certificate',
            self::TYPE_EXPERIENCE => 'Experience Certificate',
            self::TYPE_MIGRATION => 'Migration Certificate',
            self::TYPE_PROVISIONAL => 'Provisional Certificate',
            self::TYPE_PASSING => 'Passing Certificate',
            self::TYPE_MERIT => 'Merit Certificate',
            self::TYPE_APPRECIATION => 'Appreciation Certificate',
            self::TYPE_PARTICIPATION => 'Participation Certificate',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_APPROVAL => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ISSUED => 'Issued',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_REVOKED => 'Revoked',
        ];
    }

    protected static function getTypePrefix(string $type): string
    {
        return match ($type) {
            self::TYPE_TRANSFER => 'TC',
            self::TYPE_CHARACTER => 'CC',
            self::TYPE_TESTIMONIAL => 'TM',
            self::TYPE_BONAFIDE => 'BF',
            self::TYPE_COURSE_COMPLETION => 'CC',
            self::TYPE_INTERNSHIP => 'IC',
            self::TYPE_EXPERIENCE => 'EC',
            self::TYPE_MIGRATION => 'MC',
            self::TYPE_PROVISIONAL => 'PC',
            self::TYPE_PASSING => 'PSC',
            self::TYPE_MERIT => 'MC',
            self::TYPE_APPRECIATION => 'AC',
            self::TYPE_PARTICIPATION => 'PNC',
            default => 'CERT',
        };
    }

    public function verify(string $token): bool
    {
        return $this->verification_token === $token && 
               $this->status === self::STATUS_ISSUED &&
               (!$this->valid_until || $this->valid_until >= now());
    }

    public function approve(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function issue(int $userId): void
    {
        $this->update([
            'status' => self::STATUS_ISSUED,
            'issued_by' => $userId,
            'issued_at' => now(),
            'issue_date' => now(),
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => self::STATUS_REJECTED]);
    }

    public function revoke(): void
    {
        $this->update(['status' => self::STATUS_REVOKED]);
    }
}
