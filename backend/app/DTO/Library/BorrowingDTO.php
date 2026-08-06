<?php

declare(strict_types=1);

namespace App\DTO\Library;

use App\Enums\Library\BorrowingStatus;
use Illuminate\Http\Request;

final class BorrowingDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $borrow_id,
        public readonly string $book_uuid,
        public readonly string $user_uuid,
        public readonly string $user_type,
        public readonly \DateTimeInterface $borrow_date,
        public readonly \DateTimeInterface $due_date,
        public readonly ?\DateTimeInterface $return_date,
        public readonly BorrowingStatus $status = BorrowingStatus::BORROWED,
        public readonly ?float $fine_amount,
        public readonly ?string $fine_paid_date,
        public readonly ?string $remarks,
        public readonly ?string $renewed_count,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            borrow_id: $request->input('borrow_id'),
            book_uuid: $request->input('book_uuid'),
            user_uuid: $request->input('user_uuid'),
            user_type: $request->input('user_type'),
            borrow_date: new \DateTime($request->input('borrow_date')),
            due_date: new \DateTime($request->input('due_date')),
            return_date: $request->input('return_date') ? new \DateTime($request->input('return_date')) : null,
            status: BorrowingStatus::tryFrom($request->input('status', 'borrowed')) ?? BorrowingStatus::BORROWED,
            fine_amount: $request->input('fine_amount') ? (float) $request->input('fine_amount') : null,
            fine_paid_date: $request->input('fine_paid_date'),
            remarks: $request->input('remarks'),
            renewed_count: $request->input('renewed_count'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'borrow_id' => $this->borrow_id,
            'book_uuid' => $this->book_uuid,
            'user_uuid' => $this->user_uuid,
            'user_type' => $this->user_type,
            'borrow_date' => $this->borrow_date->format('Y-m-d'),
            'due_date' => $this->due_date->format('Y-m-d'),
            'return_date' => $this->return_date?->format('Y-m-d'),
            'status' => $this->status->value,
            'fine_amount' => $this->fine_amount,
            'fine_paid_date' => $this->fine_paid_date,
            'remarks' => $this->remarks,
            'renewed_count' => $this->renewed_count,
        ];
    }
}
