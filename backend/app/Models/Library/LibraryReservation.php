<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryReservation extends Model
{
    use HasFactory;

    protected $table = 'library_reservations';

    const STATUS_PENDING = 'pending';
    const STATUS_READY = 'ready';
    const STATUS_PICKED_UP = 'picked_up';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'uuid',
        'reservation_no',
        'member_id',
        'book_copy_id',
        'book_id',
        'queue_position',
        'reserved_date',
        'expiry_date',
        'pickup_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'reserved_date' => 'date',
        'expiry_date' => 'date',
        'pickup_date' => 'date',
        'queue_position' => 'integer',
    ];

    public static function generateReservationNo(): string
    {
        $prefix = 'RES';
        $year = date('Y');
        $month = date('m');
        $lastReservation = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastReservation ? ((int) substr($lastReservation->reservation_no, -5)) + 1 : 1;
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
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function markAsReady(): void
    {
        $this->update([
            'status' => self::STATUS_READY,
            'expiry_date' => now()->addDays(3),
        ]);
    }

    public function markAsPickedUp(): void
    {
        $this->update([
            'status' => self::STATUS_PICKED_UP,
            'pickup_date' => now(),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }

    public function updateQueuePosition(): void
    {
        $count = self::where('book_id', $this->book_id)
            ->where('status', self::STATUS_PENDING)
            ->where('id', '<', $this->id)
            ->count();

        $this->update(['queue_position' => $count + 1]);
    }
}
