<?php

declare(strict_types=1);

namespace App\Models\Academic;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSession extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'academic_sessions';

    protected $fillable = [
        'uuid',
        'title',
        'code',
        'start_date',
        'end_date',
        'is_current',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Get the classes for this session.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(AcademicClass::class, 'session_id');
    }

    /**
     * Scope to get current session.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Set current session.
     */
    public static function setCurrent(string $uuid): void
    {
        self::where('is_current', true)->update(['is_current' => false]);
        self::where('uuid', $uuid)->update(['is_current' => true]);
    }
}
