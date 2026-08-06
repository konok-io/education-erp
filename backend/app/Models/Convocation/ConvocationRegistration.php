<?php

declare(strict_types=1);

namespace App\Models\Convocation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConvocationRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'convocation_registrations';

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    const ATTENDANCE_REGISTERED = 'registered';
    const ATTENDANCE_ATTENDED = 'attended';
    const ATTENDANCE_ABSENT = 'absent';

    protected $fillable = [
        'uuid',
        'registration_no',
        'convocation_id',
        'alumni_id',
        'name',
        'name_bn',
        'email',
        'phone',
        'roll_number',
        'registration_no_old',
        'department',
        'program',
        'passing_year',
        'registration_fee',
        'paid_amount',
        'payment_status',
        'transaction_id',
        'payment_date',
        'guest_name',
        'guest_relation',
        'total_guests',
        'dietary_requirements',
        'accessibility_needs',
        'certificate_path',
        'seat_number',
        'attendance',
        'status',
        'remarks',
    ];

    protected $casts = [
        'passing_year' => 'integer',
        'registration_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'total_guests' => 'integer',
        'payment_date' => 'date',
    ];

    public static function generateRegistrationNo(): string
    {
        $prefix = 'REGI';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->registration_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public function convocation(): BelongsTo
    {
        return $this->belongsTo(Convocation::class, 'convocation_id');
    }

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Alumni\AlumniProfile::class, 'alumni_id');
    }

    public function isPaid(): bool
    {
        return (float) $this->paid_amount >= (float) $this->registration_fee;
    }

    public function markAsAttended(): void
    {
        $this->update([
            'attendance' => self::ATTENDANCE_ATTENDED,
        ]);
    }

    public function generateSeatNumber(int $totalSeats): string
    {
        $seat = 'S' . str_pad((string) ($totalSeats + 1), 4, '0', STR_PAD_LEFT);
        $this->update(['seat_number' => $seat]);
        return $seat;
    }
}
