<?php

declare(strict_types=1);

namespace App\Services\Library;

use App\Models\Library\LibraryBook;
use App\Models\Library\LibraryBookCopy;
use App\Models\Library\LibraryAuthor;
use App\Models\Library\LibraryPublisher;
use App\Models\Library\LibraryCategory;
use App\Models\Library\LibraryFineRule;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookService
{
    public function getBooks(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = LibraryBook::with(['category', 'publisher', 'authors', 'copies']);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%")
                    ->orWhere('book_code', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createBook(array $data): LibraryBook
    {
        return DB::transaction(function () use ($data) {
            $book = LibraryBook::create([
                'uuid' => (string) Str::uuid(),
                'book_code' => LibraryBook::generateBookCode(),
                'isbn' => $data['isbn'] ?? null,
                'title' => $data['title'],
                'title_bn' => $data['title_bn'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'publisher_id' => $data['publisher_id'] ?? null,
                'description' => $data['description'] ?? null,
                'language' => $data['language'] ?? 'English',
                'pages' => $data['pages'] ?? 0,
                'publication_year' => $data['publication_year'] ?? null,
                'edition' => $data['edition'] ?? null,
                'purchase_price' => $data['purchase_price'] ?? 0,
                'replacement_cost' => $data['replacement_cost'] ?? 0,
                'keywords' => $data['keywords'] ?? null,
                'cover_image' => $data['cover_image'] ?? null,
                'status' => LibraryBook::STATUS_ACTIVE,
                'is_digital' => $data['is_digital'] ?? false,
                'digital_file' => $data['digital_file'] ?? null,
                'digital_size' => $data['digital_size'] ?? null,
                'digital_access' => $data['digital_access'] ?? 'all',
            ]);

            // Add authors
            if (!empty($data['author_ids'])) {
                foreach ($data['author_ids'] as $index => $authorId) {
                    $book->authors()->attach($authorId, [
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            return $book->load(['category', 'publisher', 'authors']);
        });
    }

    public function addCopy(string $uuid, array $data): LibraryBookCopy
    {
        $book = LibraryBook::where('uuid', $uuid)->firstOrFail();

        return LibraryBookCopy::create([
            'uuid' => (string) Str::uuid(),
            'accession_no' => LibraryBookCopy::generateAccessionNo(),
            'book_id' => $book->id,
            'copy_number' => $data['copy_number'] ?? null,
            'barcode' => $data['barcode'] ?? null,
            'qr_code' => $data['qr_code'] ?? null,
            'shelf_id' => $data['shelf_id'] ?? null,
            'rack_id' => $data['rack_id'] ?? null,
            'condition' => $data['condition'] ?? LibraryBookCopy::CONDITION_NEW,
            'status' => LibraryBookCopy::STATUS_AVAILABLE,
            'purchase_date' => $data['purchase_date'] ?? null,
        ]);
    }

    public function getBookStats(): array
    {
        return [
            'total_books' => LibraryBook::count(),
            'total_copies' => LibraryBookCopy::count(),
            'available_copies' => LibraryBookCopy::where('status', LibraryBookCopy::STATUS_AVAILABLE)->count(),
            'issued_copies' => LibraryBookCopy::where('status', LibraryBookCopy::STATUS_ISSUED)->count(),
            'reserved_copies' => LibraryBookCopy::where('status', LibraryBookCopy::STATUS_RESERVED)->count(),
            'lost_copies' => LibraryBookCopy::where('status', LibraryBookCopy::STATUS_LOST)->count(),
            'damaged_copies' => LibraryBookCopy::where('status', LibraryBookCopy::STATUS_DAMAGED)->count(),
            'digital_books' => LibraryBook::where('is_digital', true)->count(),
        ];
    }

    public function searchBooks(string $query): LengthAwarePaginator
    {
        return LibraryBook::with(['category', 'authors'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('isbn', 'like', "%{$query}%")
                    ->orWhere('book_code', 'like', "%{$query}%")
                    ->orWhereHas('authors', function ($aq) use ($query) {
                        $aq->where('name', 'like', "%{$query}%");
                    });
            })
            ->where('status', LibraryBook::STATUS_ACTIVE)
            ->paginate(20);
    }

    // ===================== AUTHOR METHODS =====================

    public function createAuthor(array $data): LibraryAuthor
    {
        return LibraryAuthor::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'biography' => $data['biography'] ?? null,
            'country' => $data['country'] ?? null,
            'website' => $data['website'] ?? null,
            'email' => $data['email'] ?? null,
            'photo' => $data['photo'] ?? null,
            'status' => 'active',
        ]);
    }

    // ===================== PUBLISHER METHODS =====================

    public function createPublisher(array $data): LibraryPublisher
    {
        return LibraryPublisher::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'active',
        ]);
    }
}
