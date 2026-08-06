<?php

declare(strict_types=1);

namespace App\Models\Library;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LibraryReadingRoomBooking extends Model
{
    use HasFactory;

    protected $table = 'library_reading_room_bookings';

    const STATUS_BOOKED = 'booked';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'uuid',
        'booking_no',
        'member_id',
        'seat_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'check_in_time',
        'check_out_time',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public static function generateBookingNo(): string
    {
        $prefix = 'RRB';
        $year = date('Y');
        $month = date('m');
        $lastBooking = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastBooking ? ((int) substr($lastBooking->booking_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(LibraryReadingRoomSeat::class, 'seat_id');
    }

    public function checkIn(): void
    {
        $this->update([
            'status' => self::STATUS_CHECKED_IN,
            'check_in_time' => now(),
        ]);
    }

    public function checkOut(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'check_out_time' => now(),
        ]);
    }

    public function markAsNoShow(): void
    {
        $this->update(['status' => self::STATUS_NO_SHOW]);
    }

    public function cancel(): void
    {
        $this->update(['status' => self::STATUS_CANCELLED]);
    }
}
