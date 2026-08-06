<?php

declare(strict_types=1);

namespace App\Models\Facility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'facility_bookings';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'uuid',
        'booking_no',
        'facility_id',
        'booked_by',
        'organizer_name',
        'event_name',
        'description',
        'booking_date',
        'start_time',
        'end_time',
        'expected_attendees',
        'status',
        'approval_remarks',
        'approved_by',
        'approved_at',
        'rental_fee',
        'security_deposit',
        'payment_status',
        'cancellation_reason',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'expected_attendees' => 'integer',
        'rental_fee' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public static function generateBookingNo(): string
    {
        $prefix = 'FB';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->booking_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function booker(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'booked_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function approve(string $remarks = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approval_remarks' => $remarks,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function reject(string $remarks): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approval_remarks' => $remarks,
        ]);
    }

    public function complete(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }
}
