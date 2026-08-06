<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryIssue extends Model
{
    use HasFactory;

    protected $table = 'library_issues';

    const STATUS_ISSUED = 'issued';
    const STATUS_RETURNED = 'returned';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_LOST = 'lost';
    const STATUS_RENEWED = 'renewed';

    protected $fillable = [
        'uuid',
        'issue_no',
        'member_id',
        'book_copy_id',
        'issued_by',
        'issue_date',
        'due_date',
        'return_date',
        'returned_to',
        'status',
        'renewal_count',
        'fine_amount',
        'fine_paid',
        'fine_status',
        'return_condition',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'decimal:2',
        'renewal_count' => 'integer',
    ];

    public static function generateIssueNo(): string
    {
        $prefix = 'ISS';
        $year = date('Y');
        $month = date('m');
        $lastIssue = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastIssue ? ((int) substr($lastIssue->issue_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(LibraryBookCopy::class, 'book_copy_id');
    }

    public function book(): BelongsTo
    {
        return $this->bookCopy->book();
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'issued_by');
    }

    public function returner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'returned_to');
    }

    public function fine(): BelongsTo
    {
        return $this->belongsTo(LibraryFine::class, 'issue_id');
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ISSUED && $this->due_date < now()->toDateString();
    }

    public function calculateFine(float $perDayRate, int $gracePeriod = 0): float
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $overdueDays = now()->diffInDays($this->due_date) - $gracePeriod;
        if ($overdueDays < 0) {
            $overdueDays = 0;
        }

        return $overdueDays * $perDayRate;
    }

    public function markAsReturned(string $condition = 'good', ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_RETURNED,
            'return_date' => now(),
            'return_condition' => $condition,
            'notes' => $notes,
        ]);

        $this->bookCopy->update([
            'status' => LibraryBookCopy::STATUS_AVAILABLE,
            'last_issue_date' => now(),
        ]);

        $this->member->decrement('current_issued');
    }

    public function renew(int $days = 14): void
    {
        $this->update([
            'due_date' => $this->due_date->addDays($days),
            'renewal_count' => $this->renewal_count + 1,
            'status' => self::STATUS_RENEWED,
        ]);
    }
}
