<?php

declare(strict_types=1);

namespace App\DTO\Library;

use App\Enums\Library\BookStatus;
use App\Enums\Library\BookType;
use Illuminate\Http\Request;

final class BookDTO
{
    public function __construct(
        public readonly ?string $uuid,
        public readonly string $book_code,
        public readonly string $title,
        public readonly string $isbn,
        public readonly BookType $type = BookType::BOOK,
        public readonly ?string $author,
        public readonly ?string $publisher,
        public readonly ?string $edition,
        public readonly ?int $total_pages,
        public readonly ?int $year_of_publication,
        public readonly ?string $language,
        public readonly ?string $category_uuid,
        public readonly ?string $subcategory_uuid,
        public readonly ?float $price,
        public readonly ?string $shelf_location,
        public readonly ?int $quantity,
        public readonly ?int $available_quantity,
        public readonly BookStatus $status = BookStatus::AVAILABLE,
        public readonly ?string $description,
        public readonly ?string $cover_image,
        public readonly ?string $table_of_contents,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            uuid: $request->input('uuid'),
            book_code: $request->input('book_code'),
            title: $request->input('title'),
            isbn: $request->input('isbn'),
            type: BookType::tryFrom($request->input('type', 'book')) ?? BookType::BOOK,
            author: $request->input('author'),
            publisher: $request->input('publisher'),
            edition: $request->input('edition'),
            total_pages: $request->input('total_pages') ? (int) $request->input('total_pages') : null,
            year_of_publication: $request->input('year_of_publication') ? (int) $request->input('year_of_publication') : null,
            language: $request->input('language'),
            category_uuid: $request->input('category_uuid'),
            subcategory_uuid: $request->input('subcategory_uuid'),
            price: $request->input('price') ? (float) $request->input('price') : null,
            shelf_location: $request->input('shelf_location'),
            quantity: $request->input('quantity') ? (int) $request->input('quantity') : null,
            available_quantity: $request->input('available_quantity') ? (int) $request->input('available_quantity') : null,
            status: BookStatus::tryFrom($request->input('status', 'available')) ?? BookStatus::AVAILABLE,
            description: $request->input('description'),
            cover_image: $request->input('cover_image'),
            table_of_contents: $request->input('table_of_contents'),
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'book_code' => $this->book_code,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'type' => $this->type->value,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'edition' => $this->edition,
            'total_pages' => $this->total_pages,
            'year_of_publication' => $this->year_of_publication,
            'language' => $this->language,
            'category_uuid' => $this->category_uuid,
            'subcategory_uuid' => $this->subcategory_uuid,
            'price' => $this->price,
            'shelf_location' => $this->shelf_location,
            'quantity' => $this->quantity,
            'available_quantity' => $this->available_quantity,
            'status' => $this->status->value,
            'description' => $this->description,
            'cover_image' => $this->cover_image,
            'table_of_contents' => $this->table_of_contents,
        ];
    }
}
