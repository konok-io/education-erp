<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publication extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'publications';

    protected $fillable = [
        'uuid', 'publication_code', 'title', 'abstract', 'publication_type',
        'project_id', 'journal_name', 'journal_issn', 'publisher', 'volume',
        'issue', 'pages', 'doi', 'url', 'publication_year', 'publication_date',
        'authors', 'keywords', 'co_authors', 'orcid', 'scopus_id', 'wos_id',
        'google_scholar_id', 'citation_count', 'impact_factor', 'quartile',
        'status', 'conference_name', 'conference_venue', 'conference_date',
        'isbn', 'book_publisher', 'book_chapters', 'pdf_document', 'is_open_access',
        'is_peer_reviewed', 'created_by',
    ];

    protected $casts = [
        'authors' => 'array',
        'keywords' => 'array',
        'co_authors' => 'array',
        'impact_factor' => 'decimal:2',
        'citation_count' => 'integer',
        'publication_date' => 'date',
        'conference_date' => 'date',
        'is_open_access' => 'boolean',
        'is_peer_reviewed' => 'boolean',
    ];

    // ===================== PUBLICATION TYPES =====================
    public const TYPE_JOURNAL = 'journal_article';
    public const TYPE_CONFERENCE = 'conference_paper';
    public const TYPE_BOOK = 'book';
    public const TYPE_BOOK_CHAPTER = 'book_chapter';
    public const TYPE_MAGAZINE = 'magazine';
    public const TYPE_TECHNICAL_REPORT = 'technical_report';
    public const TYPE_WORKING_PAPER = 'working_paper';

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_REVISED = 'revised';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';

    // ===================== RELATIONSHIPS =====================

    public function project(): BelongsTo
    {
        return $this->belongsTo(ResearchProject::class, 'project_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function citations(): HasMany
    {
        return $this->hasMany(Citation::class, 'publication_id');
    }

    // ===================== SCOPES =====================

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    // ===================== METHODS =====================

    public static function generatePublicationCode(): string
    {
        $prefix = 'PUB';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s-%s-%06d', $prefix, $year, $count);
    }

    public static function publicationTypes(): array
    {
        return [
            self::TYPE_JOURNAL => 'Journal Article',
            self::TYPE_CONFERENCE => 'Conference Paper',
            self::TYPE_BOOK => 'Book',
            self::TYPE_BOOK_CHAPTER => 'Book Chapter',
            self::TYPE_MAGAZINE => 'Magazine Article',
            self::TYPE_TECHNICAL_REPORT => 'Technical Report',
            self::TYPE_WORKING_PAPER => 'Working Paper',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_REVISED => 'Revised',
            self::STATUS_ACCEPTED => 'Accepted',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function incrementCitations(): void
    {
        $this->increment('citation_count');
    }
}
