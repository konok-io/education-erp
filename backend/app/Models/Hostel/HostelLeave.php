<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelLeave extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_leaves';

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'leave_no',
        'student_id',
        'building_id',
        'leave_date',
        'return_date',
        'reason',
        'destination',
        'guardian_phone',
        'status',
        'approval_remarks',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'leave_date' => 'date',
        'return_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public static function generateLeaveNo(): string
    {
        $prefix = 'HL';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->leave_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(HostelBuilding::class, 'building_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
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

    public function reject(string $remarks = null): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'approval_remarks' => $remarks,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }
}
