<?php

declare(strict_types=1);

namespace App\Models\Payment;

use App\Models\Student\Student;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'invoices';

    protected $fillable = [
        'uuid',
        'invoice_no',
        'student_id',
        'category_id',
        'academic_session_id',
        'semester_id',
        'billing_month',
        'billing_year',
        'total_amount',
        'discount_amount',
        'fine_amount',
        'waiver_amount',
        'net_amount',
        'paid_amount',
        'due_amount',
        'due_date',
        'status',
        'remarks',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'waiver_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'due_date' => 'date',
        'published_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_CANCELLED = 'cancelled';

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FeeCategory::class, 'category_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\AcademicSession::class, 'academic_session_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Semester::class, 'semester_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function waivers(): HasMany
    {
        return $this->hasMany(Waiver::class, 'invoice_id');
    }

    public static function generateInvoiceNo(): string
    {
        $prefix = 'INV';
        $year = now()->format('Y');
        $month = now()->format('m');
        $count = self::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending',
            self::STATUS_PARTIAL => 'Partial',
            self::STATUS_PAID => 'Paid',
            self::STATUS_OVERDUE => 'Overdue',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function isPaid(): bool
    {
        return (float) $this->due_amount <= 0;
    }

    public function calculateDue(): void
    {
        $this->due_amount = $this->net_amount - $this->paid_amount;
        
        if ($this->due_amount <= 0) {
            $this->status = self::STATUS_PAID;
        } elseif ($this->paid_amount > 0) {
            $this->status = self::STATUS_PARTIAL;
        }
    }
}
