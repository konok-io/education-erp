<?php

declare(strict_types=1);

namespace App\Models\Facility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityType extends Model
{
    use HasFactory;

    protected $table = 'facility_types';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'capacity',
        'hourly_rate',
        'requires_approval',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'hourly_rate' => 'decimal:2',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'facility_type_id');
    }
}
