<?php

declare(strict_types=1);

namespace App\Http\Resources\Library;

use App\Http\Resources\BaseResource;

class BookIssueResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'issue_no' => $this->issue_no,
            'issue_date' => $this->issue_date?->toDateString(),
            'due_date' => $this->due_date?->toDateString(),
            'return_date' => $this->return_date?->toDateString(),
            'status' => $this->status,
            'renewal_count' => $this->renewal_count,
            'max_renewals' => $this->max_renewals,
            'is_overdue' => $this->isOverdue(),
            'overdue_days' => $this->getOverdueDays(),
            'notes' => $this->notes,
            
            'member' => $this->whenLoaded('member', fn() => [
                'id' => $this->member?->uuid,
                'member_no' => $this->member?->member_no,
                'name' => $this->member?->name,
            ]),
            
            'book_copy' => $this->whenLoaded('bookCopy', fn() => [
                'id' => $this->bookCopy?->uuid,
                'accession_number' => $this->bookCopy?->accession_number,
                'barcode' => $this->bookCopy?->barcode,
                'book' => $this->bookCopy?->book ? [
                    'id' => $this->bookCopy->book->uuid,
                    'title' => $this->bookCopy->book->title,
                    'isbn' => $this->bookCopy->book->isbn,
                ] : null,
            ]),
            
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
