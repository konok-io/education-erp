<?php

declare(strict_types=1);

namespace App\Models\Student;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentDocument extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'student_documents';

    protected $fillable = [
        'uuid',
        'studentable_type',
        'studentable_id',
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

    /**
     * Document types.
     */
    public const TYPE_BIRTH_CERTIFICATE = 'birth_certificate';
    public const TYPE_NID = 'nid';
    public const TYPE_PASSPORT = 'passport';
    public const TYPE_SSC_CERTIFICATE = 'ssc_certificate';
    public const TYPE_HSC_CERTIFICATE = 'hsc_certificate';
    public const TYPE_TRANSFER_CERTIFICATE = 'transfer_certificate';
    public const TYPE_CHARACTER_CERTIFICATE = 'character_certificate';
    public const TYPE_PHOTO = 'photo';
    public const TYPE_SIGNATURE = 'signature';
    public const TYPE_OTHER = 'other';

    /**
     * Get the parent student.
     */
    public function studentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get file URL.
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    /**
     * Check if document is expired.
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->isPast();
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope to get verified documents.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
