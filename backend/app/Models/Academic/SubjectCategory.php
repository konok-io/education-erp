<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubjectCategory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'subject_categories';

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'description',
        'status',
    ];

    /**
     * Get the subjects for this category.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'subject_category_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
