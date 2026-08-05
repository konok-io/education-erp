<?php

declare(strict_types=1);

namespace App\Http\Resources\Library;

use App\Http\Resources\BaseResource;

class DigitalBookResource extends BaseResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'title_bn' => $this->title_bn,
            'isbn' => $this->isbn,
            'author_name' => $this->author_name,
            'publisher' => $this->publisher,
            'publication_year' => $this->publication_year,
            'language' => $this->language,
            'file_type' => $this->file_type,
            'file_type_label' => $this->file_type ? \App\Models\Library\DigitalBook::fileTypes()[$this->file_type] ?? $this->file_type : null,
            'file_size' => $this->file_size,
            'file_size_formatted' => $this->getFileSizeFormatted(),
            'page_count' => $this->page_count,
            'access_type' => $this->access_type,
            'access_type_label' => $this->access_type ? \App\Models\Library\DigitalBook::accessTypes()[$this->access_type] ?? $this->access_type : null,
            'download_permission' => $this->download_permission,
            'can_download' => $this->canDownload(),
            'view_count' => $this->view_count,
            'download_count' => $this->download_count,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category?->uuid,
                'name' => $this->category?->name,
            ]),
            
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
