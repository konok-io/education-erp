<?php

declare(strict_types=1);

namespace App\Http\Resources\Library;

use App\Http\Resources\BaseResource;

class BookResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'isbn' => $this->isbn,
            'title' => $this->title,
            'title_bn' => $this->title_bn,
            'subtitle' => $this->subtitle,
            'edition' => $this->edition,
            'language' => $this->language,
            'description' => $this->description,
            'publication_year' => $this->publication_year,
            'pages' => $this->pages,
            'price' => $this->price,
            'currency' => $this->currency,
            'keywords' => $this->keywords,
            'cover_image' => $this->cover_image,
            'is_digital' => $this->is_digital,
            'is_reference_only' => $this->is_reference_only,
            'total_copies' => $this->total_copies,
            'available_copies' => $this->available_copies,
            'is_active' => $this->is_active,
            
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category?->uuid,
                'name' => $this->category?->name,
            ]),
            
            'subject' => $this->whenLoaded('subject', fn() => [
                'id' => $this->subject?->uuid,
                'name' => $this->subject?->name,
            ]),
            
            'authors' => $this->whenLoaded('authors', fn() => $this->authors->map(fn($a) => [
                'id' => $a->uuid,
                'name' => $a->name,
                'is_primary' => $a->pivot?->is_primary ?? false,
            ])),
            
            'publishers' => $this->whenLoaded('publishers', fn() => $this->publishers->map(fn($p) => [
                'id' => $p->uuid,
                'name' => $p->name,
            ])),
            
            'copies' => $this->whenLoaded('copies', fn() => $this->copies->map(fn($c) => [
                'id' => $c->uuid,
                'accession_number' => $c->accession_number,
                'barcode' => $c->barcode,
                'status' => $c->status,
                'condition' => $c->condition,
            ])),
            
            'author_names' => $this->when(!$this->relationLoaded('authors'), fn() => $this->getAuthorNames()),
            
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
