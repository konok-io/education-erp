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

class ExperienceCertificate extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'experience_certificates';

    protected $fillable = [
        'uuid',
        'certificate_no',
        'employee_id',
        'issue_date',
        'start_date',
        'end_date',
        'total_years',
        'total_months',
        'experience_summary',
        'performance_remarks',
        'reason_for_leaving',
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
        'start_date' => 'date',
        'end_date' => 'date',
        'is_verified' => 'boolean',
        'total_years' => 'integer',
        'total_months' => 'integer',
    ];

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
        $prefix = 'EXP';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function generateVerificationCode(): string
    {
        return strtoupper(Str::random(12));
    }

    public function markVerified(): void
    {
        $this->update(['is_verified' => true]);
    }

    public function getDurationFormattedAttribute(): string
    {
        $parts = [];
        if ($this->total_years > 0) {
            $parts[] = $this->total_years . ' year(s)';
        }
        if ($this->total_months > 0) {
            $parts[] = $this->total_months . ' month(s)';
        }
        return implode(' ', $parts) ?: 'N/A';
    }

    public function getVerificationUrlAttribute(): string
    {
        return url("/verify/experience/{$this->verification_code}");
    }
}
