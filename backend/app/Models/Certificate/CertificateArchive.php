<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateArchive extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'certificate_archive';

    protected $fillable = [
        'uuid',
        'document_type',
        'document_number',
        'student_id',
        'student_name',
        'student_roll',
        'document_category',
        'file_path',
        'file_type',
        'file_size',
        'file_hash',
        'storage_type',
        'cloud_url',
        'description',
        'metadata',
        'version',
        'status',
        'uploaded_by',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    // ===================== STATUS =====================
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';
    public const STATUS_DELETED = 'deleted';

    // ===================== STORAGE TYPES =====================
    public const STORAGE_LOCAL = 'local';
    public const STORAGE_CLOUD = 'cloud';
    public const STORAGE_BOTH = 'both';

    // ===================== RELATIONSHIPS =====================

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // ===================== METHODS =====================

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_DELETED => 'Deleted',
        ];
    }

    public static function storageTypes(): array
    {
        return [
            self::STORAGE_LOCAL => 'Local Storage',
            self::STORAGE_CLOUD => 'Cloud Storage',
            self::STORAGE_BOTH => 'Both',
        ];
    }

    public function archive(): void
    {
        $this->update(['status' => self::STATUS_ARCHIVED]);
    }
}
