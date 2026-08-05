<?php

declare(strict_types=1);

namespace App\Http\Resources\Research;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'publication_code' => $this->publication_code,
            'title' => $this->title,
            'abstract' => $this->abstract,
            'publication_type' => $this->publication_type,
            'publication_type_label' => \App\Models\Research\Publication::publicationTypes()[$this->publication_type] ?? $this->publication_type,
            'journal_name' => $this->journal_name,
            'journal_issn' => $this->journal_issn,
            'publisher' => $this->publisher,
            'volume' => $this->volume,
            'issue' => $this->issue,
            'pages' => $this->pages,
            'doi' => $this->doi,
            'url' => $this->url,
            'publication_year' => $this->publication_year,
            'publication_date' => $this->publication_date,
            'authors' => $this->authors,
            'keywords' => $this->keywords,
            'co_authors' => $this->co_authors,
            'orcid' => $this->orcid,
            'citation_count' => $this->citation_count,
            'impact_factor' => $this->impact_factor,
            'quartile' => $this->quartile,
            'status' => $this->status,
            'status_label' => \App\Models\Research\Publication::statuses()[$this->status] ?? $this->status,
            'conference_name' => $this->conference_name,
            'conference_venue' => $this->conference_venue,
            'conference_date' => $this->conference_date,
            'isbn' => $this->isbn,
            'book_publisher' => $this->book_publisher,
            'pdf_document' => $this->pdf_document,
            'is_open_access' => $this->is_open_access,
            'is_peer_reviewed' => $this->is_peer_reviewed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
