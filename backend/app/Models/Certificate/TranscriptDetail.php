<?php

declare(strict_types=1);

namespace App\Models\Certificate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranscriptDetail extends Model
{
    use HasFactory;

    protected $table = 'transcript_details';

    protected $fillable = [
        'uuid',
        'transcript_id',
        'semester',
        'course_code',
        'course_name',
        'credits',
        'grade',
        'grade_point',
        'marks',
        'semester_gpa',
        'remarks',
    ];

    protected $casts = [
        'credits' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'marks' => 'decimal:2',
        'semester_gpa' => 'decimal:2',
    ];

    public function transcript(): BelongsTo
    {
        return $this->belongsTo(Transcript::class, 'transcript_id');
    }
}
