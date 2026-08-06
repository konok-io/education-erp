<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Library;

use App\Http\Controllers\Controller;
use App\Services\Library\BookService;
use App\Models\Library\LibraryBook;
use App\Models\Library\LibraryBookCopy;
use App\Models\Library\LibraryCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(
        private readonly BookService $bookService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['category_id', 'status', 'search']);
        $perPage = (int) $request->get('per_page', 20);

        $books = $this->bookService->getBooks($perPage, $filters);

        return response()->json([
            'data' => $books->items(),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:20',
            'category_id' => 'nullable|exists:library_categories,id',
            'publisher_id' => 'nullable|exists:library_publishers,id',
            'author_ids' => 'nullable|array',
            'author_ids.*' => 'exists:library_authors,id',
            'language' => 'nullable|string|max:50',
            'pages' => 'nullable|integer|min:0',
            'publication_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'edition' => 'nullable|string|max:50',
            'purchase_price' => 'nullable|numeric|min:0',
            'replacement_cost' => 'nullable|numeric|min:0',
            'keywords' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'is_digital' => 'nullable|boolean',
            'digital_file' => 'nullable|string',
            'digital_access' => 'nullable|in:public,students,teachers,staff,all',
        ]);

        $book = $this->bookService->createBook($validated);
        return response()->json(['data' => $book], 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $book = LibraryBook::with(['category', 'publisher', 'authors', 'copies'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json(['data' => $book]);
    }

    public function addCopy(Request $request, string $uuid): JsonResponse
    {
        $validated = $request->validate([
            'copy_number' => 'nullable|string|max:20',
            'barcode' => 'nullable|string|max:100',
            'shelf_id' => 'nullable|exists:library_shelves,id',
            'rack_id' => 'nullable|exists:library_racks,id',
            'condition' => 'nullable|in:new,good,fair,poor,damaged',
            'purchase_date' => 'nullable|date',
        ]);

        $copy = $this->bookService->addCopy($uuid, $validated);
        return response()->json(['data' => $copy], 201);
    }

    public function getCategories(): JsonResponse
    {
        $categories = LibraryCategory::where('is_active', true)
            ->with('children')
            ->whereNull('parent_id')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function getStats(): JsonResponse
    {
        $stats = $this->bookService->getBookStats();
        return response()->json(['data' => $stats]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $books = $this->bookService->searchBooks($query);
        return response()->json([
            'data' => $books->items(),
            'meta' => [
                'total' => $books->total(),
            ],
        ]);
    }
}
