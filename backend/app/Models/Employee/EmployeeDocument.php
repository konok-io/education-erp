<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeDocument extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_documents';

    protected $fillable = [
        'uuid',
        'employeeable_type',
        'employeeable_id',
        'document_type',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'issue_date',
        'expiry_date',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public const TYPE_PHOTO = 'photo';
    public const TYPE_SIGNATURE = 'signature';
    public const TYPE_NID = 'nid';
    public const TYPE_PASSPORT = 'passport';
    public const TYPE_APPOINTMENT_LETTER = 'appointment_letter';
    public const TYPE_JOINING_LETTER = 'joining_letter';
    public const TYPE_RESUME = 'resume';
    public const TYPE_ACADEMIC_CERTIFICATE = 'academic_certificate';
    public const TYPE_EXPERIENCE_CERTIFICATE = 'experience_certificate';
    public const TYPE_POLICE_CLEARANCE = 'police_clearance';
    public const TYPE_MEDICAL_CERTIFICATE = 'medical_certificate';
    public const TYPE_BANK_INFO = 'bank_info';
    public const TYPE_TIN = 'tin';
    public const TYPE_OTHER = 'other';

    public function employeeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
