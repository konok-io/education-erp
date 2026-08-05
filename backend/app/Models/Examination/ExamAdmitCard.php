<?php

declare(strict_types=1);

namespace App\Models\Examination;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ExamAdmitCard extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'exam_admit_cards';

    protected $fillable = [
        'uuid',
        'admit_card_no',
        'exam_id',
        'student_id',
        'student_name',
        'student_roll',
        'registration_no',
        'class_name',
        'section',
        'photo',
        'signature',
        'qr_code',
        'barcode',
        'verification_token',
        'issue_date',
        'valid_until',
        'status',
        'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_ISSUED = 'issued';
    public const STATUS_DOWNLOADED = 'downloaded';
    public const STATUS_USED = 'used';
    public const STATUS_EXPIRED = 'expired';

    // ===================== RELATIONSHIPS =====================

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    // ===================== SCOPES =====================

    public function scopeValid($query)
    {
        return $query->where('status', '!=', self::STATUS_EXPIRED)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now());
            });
    }

    // ===================== METHODS =====================

    public static function generateAdmitCardNo(): string
    {
        $prefix = 'AC';
        $year = now()->format('Y');
        $random = strtoupper(Str::random(6));
        return sprintf('%s/%s/%s', $prefix, $year, $random);
    }

    public static function generateVerificationToken(): string
    {
        return Str::random(32);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_ISSUED => 'Issued',
            self::STATUS_DOWNLOADED => 'Downloaded',
            self::STATUS_USED => 'Used',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }

    public function verify(string $token): bool
    {
        return $this->verification_token === $token && 
               (!$this->valid_until || $this->valid_until >= now());
    }

    public function markAsDownloaded(): void
    {
        $this->update(['status' => self::STATUS_DOWNLOADED]);
    }
}
