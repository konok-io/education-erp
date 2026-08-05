<?php

declare(strict_types=1);

namespace App\Models\Teacher;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherExperience extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'teacher_experiences';

    protected $fillable = [
        'uuid',
        'teacher_id',
        'organization',
        'organization_bn',
        'designation',
        'department',
        'joining_date',
        'resign_date',
        'is_current',
        'responsibilities',
        'document',
        'remarks',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'resign_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function getDurationAttribute(): ?string
    {
        if (!$this->joining_date) {
            return null;
        }

        $end = $this->resign_date ?? now();
        $diff = $this->joining_date->diff($end);

        $years = $diff->y;
        $months = $diff->m;

        $result = '';
        if ($years > 0) {
            $result .= $years . ' year' . ($years > 1 ? 's' : '');
        }
        if ($months > 0) {
            $result .= ' ' . $months . ' month' . ($months > 1 ? 's' : '');
        }

        return trim($result) ?: null;
    }
}
