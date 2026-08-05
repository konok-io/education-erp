<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentType extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employment_types';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'description',
        'status',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'employment_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function types(): array
    {
        return [
            'permanent' => 'Permanent',
            'contract' => 'Contract',
            'probation' => 'Probation',
            'temporary' => 'Temporary',
            'daily_basis' => 'Daily Basis',
            'part_time' => 'Part Time',
        ];
    }
}
