<?php

declare(strict_types=1);

namespace App\Services\Library;

use App\Models\Library\LibraryBook;
use App\Models\Library\LibraryBookCopy;
use App\Models\Library\LibraryMember;
use App\Models\Library\LibraryIssue;
use App\Models\Library\LibraryFine;
use App\Models\Library\LibraryFineRule;
use App\Models\Library\LibraryIssueRule;
use App\Models\Library\LibraryReservation;
use App\Models\Library\LibraryReadingHistory;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IssueService
{
    public function issueBook(array $data): LibraryIssue
    {
        return DB::transaction(function () use ($data) {
            $member = LibraryMember::findOrFail($data['member_id']);
            $bookCopy = LibraryBookCopy::where('uuid', $data['book_copy_uuid'])->firstOrFail();

            // Validate member can borrow
            if (!$member->canIssueMoreBooks()) {
                throw new \Exception('Member has reached maximum book limit or has outstanding fines');
            }

            // Validate book is available
            if ($bookCopy->status !== LibraryBookCopy::STATUS_AVAILABLE) {
                throw new \Exception('Book is not available for issue');
            }

            // Get issue rules
            $rule = LibraryIssueRule::getRuleForMember($member, $bookCopy->book->category_id);
            $maxDays = $rule ? $rule->max_days : $member->max_days ?? 14;

            // Create issue
            $issue = LibraryIssue::create([
                'uuid' => (string) Str::uuid(),
                'issue_no' => LibraryIssue::generateIssueNo(),
                'member_id' => $member->id,
                'book_copy_id' => $bookCopy->id,
                'issued_by' => $data['issued_by'] ?? auth()->id(),
                'issue_date' => now(),
                'due_date' => now()->addDays($maxDays),
                'status' => LibraryIssue::STATUS_ISSUED,
                'renewal_count' => 0,
                'fine_amount' => 0,
                'fine_paid' => 0,
                'fine_status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // Update book copy status
            $bookCopy->update(['status' => LibraryBookCopy::STATUS_ISSUED]);

            // Update member issued count
            $member->increment('current_issued');

            // Record reading history
            LibraryReadingHistory::recordAccess(
                $member->id,
                $bookCopy->book_id,
                $bookCopy->id,
                LibraryReadingHistory::ACCESS_ISSUE
            );

            // Check for pending reservations
            $this->processReservations($bookCopy->book_id);

            return $issue->load(['member', 'bookCopy.book']);
        });
    }

    public function returnBook(string $uuid, array $data): LibraryIssue
    {
        return DB::transaction(function () use ($uuid, $data) {
            $issue = LibraryIssue::where('uuid', $uuid)->firstOrFail();

            if ($issue->status !== LibraryIssue::STATUS_ISSUED && $issue->status !== LibraryIssue::STATUS_OVERDUE) {
                throw new \Exception('Book has already been returned');
            }

            $bookCopy = $issue->bookCopy;
            $member = $issue->member;

            // Calculate fine if overdue
            $fineAmount = 0;
            if ($issue->isOverdue()) {
                $rule = LibraryFineRule::getRuleForMember($member, 'overdue');
                if ($rule) {
                    $overdueDays = now()->diffInDays($issue->due_date);
                    $fineAmount = $rule->calculateFine($overdueDays);
                }
            }

            // Check for damaged condition
            if (!empty($data['return_condition']) && $data['return_condition'] !== 'excellent' && $data['return_condition'] !== 'good') {
                $fineAmount += $bookCopy->book->replacement_cost * 0.1; // 10% of replacement cost
            }

            // Create fine if applicable
            if ($fineAmount > 0) {
                $fine = LibraryFine::create([
                    'uuid' => (string) Str::uuid(),
                    'fine_no' => LibraryFine::generateFineNo(),
                    'member_id' => $member->id,
                    'issue_id' => $issue->id,
                    'fine_type' => LibraryFine::TYPE_LATE_RETURN,
                    'reason' => $data['return_condition'] ?? 'Late return',
                    'amount' => $fineAmount,
                    'paid_amount' => 0,
                    'fine_date' => now(),
                    'status' => LibraryFine::STATUS_PENDING,
                ]);

                $member->increment('outstanding_fine', $fineAmount);
            }

            // Update issue
            $issue->update([
                'return_date' => now(),
                'returned_to' => auth()->id(),
                'status' => LibraryIssue::STATUS_RETURNED,
                'fine_amount' => $fineAmount,
                'return_condition' => $data['return_condition'] ?? 'good',
                'notes' => $data['notes'] ?? null,
            ]);

            // Update book copy
            $bookCopy->update([
                'status' => LibraryBookCopy::STATUS_AVAILABLE,
                'condition' => $data['return_condition'] ?? $bookCopy->condition,
                'last_issue_date' => now(),
            ]);

            // Update member
            $member->decrement('current_issued');

            // Record reading history
            LibraryReadingHistory::recordAccess(
                $member->id,
                $bookCopy->book_id,
                $bookCopy->id,
                LibraryReadingHistory::ACCESS_RETURN
            );

            return $issue->fresh(['member', 'bookCopy.book', 'fine']);
        });
    }

    public function renewBook(string $uuid): LibraryIssue
    {
        $issue = LibraryIssue::where('uuid', $uuid)->firstOrFail();

        if ($issue->status === LibraryIssue::STATUS_RETURNED) {
            throw new \Exception('Book has already been returned');
        }

        $rule = LibraryIssueRule::getRuleForMember($issue->member, $issue->bookCopy->book->category_id);
        $maxRenewals = $rule ? $rule->max_renewals : 2;

        if ($issue->renewal_count >= $maxRenewals) {
            throw new \Exception('Maximum renewal limit reached');
        }

        $days = $rule ? $rule->max_days : 14;
        $issue->renew($days);

        return $issue->fresh();
    }

    public function reserveBook(array $data): LibraryReservation
    {
        $member = LibraryMember::findOrFail($data['member_id']);
        $book = LibraryBook::where('uuid', $data['book_uuid'])->firstOrFail();

        // Check if book is available
        $availableCopy = $book->availableCopies()->first();
        if ($availableCopy) {
            throw new \Exception('Book is available, no reservation needed');
        }

        // Get queue position
        $queuePosition = LibraryReservation::where('book_id', $book->id)
            ->whereIn('status', [LibraryReservation::STATUS_PENDING, LibraryReservation::STATUS_READY])
            ->count() + 1;

        return LibraryReservation::create([
            'uuid' => (string) Str::uuid(),
            'reservation_no' => LibraryReservation::generateReservationNo(),
            'member_id' => $member->id,
            'book_id' => $book->id,
            'queue_position' => $queuePosition,
            'reserved_date' => now(),
            'status' => LibraryReservation::STATUS_PENDING,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function processReservations(int $bookId): void
    {
        $book = LibraryBook::find($bookId);
        $availableCopy = $book->availableCopies()->first();

        if (!$availableCopy) {
            return;
        }

        // Find first pending reservation
        $reservation = LibraryReservation::where('book_id', $bookId)
            ->where('status', LibraryReservation::STATUS_PENDING)
            ->orderBy('queue_position')
            ->first();

        if ($reservation) {
            $reservation->update([
                'book_copy_id' => $availableCopy->id,
            ]);
            $reservation->markAsReady();

            // Update queue positions
            LibraryReservation::where('book_id', $bookId)
                ->where('status', LibraryReservation::STATUS_PENDING)
                ->where('id', '!=', $reservation->id)
                ->each(function ($res) {
                    $res->updateQueuePosition();
                });
        }
    }

    public function getIssues(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = LibraryIssue::with(['member', 'bookCopy.book']);

        if (!empty($filters['status'])) {
            $query->where('library_issues.status', $filters['status']);
        }

        if (!empty($filters['member_id'])) {
            $query->where('member_id', $filters['member_id']);
        }

        if (!empty($filters['overdue'])) {
            $query->where('due_date', '<', now())->where('status', LibraryIssue::STATUS_ISSUED);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getTodayStats(): array
    {
        $today = now()->toDateString();

        return [
            'today_issued' => LibraryIssue::whereDate('issue_date', $today)->count(),
            'today_returned' => LibraryIssue::whereDate('return_date', $today)->count(),
            'current_issued' => LibraryIssue::where('status', LibraryIssue::STATUS_ISSUED)->count(),
            'overdue' => LibraryIssue::where('due_date', '<', now())
                ->whereIn('status', [LibraryIssue::STATUS_ISSUED, LibraryIssue::STATUS_OVERDUE])
                ->count(),
        ];
    }
}
