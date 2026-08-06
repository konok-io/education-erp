<?php

declare(strict_types=1);

namespace App\Models\Hostel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostelFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hostel_fees';

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_WAIVED = 'waived';

    protected $fillable = [
        'uuid',
        'fee_no',
        'student_id',
        'building_id',
        'fee_head',
        'amount',
        'waiver',
        'paid',
        'due',
        'status',
        'due_date',
        'paid_date',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'waiver' => 'decimal:2',
        'paid' => 'decimal:2',
        'due' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public static function generateFeeNo(): string
    {
        $prefix = 'HHF';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->fee_no, -5)) + 1 : 1;
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

    public function recordPayment(float $amount): void
    {
        $newPaid = $this->paid + $amount;

        if ($newPaid >= $this->amount - $this->waiver) {
            $this->update([
                'paid' => $this->amount - $this->waiver,
                'due' => 0,
                'status' => self::STATUS_PAID,
                'paid_date' => now(),
            ]);
        } else {
            $this->update([
                'paid' => $newPaid,
                'due' => $this->amount - $this->waiver - $newPaid,
                'status' => self::STATUS_PARTIAL,
            ]);
        }
    }

    public function waive(float $amount): void
    {
        $newWaiver = $this->waiver + $amount;
        $this->update([
            'waiver' => $newWaiver,
            'due' => $this->amount - $newWaiver - $this->paid,
        ]);
    }
}
