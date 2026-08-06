<?php

declare(strict_types=1);

namespace App\DTO\Research;

use App\Enums\Research\ResearchStatus;
use Illuminate\Http\Request;

final class ResearchDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $title,
        public readonly ?string $title_bn,
        public readonly string $researcher_uuid,
        public readonly ?string $co_researchers,
        public readonly ?string $abstract,
        public readonly ?string $methodology,
        public readonly ?string $keywords,
        public readonly ?string $research_area,
        public readonly ?string $institution,
        public readonly ?float $funding_amount,
        public readonly ?string $funding_source,
        public readonly ?\DateTimeInterface $start_date,
        public readonly ?\DateTimeInterface $expected_end_date,
        public readonly ?\DateTimeInterface $actual_end_date,
        public readonly ResearchStatus $status = ResearchStatus::DRAFT,
        public readonly ?string $document_path,
        public readonly ?string $ethical_approval,
        public readonly ?string $irb_number,
        public readonly ?string $notes,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            title: $request->input('title'),
            title_bn: $request->input('title_bn'),
            researcher_uuid: $request->input('researcher_uuid'),
            co_researchers: $request->input('co_researchers'),
            abstract: $request->input('abstract'),
            methodology: $request->input('methodology'),
            keywords: $request->input('keywords'),
            research_area: $request->input('research_area'),
            institution: $request->input('institution'),
            funding_amount: $request->input('funding_amount') ? (float) $request->input('funding_amount') : null,
            funding_source: $request->input('funding_source'),
            start_date: $request->input('start_date') ? new \DateTime($request->input('start_date')) : null,
            expected_end_date: $request->input('expected_end_date') ? new \DateTime($request->input('expected_end_date')) : null,
            actual_end_date: $request->input('actual_end_date') ? new \DateTime($request->input('actual_end_date')) : null,
            status: ResearchStatus::tryFrom($request->input('status', 'draft')) ?? ResearchStatus::DRAFT,
            document_path: $request->input('document_path'),
            ethical_approval: $request->input('ethical_approval'),
            irb_number: $request->input('irb_number'),
            notes: $request->input('notes'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'title_bn' => $this->title_bn,
            'researcher_uuid' => $this->researcher_uuid,
            'co_researchers' => $this->co_researchers,
            'abstract' => $this->abstract,
            'methodology' => $this->methodology,
            'keywords' => $this->keywords,
            'research_area' => $this->research_area,
            'institution' => $this->institution,
            'funding_amount' => $this->funding_amount,
            'funding_source' => $this->funding_source,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'expected_end_date' => $this->expected_end_date?->format('Y-m-d'),
            'actual_end_date' => $this->actual_end_date?->format('Y-m-d'),
            'status' => $this->status->value,
            'document_path' => $this->document_path,
            'ethical_approval' => $this->ethical_approval,
            'irb_number' => $this->irb_number,
            'notes' => $this->notes,
        ];
    }
}
