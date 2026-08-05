<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NocCertificate extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'noc_certificates';

    protected $fillable = [
        'uuid',
        'certificate_no',
        'employee_id',
        'noc_type',
        'issue_date',
        'purpose',
        'content',
        'is_verified',
        'verification_code',
        'qr_code',
        'pdf_file',
        'issued_by',
        'authorized_by',
        'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'is_verified' => 'boolean',
    ];

    // ===================== TYPES =====================
    public const TYPE_GENERAL = 'general';
    public const TYPE_VISA = 'visa';
    public const TYPE_IMMIGRATION = 'immigration';
    public const TYPE_EMPLOYMENT = 'employment';
    public const TYPE_GOVERNMENT = 'government';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function authorizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    // ===================== METHODS =====================

    public static function generateCertificateNo(): string
    {
        $prefix = 'NOC';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function generateVerificationCode(): string
    {
        return strtoupper(Str::random(12));
    }

    public static function types(): array
    {
        return [
            self::TYPE_GENERAL => 'General NOC',
            self::TYPE_VISA => 'Visa NOC',
            self::TYPE_IMMIGRATION => 'Immigration NOC',
            self::TYPE_EMPLOYMENT => 'Employment NOC',
            self::TYPE_GOVERNMENT => 'Government NOC',
        ];
    }

    public function markVerified(): void
    {
        $this->update(['is_verified' => true]);
    }

    public function getVerificationUrlAttribute(): string
    {
        return url("/verify/noc/{$this->verification_code}");
    }
}
