<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookIssue extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'book_issues';

    protected $fillable = [
        'uuid',
        'issue_no',
        'member_id',
        'book_copy_id',
        'issue_date',
        'due_date',
        'return_date',
        'status',
        'renewal_count',
        'max_renewals',
        'issued_by',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'renewal_count' => 'integer',
        'max_renewals' => 'integer',
    ];

    // ===================== STATUS =====================
    public const STATUS_ISSUED = 'issued';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_LOST = 'lost';
    public const STATUS_RENEWED = 'renewed';

    // ===================== RELATIONSHIPS =====================

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function fines(): HasMany
    {
        return $this->hasMany(LibraryFine::class, 'issue_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_ISSUED, self::STATUS_OVERDUE]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_ISSUED)
            ->where('due_date', '<', now());
    }

    public function scopeReturned($query)
    {
        return $query->where('status', self::STATUS_RETURNED);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    // ===================== METHODS =====================

    public static function generateIssueNo(): string
    {
        $prefix = 'ISS';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count() + 1;
        return sprintf('%s/%s/%s/%04d', $prefix, $year, $month, $count);
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_ISSUED && $this->due_date < now();
    }

    public function getOverdueDays(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        return now()->diffInDays($this->due_date);
    }

    public function canRenew(): bool
    {
        return $this->status === self::STATUS_ISSUED 
            && $this->renewal_count < $this->max_renewals;
    }

    public function renew(int $days = 14): void
    {
        $this->update([
            'due_date' => now()->addDays($days),
            'renewal_count' => $this->renewal_count + 1,
            'status' => self::STATUS_RENEWED,
        ]);
    }

    public function markAsReturned(): void
    {
        $this->update([
            'status' => self::STATUS_RETURNED,
            'return_date' => now(),
        ]);
    }

    public function markAsOverdue(): void
    {
        $this->update(['status' => self::STATUS_OVERDUE]);
    }

    public function markAsLost(): void
    {
        $this->update(['status' => self::STATUS_LOST]);
    }

    public function calculateFine(float $dailyRate): float
    {
        return $this->getOverdueDays() * $dailyRate;
    }
}
