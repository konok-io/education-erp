<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalBook extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'digital_books';

    protected $fillable = [
        'uuid',
        'book_id',
        'title',
        'title_bn',
        'category_id',
        'file_type',
        'file_path',
        'file_size',
        'page_count',
        'isbn',
        'author_name',
        'publisher',
        'publication_year',
        'language',
        'access_type',
        'download_permission',
        'view_count',
        'download_count',
        'description',
        'cover_image',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'page_count' => 'integer',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== FILE TYPES =====================
    public const TYPE_PDF = 'pdf';
    public const TYPE_EPUB = 'epub';
    public const TYPE_DOCX = 'docx';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_VIDEO = 'video';

    // ===================== ACCESS TYPES =====================
    public const ACCESS_PUBLIC = 'public';
    public const ACCESS_MEMBERS = 'members';
    public const ACCESS_PREMIUM = 'premium';
    public const ACCESS_RESTRICTED = 'restricted';

    // ===================== DOWNLOAD PERMISSIONS =====================
    public const DOWNLOAD_ALLOWED = 'allowed';
    public const DOWNLOAD_NOT_ALLOWED = 'not_allowed';
    public const DOWNLOAD_PREMIUM = 'premium';

    // ===================== RELATIONSHIPS =====================

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('file_type', $type);
    }

    public function scopeByAccess($query, $access)
    {
        return $query->where('access_type', $access);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('author_name', 'like', "%{$term}%")
              ->orWhere('isbn', 'like', "%{$term}%");
        });
    }

    // ===================== METHODS =====================

    public static function fileTypes(): array
    {
        return [
            self::TYPE_PDF => 'PDF',
            self::TYPE_EPUB => 'EPUB',
            self::TYPE_DOCX => 'Word Document',
            self::TYPE_AUDIO => 'Audio Book',
            self::TYPE_VIDEO => 'Video Lecture',
        ];
    }

    public static function accessTypes(): array
    {
        return [
            self::ACCESS_PUBLIC => 'Public',
            self::ACCESS_MEMBERS => 'Members Only',
            self::ACCESS_PREMIUM => 'Premium',
            self::ACCESS_RESTRICTED => 'Restricted',
        ];
    }

    public function isPdf(): bool
    {
        return $this->file_type === self::TYPE_PDF;
    }

    public function isEpub(): bool
    {
        return $this->file_type === self::TYPE_EPUB;
    }

    public function canDownload(): bool
    {
        return $this->download_permission === self::DOWNLOAD_ALLOWED;
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = (int) $this->file_size;
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' B';
    }
}
