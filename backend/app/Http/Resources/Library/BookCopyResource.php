<?php

declare(strict_types=1);

namespace App\Http\Resources\Library;

use App\Http\Resources\BaseResource;

class BookCopyResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'accession_number' => $this->accession_number,
            'barcode' => $this->barcode,
            'qr_code' => $this->qr_code,
            'condition' => $this->condition,
            'status' => $this->status,
            'acquisition_date' => $this->acquisition_date?->toDateString(),
            'purchase_price' => $this->purchase_price,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            
            'book' => $this->whenLoaded('book', fn() => [
                'id' => $this->book?->uuid,
                'title' => $this->book?->title,
                'isbn' => $this->book?->isbn,
            ]),
            
            'rack' => $this->whenLoaded('rack', fn() => [
                'id' => $this->rack?->uuid,
                'name' => $this->rack?->name,
                'code' => $this->rack?->code,
                'shelf' => $this->rack?->shelf?->name,
            ]),
            
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
