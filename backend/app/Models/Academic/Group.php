<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'groups';

    protected $fillable = [
        'uuid',
        'class_id',
        'name',
        'code',
        'status',
    ];

    /**
     * Get the class that owns the group.
     */
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_id');
    }

    /**
     * Scope to filter by class.
     */
    public function scopeByClass($query, string $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
