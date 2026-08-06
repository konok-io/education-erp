<?php

declare(strict_types=1);

namespace App\Models\Exam;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamCommitteeMember extends Model
{
    use HasFactory;

    protected $table = 'exam_committee_members';

    protected $fillable = [
        'uuid',
        'session_id',
        'user_id',
        'name',
        'designation',
        'role',
        'status',
        'remarks',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
