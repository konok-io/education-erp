<?php

declare(strict_types=1);

namespace App\Services\Library;

use App\Models\Library\LibraryFine;
use App\Models\Library\LibraryMember;
use App\Models\Library\LibraryFineRule;
use App\Models\Library\LibraryIssue;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class FineService
{
    public function getFines(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = LibraryFine::with(['member', 'issue']);

        if (!empty($filters['status'])) {
            $query->where('library_fines.status', $filters['status']);
        }

        if (!empty($filters['fine_type'])) {
            $query->where('fine_type', $filters['fine_type']);
        }

        if (!empty($filters['member_id'])) {
            $query->where('member_id', $filters['member_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createFine(array $data): LibraryFine
    {
        $member = LibraryMember::findOrFail($data['member_id']);

        $fine = LibraryFine::create([
            'uuid' => (string) Str::uuid(),
            'fine_no' => LibraryFine::generateFineNo(),
            'member_id' => $member->id,
            'issue_id' => $data['issue_id'] ?? null,
            'fine_type' => $data['fine_type'],
            'reason' => $data['reason'],
            'amount' => $data['amount'],
            'paid_amount' => 0,
            'fine_date' => now(),
            'status' => LibraryFine::STATUS_PENDING,
            'notes' => $data['notes'] ?? null,
        ]);

        $member->increment('outstanding_fine', $data['amount']);

        return $fine;
    }

    public function collectFine(string $uuid, float $amount): LibraryFine
    {
        $fine = LibraryFine::where('uuid', $uuid)->firstOrFail();

        if ($fine->status === LibraryFine::STATUS_PAID) {
            throw new \Exception('Fine has already been fully paid');
        }

        $fine->pay($amount);

        return $fine->fresh();
    }

    public function waiveFine(string $uuid, float $amount): LibraryFine
    {
        $fine = LibraryFine::where('uuid', $uuid)->firstOrFail();

        $fine->waive($amount);

        return $fine->fresh();
    }

    public function createLostBookFine(string $issueUuid): LibraryFine
    {
        $issue = LibraryIssue::where('uuid', $issueUuid)->firstOrFail();

        if ($issue->status === LibraryIssue::STATUS_LOST) {
            throw new \Exception('Lost book fine already created for this issue');
        }

        $fineAmount = $issue->bookCopy->book->replacement_cost;

        $fine = $this->createFine([
            'member_id' => $issue->member_id,
            'issue_id' => $issue->id,
            'fine_type' => LibraryFine::TYPE_LOST_BOOK,
            'reason' => 'Lost book: ' . $issue->bookCopy->book->title,
            'amount' => $fineAmount,
        ]);

        $issue->update(['status' => LibraryIssue::STATUS_LOST]);
        $issue->bookCopy->update(['status' => 'lost']);

        return $fine;
    }

    public function createDamagedBookFine(string $issueUuid, float $amount): LibraryFine
    {
        $issue = LibraryIssue::where('uuid', $issueUuid)->firstOrFail();

        return $this->createFine([
            'member_id' => $issue->member_id,
            'issue_id' => $issue->id,
            'fine_type' => LibraryFine::TYPE_DAMAGED_BOOK,
            'reason' => 'Damaged book: ' . $issue->bookCopy->book->title,
            'amount' => $amount,
        ]);
    }

    public function getFineStats(): array
    {
        return [
            'total_fines' => LibraryFine::count(),
            'pending_fines' => LibraryFine::pending()->count(),
            'pending_amount' => LibraryFine::pending()->sum('amount'),
            'paid_fines' => LibraryFine::paid()->count(),
            'paid_amount' => LibraryFine::paid()->sum('paid_amount'),
            'waived_fines' => LibraryFine::where('status', LibraryFine::STATUS_WAIVED)->count(),
            'by_type' => LibraryFine::selectRaw('fine_type, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('fine_type')
                ->get(),
        ];
    }
}
