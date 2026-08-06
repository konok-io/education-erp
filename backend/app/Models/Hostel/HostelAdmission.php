<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelAdmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_admissions';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_CHECKED_IN = 'checked_in';
    const STATUS_CHECKED_OUT = 'checked_out';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'admission_no',
        'student_id',
        'student_name',
        'guardian_name',
        'guardian_phone',
        'building_id',
        'room_id',
        'bed_id',
        'admission_date',
        'checkout_date',
        'status',
        'reason',
        'notes',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'checkout_date' => 'date',
    ];

    public static function generateAdmissionNo(): string
    {
        $prefix = 'HA';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->admission_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(HostelBuilding::class, 'building_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HostelRoom::class, 'room_id');
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(HostelBed::class, 'bed_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function approve(): void
    {
        $this->update(['status' => self::STATUS_APPROVED]);
    }

    public function checkIn(): void
    {
        $this->update([
            'status' => self::STATUS_CHECKED_IN,
            'admission_date' => now(),
        ]);

        if ($this->bed) {
            $this->bed->allocate();
        }
    }

    public function checkOut(): void
    {
        $this->update([
            'status' => self::STATUS_CHECKED_OUT,
            'checkout_date' => now(),
        ]);

        if ($this->bed) {
            $this->bed->vacate();
        }
    }

    public function isActive(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_CHECKED_IN,
        ]);
    }
}
