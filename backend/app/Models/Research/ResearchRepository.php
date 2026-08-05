<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResearchRepository extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'research_repository';

    protected $fillable = [
        'uuid', 'document_code', 'title', 'description', 'document_type',
        'project_id', 'publication_id', 'file_path', 'file_name', 'file_type',
        'file_size', 'file_hash', 'access_type', 'license', 'metadata',
        'version', 'doi', 'is_featured', 'is_active', 'uploaded_by',
        'contributor', 'published_date',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'published_date' => 'date',
    ];

    // ===================== DOCUMENT TYPES =====================
    public const TYPE_PDF = 'pdf';
    public const TYPE_DATASET = 'dataset';
    public const TYPE_IMAGE = 'image';
    public const TYPE_CODE = 'source_code';
    public const TYPE_PRESENTATION = 'presentation';
    public const TYPE_POSTER = 'poster';
    public const TYPE_SUPPLEMENTARY = 'supplementary';

    // ===================== ACCESS TYPES =====================
    public const ACCESS_PUBLIC = 'public';
    public const ACCESS_PRIVATE = 'private';
    public const ACCESS_INSTITUTION = 'institution';
    public const ACCESS_TEAM = 'team_only';

    // ===================== RELATIONSHIPS =====================

    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    // ===================== METHODS =====================

    public static function generateDocumentCode(): string
    {
        $prefix = 'REPO';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function documentTypes(): array
    {
        return [
            self::TYPE_PDF => 'PDF Document',
            self::TYPE_DATASET => 'Dataset',
            self::TYPE_IMAGE => 'Images',
            self::TYPE_CODE => 'Source Code',
            self::TYPE_PRESENTATION => 'Presentation',
            self::TYPE_POSTER => 'Poster',
            self::TYPE_SUPPLEMENTARY => 'Supplementary Files',
        ];
    }

    public static function accessTypes(): array
    {
        return [
            self::ACCESS_PUBLIC => 'Public',
            self::ACCESS_PRIVATE => 'Private',
            self::ACCESS_INSTITUTION => 'Institution Only',
            self::ACCESS_TEAM => 'Research Team Only',
        ];
    }
}
