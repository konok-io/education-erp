<?php

declare(strict_types=1);

namespace App\DTO\Research;

use App\Enums\Research\PublicationStatus;
use Illuminate\Http\Request;

final class PublicationDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $title,
        public readonly string $author_uuid,
        public readonly ?string $co_authors,
        public readonly string $publication_type,
        public readonly ?string $journal_name,
        public readonly ?string $conference_name,
        public readonly ?string $isbn,
        public readonly ?string $doi,
        public readonly ?string $publisher,
        public readonly ?int $volume,
        public readonly ?int $issue,
        public readonly ?int $pages,
        public readonly ?\DateTimeInterface $published_date,
        public readonly ?string $url,
        public readonly PublicationStatus $status = PublicationStatus::DRAFT,
        public readonly ?string $abstract,
        public readonly ?string $keywords,
        public readonly ?float $impact_factor,
        public readonly ?string $indexing,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            title: $request->input('title'),
            author_uuid: $request->input('author_uuid'),
            co_authors: $request->input('co_authors'),
            publication_type: $request->input('publication_type'),
            journal_name: $request->input('journal_name'),
            conference_name: $request->input('conference_name'),
            isbn: $request->input('isbn'),
            doi: $request->input('doi'),
            publisher: $request->input('publisher'),
            volume: $request->input('volume') ? (int) $request->input('volume') : null,
            issue: $request->input('issue') ? (int) $request->input('issue') : null,
            pages: $request->input('pages') ? (int) $request->input('pages') : null,
            published_date: $request->input('published_date') ? new \DateTime($request->input('published_date')) : null,
            url: $request->input('url'),
            status: PublicationStatus::tryFrom($request->input('status', 'draft')) ?? PublicationStatus::DRAFT,
            abstract: $request->input('abstract'),
            keywords: $request->input('keywords'),
            impact_factor: $request->input('impact_factor') ? (float) $request->input('impact_factor') : null,
            indexing: $request->input('indexing'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'author_uuid' => $this->author_uuid,
            'co_authors' => $this->co_authors,
            'publication_type' => $this->publication_type,
            'journal_name' => $this->journal_name,
            'conference_name' => $this->conference_name,
            'isbn' => $this->isbn,
            'doi' => $this->doi,
            'publisher' => $this->publisher,
            'volume' => $this->volume,
            'issue' => $this->issue,
            'pages' => $this->pages,
            'published_date' => $this->published_date?->format('Y-m-d'),
            'url' => $this->url,
            'status' => $this->status->value,
            'abstract' => $this->abstract,
            'keywords' => $this->keywords,
            'impact_factor' => $this->impact_factor,
            'indexing' => $this->indexing,
        ];
    }
}
