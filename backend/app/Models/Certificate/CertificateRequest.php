<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CertificateRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificate_requests';

    const TYPE_CHARACTER = 'character';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_TESTIMONIAL = 'testimonial';
    const TYPE_BONAFIDE = 'bonafide';
    const TYPE_EXPERIENCE = 'experience';
    const TYPE_COURSE_COMPLETION = 'course_completion';
    const TYPE_TRAINING = 'training';
    const TYPE_INTERNSHIP = 'internship';
    const TYPE_MIGRATION = 'migration';
    const TYPE_SCHOLARSHIP = 'scholarship';
    const TYPE_ACHIEVEMENT = 'achievement';
    const TYPE_OTHER = 'other';

    const STATUS_PENDING = 'pending';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_GENERATED = 'generated';
    const STATUS_ISSUED = 'issued';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'request_no',
        'student_id',
        'student_name',
        'student_email',
        'student_phone',
        'certificate_type',
        'purpose',
        'remarks',
        'fee',
        'paid_amount',
        'payment_status',
        'transaction_id',
        'payment_date',
        'certificate_id',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'payment_date' => 'date',
    ];

    public static function generateRequestNo(): string
    {
        $prefix = 'CREQ';
        $year = date('Y');
        $month = date('m');
        $last = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $last ? ((int) substr($last->request_no, -5)) + 1 : 1;
        return sprintf('%s-%s%s-%05d', $prefix, $year, $month, $sequence);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student\Student::class, 'student_id');
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class, 'certificate_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function approve(int $approvedBy): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function reject(int $reviewedBy, string $reason): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
        ]);
    }

    public static function certificateTypes(): array
    {
        return [
            self::TYPE_CHARACTER => 'Character Certificate',
            self::TYPE_TRANSFER => 'Transfer Certificate',
            self::TYPE_TESTIMONIAL => 'Testimonial',
            self::TYPE_BONAFIDE => 'Bonafide Certificate',
            self::TYPE_EXPERIENCE => 'Experience Certificate',
            self::TYPE_COURSE_COMPLETION => 'Course Completion',
            self::TYPE_TRAINING => 'Training Certificate',
            self::TYPE_INTERNSHIP => 'Internship Certificate',
            self::TYPE_MIGRATION => 'Migration Certificate',
            self::TYPE_SCHOLARSHIP => 'Scholarship Certificate',
            self::TYPE_ACHIEVEMENT => 'Achievement Certificate',
            self::TYPE_OTHER => 'Other',
        ];
    }
}
