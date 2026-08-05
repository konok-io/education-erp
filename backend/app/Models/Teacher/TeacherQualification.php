<?php

declare(strict_types=1);

namespace App\Models\Teacher;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherQualification extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'teacher_qualifications';

    protected $fillable = [
        'uuid',
        'teacher_id',
        'degree',
        'degree_bn',
        'institution',
        'board_university',
        'subject',
        'passing_year',
        'result',
        'result_point',
        'attachment',
        'is_verified',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'passing_year' => 'integer',
        'result_point' => 'decimal:2',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}
