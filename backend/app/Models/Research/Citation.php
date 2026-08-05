<?php

declare(strict_types=1);

namespace App\Models\Research;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Citation extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'citations';

    protected $fillable = [
        'uuid', 'publication_id', 'cited_doi', 'cited_title', 'citing_source',
        'source_name', 'citation_url', 'citation_year', 'cited_date',
    ];

    protected $casts = [
        'citation_year' => 'integer',
        'cited_date' => 'date',
    ];

    // ===================== CITING SOURCES =====================
    public const SOURCE_GOOGLE_SCHOLAR = 'google_scholar';
    public const SOURCE_SCOPUS = 'scopus';
    public const SOURCE_WOS = 'web_of_science';
    public const SOURCE_CROSSREF = 'crossref';
    public const SOURCE_MANUAL = 'manual';

    // ===================== RELATIONSHIPS =====================

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    // ===================== METHODS =====================

    public static function citingSources(): array
    {
        return [
            self::SOURCE_GOOGLE_SCHOLAR => 'Google Scholar',
            self::SOURCE_SCOPUS => 'Scopus',
            self::SOURCE_WOS => 'Web of Science',
            self::SOURCE_CROSSREF => 'CrossRef',
            self::SOURCE_MANUAL => 'Manual',
        ];
    }
}
