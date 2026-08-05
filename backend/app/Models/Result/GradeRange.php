<?php

declare(strict_types=1);

namespace App\Models\Result;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeRange extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'grade_ranges';

    protected $fillable = [
        'uuid',
        'grade_rule_id',
        'grade',
        'grade_bn',
        'min_percentage',
        'max_percentage',
        'grade_point',
        'description',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function gradeRule(): BelongsTo
    {
        return $this->belongsTo(GradeRule::class, 'grade_rule_id');
    }
}
