<?php

declare(strict_types=1);

namespace App\Models\Library;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookReservation extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'book_reservations';

    protected $fillable = [
        'uuid',
        'reservation_no',
        'member_id',
        'book_id',
        'reservation_date',
        'expiry_date',
        'status',
        'fulfilled_date',
        'notify_status',
        'notes',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'expiry_date' => 'date',
        'fulfilled_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_READY = 'ready';
    public const STATUS_FULFILLED = 'fulfilled';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== NOTIFY STATUS =====================
    public const NOTIFY_PENDING = 'pending';
    public const NOTIFY_SENT = 'sent';
    public const NOTIFY_CONFIRMED = 'confirmed';

    // ===================== RELATIONSHIPS =====================

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_READY]);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReady($query)
    {
        return $query->where('status', self::STATUS_READY);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', self::STATUS_EXPIRED)
            ->orWhere('expiry_date', '<', now());
    }

    // ===================== METHODS =====================

    public static function generateReservationNo(): string
    {
        $prefix = 'RES';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%04d', $prefix, $year, $count);
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED 
            || ($this->expiry_date && $this->expiry_date < now());
    }

    public function markAsReady(): void
    {
        $this->update([
            'status' => self::STATUS_READY,
            'notify_status' => self::NOTIFY_SENT,
        ]);
    }

    public function markAsFulfilled(): void
    {
        $this->update([
            'status' => self::STATUS_FULFILLED,
            'fulfilled_date' => now(),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function getQueuePosition(): int
    {
        return self::where('book_id', $this->book_id)
            ->where('status', self::STATUS_PENDING)
            ->where('reservation_date', '<=', $this->reservation_date)
            ->count();
    }
}
