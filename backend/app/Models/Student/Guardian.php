<?php

declare(strict_types=1);

namespace App\Models\Student;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'guardians';

    protected $fillable = [
        'uuid',
        'studentable_type',
        'studentable_id',
        'guardian_type',
        'name',
        'name_bn',
        'relation',
        'occupation',
        'organization',
        'designation',
        'mobile',
        'email',
        'nid',
        'passport',
        'annual_income',
        'photo',
        'address',
        'is_emergency_contact',
    ];

    protected $casts = [
        'annual_income' => 'decimal:2',
        'is_emergency_contact' => 'boolean',
        'address' => 'array',
    ];

    /**
     * Get the parent student.
     */
    public function studentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Guardian types.
     */
    public const TYPE_FATHER = 'father';
    public const TYPE_MOTHER = 'mother';
    public const TYPE_GUARDIAN = 'guardian';
    public const TYPE_OTHER = 'other';

    /**
     * Scope to get emergency contacts.
     */
    public function scopeEmergencyContacts($query)
    {
        return $query->where('is_emergency_contact', true);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('guardian_type', $type);
    }
}
