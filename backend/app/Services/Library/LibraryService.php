<?php

declare(strict_types=1);

namespace App\Services\Library;

use App\Models\Library\Book;
use App\Models\Library\BookCopy;
use App\Models\Library\BookCategory;
use App\Models\Library\BookIssue;
use App\Models\Library\BookReservation;
use App\Models\Library\LibraryMember;
use App\Models\Library\LibraryFine;
use App\Models\Library\DigitalBook;
use App\Models\Library\Author;
use App\Models\Library\Publisher;
use App\Models\Library\Subject;
use App\Models\Library\LibraryShelf;
use App\Models\Library\LibraryRack;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LibraryService
{
    // ===================== BOOK MANAGEMENT =====================

    public function getBooks(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Book::with(['category', 'authors', 'copies']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['author_id'])) {
            $query->whereHas('authors', fn($q) => $q->where('author_id', $filters['author_id']));
        }

        if (!empty($filters['availability'])) {
            $query->where('available_copies', '>', 0);
        }

        return $query->orderBy('title')->paginate($perPage);
    }

    public function createBook(array $data): Book
    {
        return DB::transaction(function () use ($data) {
            $book = Book::create([
                'uuid' => (string) Str::uuid(),
                'isbn' => $data['isbn'] ?? null,
                'title' => $data['title'],
                'title_bn' => $data['title_bn'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'edition' => $data['edition'] ?? null,
                'language' => $data['language'] ?? 'English',
                'category_id' => $data['category_id'] ?? null,
                'subject_id' => $data['subject_id'] ?? null,
                'description' => $data['description'] ?? null,
                'publication_year' => $data['publication_year'] ?? null,
                'pages' => $data['pages'] ?? null,
                'price' => $data['price'] ?? null,
                'currency' => $data['currency'] ?? 'BDT',
                'keywords' => $data['keywords'] ?? null,
                'is_digital' => $data['is_digital'] ?? false,
                'is_reference_only' => $data['is_reference_only'] ?? false,
                'total_copies' => $data['total_copies'] ?? 1,
                'available_copies' => $data['total_copies'] ?? 1,
                'is_active' => true,
            ]);

            // Attach authors
            if (!empty($data['author_ids'])) {
                $authors = [];
                foreach ($data['author_ids'] as $index => $authorId) {
                    $authors[$authorId] = ['is_primary' => $index === 0];
                }
                $book->authors()->attach($authors);
            }

            // Attach publishers
            if (!empty($data['publisher_ids'])) {
                $book->publishers()->attach($data['publisher_ids']);
            }

            // Create copies
            $copyCount = $data['total_copies'] ?? 1;
            for ($i = 0; $i < $copyCount; $i++) {
                $this->createBookCopy($book->id, $data['rack_id'] ?? null);
            }

            return $book->load(['category', 'authors', 'publishers', 'copies']);
        });
    }

    public function createBookCopy(int $bookId, ?int $rackId = null): BookCopy
    {
        return BookCopy::create([
            'uuid' => (string) Str::uuid(),
            'book_id' => $bookId,
            'rack_id' => $rackId,
            'accession_number' => BookCopy::generateAccessionNumber(),
            'barcode' => BookCopy::generateBarcode(),
            'qr_code' => BookCopy::generateQRCode(),
            'condition' => BookCopy::CONDITION_GOOD,
            'status' => BookCopy::STATUS_AVAILABLE,
            'acquisition_date' => now(),
            'is_active' => true,
        ]);
    }

    public function updateBook(string $uuid, array $data): Book
    {
        $book = Book::where('uuid', $uuid)->firstOrFail();
        
        $book->update($data);

        if (isset($data['author_ids'])) {
            $authors = [];
            foreach ($data['author_ids'] as $index => $authorId) {
                $authors[$authorId] = ['is_primary' => $index === 0];
            }
            $book->authors()->sync($authors);
        }

        if (isset($data['publisher_ids'])) {
            $book->publishers()->sync($data['publisher_ids']);
        }

        return $book->load(['category', 'authors', 'publishers', 'copies']);
    }

    // ===================== ISSUE & RETURN =====================

    public function issueBook(int $memberId, int $bookCopyId, int $userId): BookIssue
    {
        return DB::transaction(function () use ($memberId, $bookCopyId, $userId) {
            $member = LibraryMember::findOrFail($memberId);
            $bookCopy = BookCopy::with('book')->findOrFail($bookCopyId);

            // Validation checks
            if (!$member->canIssueMoreBooks()) {
                throw new \Exception('Member has reached maximum book limit');
            }

            if ($member->hasUnpaidFines()) {
                throw new \Exception('Member has unpaid fines');
            }

            if (!$bookCopy->isAvailable()) {
                throw new \Exception('Book copy is not available');
            }

            // Create issue
            $issue = BookIssue::create([
                'uuid' => (string) Str::uuid(),
                'issue_no' => BookIssue::generateIssueNo(),
                'member_id' => $memberId,
                'book_copy_id' => $bookCopyId,
                'issue_date' => now(),
                'due_date' => now()->addDays($member->max_days),
                'status' => BookIssue::STATUS_ISSUED,
                'renewal_count' => 0,
                'max_renewals' => 2,
                'issued_by' => $userId,
            ]);

            // Update book copy and book status
            $bookCopy->markAsIssued();
            $bookCopy->book->decrementAvailableCopies();

            return $issue->load(['member', 'bookCopy.book']);
        });
    }

    public function returnBook(int $issueId, int $userId): array
    {
        return DB::transaction(function () use ($issueId, $userId) {
            $issue = BookIssue::with(['member', 'bookCopy.book'])->findOrFail($issueId);

            if ($issue->status === BookIssue::STATUS_RETURNED) {
                throw new \Exception('Book already returned');
            }

            // Calculate fine if overdue
            $fineAmount = 0;
            if ($issue->isOverdue()) {
                $fineAmount = $issue->calculateFine($issue->member->fine_rate);
                
                $fine = LibraryFine::create([
                    'uuid' => (string) Str::uuid(),
                    'fine_no' => LibraryFine::generateFineNo(),
                    'member_id' => $issue->member_id,
                    'issue_id' => $issueId,
                    'fine_type' => LibraryFine::TYPE_LATE_RETURN,
                    'reason' => 'Late return: ' . $issue->getOverdueDays() . ' days overdue',
                    'amount' => $fineAmount,
                    'paid_amount' => 0,
                    'waived_amount' => 0,
                    'fine_date' => now(),
                    'due_date' => now()->addDays(7),
                    'status' => LibraryFine::STATUS_PENDING,
                ]);
            }

            // Update issue status
            $issue->markAsReturned();

            // Update book copy and book status
            $issue->bookCopy->markAsAvailable();
            $issue->bookCopy->book->incrementAvailableCopies();

            return [
                'issue' => $issue->load(['member', 'bookCopy.book']),
                'fine' => $fineAmount > 0 ? $fine : null,
                'overdue_days' => $issue->getOverdueDays(),
            ];
        });
    }

    public function renewBook(int $issueId): BookIssue
    {
        $issue = BookIssue::with('member')->findOrFail($issueId);

        if (!$issue->canRenew()) {
            throw new \Exception('Cannot renew this book');
        }

        $issue->renew($issue->member->max_days);

        return $issue->load(['member', 'bookCopy.book']);
    }

    // ===================== RESERVATION =====================

    public function createReservation(int $memberId, int $bookId): BookReservation
    {
        $member = LibraryMember::findOrFail($memberId);
        $book = Book::findOrFail($bookId);

        // Check if book is available
        if ($book->available_copies > 0) {
            throw new \Exception('Book is available, no reservation needed');
        }

        // Check existing reservation
        $existing = BookReservation::where('member_id', $memberId)
            ->where('book_id', $bookId)
            ->whereIn('status', [BookReservation::STATUS_PENDING, BookReservation::STATUS_READY])
            ->first();

        if ($existing) {
            throw new \Exception('Reservation already exists');
        }

        return BookReservation::create([
            'uuid' => (string) Str::uuid(),
            'reservation_no' => BookReservation::generateReservationNo(),
            'member_id' => $memberId,
            'book_id' => $bookId,
            'reservation_date' => now(),
            'expiry_date' => now()->addDays(7),
            'status' => BookReservation::STATUS_PENDING,
            'notify_status' => BookReservation::NOTIFY_PENDING,
        ]);
    }

    public function fulfillReservation(int $reservationId): void
    {
        $reservation = BookReservation::findOrFail($reservationId);
        $reservation->markAsReady();
    }

    // ===================== FINE MANAGEMENT =====================

    public function payFine(string $uuid, float $amount, ?string $method = null): LibraryFine
    {
        $fine = LibraryFine::where('uuid', $uuid)->firstOrFail();
        $fine->pay($amount, $method);
        return $fine;
    }

    public function waiveFine(string $uuid, float $amount): LibraryFine
    {
        $fine = LibraryFine::where('uuid', $uuid)->firstOrFail();
        $fine->waive($amount);
        return $fine;
    }

    // ===================== MEMBER MANAGEMENT =====================

    public function getMembers(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = LibraryMember::with(['issues', 'fines']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('member_no', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['member_type'])) {
            $query->where('member_type', $filters['member_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function createMember(array $data): LibraryMember
    {
        return LibraryMember::create([
            'uuid' => (string) Str::uuid(),
            'member_no' => LibraryMember::generateMemberNo(),
            'member_type' => $data['member_type'],
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'photo' => $data['photo'] ?? null,
            'department' => $data['department'] ?? null,
            'student_id' => $data['student_id'] ?? null,
            'employee_id' => $data['employee_id'] ?? null,
            'joining_date' => $data['joining_date'] ?? now(),
            'expiry_date' => $data['expiry_date'] ?? now()->addYear(),
            'status' => LibraryMember::STATUS_ACTIVE,
            'max_books' => $data['max_books'] ?? 5,
            'max_days' => $data['max_days'] ?? 14,
            'fine_rate' => $data['fine_rate'] ?? 5.00,
            'is_active' => true,
        ]);
    }

    public function getMemberStats(int $memberId): array
    {
        $member = LibraryMember::with(['issues', 'fines', 'reservations'])->findOrFail($memberId);

        return [
            'total_issues' => $member->issues()->count(),
            'current_issues' => $member->issues()->whereIn('status', ['issued', 'overdue'])->count(),
            'returned_books' => $member->issues()->where('status', 'returned')->count(),
            'overdue_books' => $member->issues()->overdue()->count(),
            'total_fines' => $member->fines()->sum('amount'),
            'unpaid_fines' => $member->fines()->whereIn('status', ['pending', 'partial'])->sum('amount'),
            'active_reservations' => $member->reservations()->active()->count(),
        ];
    }

    // ===================== OPAC SEARCH =====================

    public function opacSearch(array $filters): LengthAwarePaginator
    {
        $query = Book::with(['category', 'authors'])
            ->active()
            ->where('available_copies', '>', 0);

        if (!empty($filters['q'])) {
            $searchTerm = $filters['q'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('isbn', 'like', "%{$searchTerm}%")
                  ->orWhere('keywords', 'like', "%{$searchTerm}%")
                  ->orWhereHas('authors', fn($aq) => $aq->where('name', 'like', "%{$searchTerm}%"));
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['author_id'])) {
            $query->whereHas('authors', fn($q) => $q->where('author_id', $filters['author_id']));
        }

        if (!empty($filters['publication_year'])) {
            $query->where('publication_year', $filters['publication_year']);
        }

        if (!empty($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        return $query->orderBy('title')->paginate($filters['per_page'] ?? 20);
    }

    // ===================== DASHBOARD =====================

    public function getDashboard(): array
    {
        $totalBooks = Book::active()->sum('total_copies');
        $availableBooks = Book::active()->sum('available_copies');
        $issuedBooks = $totalBooks - $availableBooks;
        
        return [
            'total_books' => $totalBooks,
            'available_books' => $availableBooks,
            'issued_books' => $issuedBooks,
            'total_members' => LibraryMember::active()->count(),
            'active_issues' => BookIssue::active()->count(),
            'overdue_issues' => BookIssue::overdue()->count(),
            'pending_reservations' => BookReservation::pending()->count(),
            'pending_fines' => LibraryFine::pending()->sum('amount'),
            'digital_books' => DigitalBook::active()->count(),
            'today_issues' => BookIssue::whereDate('issue_date', today())->count(),
            'today_returns' => BookIssue::whereDate('return_date', today())->count(),
        ];
    }

    // ===================== REPORTS =====================

    public function getIssueReport(array $filters): array
    {
        $query = BookIssue::with(['member', 'bookCopy.book']);

        if (!empty($filters['date_from'])) {
            $query->whereDate('issue_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('issue_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['member_id'])) {
            $query->where('member_id', $filters['member_id']);
        }

        $issues = $query->get();

        return [
            'total_issues' => $issues->count(),
            'total_returns' => $issues->where('status', 'returned')->count(),
            'total_overdue' => $issues->filter(fn($i) => $i->isOverdue())->count(),
            'by_member_type' => $issues->groupBy('member.member_type')
                ->map(fn($g) => $g->count()),
            'top_books' => $issues->groupBy('bookCopy.book.title')
                ->map(fn($g) => $g->count())
                ->sortDesc()
                ->take(10),
        ];
    }

    public function getFineReport(array $filters): array
    {
        $query = LibraryFine::with('member');

        if (!empty($filters['date_from'])) {
            $query->whereDate('fine_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('fine_date', '<=', $filters['date_to']);
        }

        $fines = $query->get();

        return [
            'total_fines' => $fines->count(),
            'total_amount' => $fines->sum('amount'),
            'total_collected' => $fines->where('status', 'paid')->sum('paid_amount'),
            'total_pending' => $fines->whereIn('status', ['pending', 'partial'])->sum('amount'),
            'total_waived' => $fines->sum('waived_amount'),
            'by_type' => $fines->groupBy('fine_type')
                ->map(fn($g) => [
                    'count' => $g->count(),
                    'amount' => $g->sum('amount'),
                ]),
        ];
    }

    // ===================== DIGITAL LIBRARY =====================

    public function getDigitalBooks(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = DigitalBook::with('category');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['file_type'])) {
            $query->where('file_type', $filters['file_type']);
        }

        if (!empty($filters['access_type'])) {
            $query->where('access_type', $filters['access_type']);
        }

        return $query->orderBy('title')->paginate($perPage);
    }

    public function incrementViewCount(string $uuid): void
    {
        $book = DigitalBook::where('uuid', $uuid)->firstOrFail();
        $book->incrementViewCount();
    }

    public function getSignedDownloadUrl(string $uuid): string
    {
        $book = DigitalBook::where('uuid', $uuid)->firstOrFail();
        
        if (!$book->canDownload()) {
            throw new \Exception('Download not allowed for this book');
        }

        $book->incrementDownloadCount();

        // Generate signed URL (implementation depends on storage driver)
        return url('storage/' . $book->file_path);
    }
}
