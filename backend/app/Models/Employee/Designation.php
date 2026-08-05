<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'designations';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'level',
        'status',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
