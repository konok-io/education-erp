<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmitCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'admit_cards';

    const STATUS_PENDING = 'pending';
    const STATUS_ISSUED = 'issued';
    const STATUS_DOWNLOADED = 'downloaded';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'uuid',
        'admit_card_no',
        'student_id',
        'student_name',
        'roll_number',
        'registration_no',
        'session_id',
        'exam_id',
        'center_id',
        'photo',
        'qr_code',
        'issue_date',
        'exam_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'exam_date' => 'date',
    ];

    public static function generateAdmitCardNo(): string
    {
        $prefix = 'AC';
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
        $sequence = $last ? ((int) substr($last->admit_card_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'session_id');
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(ExamCenter::class, 'center_id');
    }
}
