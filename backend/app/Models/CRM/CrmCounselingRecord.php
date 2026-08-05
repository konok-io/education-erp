<?php

declare(strict_types=1);

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmCounselingRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_counseling_records';

    protected $fillable = [
        'uuid',
        'counseling_no',
        'lead_id',
        'inquiry_id',
        'counselor_id',
        'meeting_date',
        'start_time',
        'end_time',
        'meeting_type',
        'discussion',
        'documents_discussed',
        'outcome',
        'recommendation',
        'next_meeting_date',
        'notes',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'next_meeting_date' => 'date',
        'documents_discussed' => 'array',
    ];

    public static function generateCounselingNo(): string
    {
        $prefix = 'CNSL';
        $year = date('Y');
        $lastRecord = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastRecord ? ((int) substr($lastRecord->counseling_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(CrmLead::class, 'lead_id');
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(CrmInquiry::class, 'inquiry_id');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'counselor_id');
    }
}
