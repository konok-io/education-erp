<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeEmergencyContact extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_emergency_contacts';

    protected $fillable = [
        'uuid',
        'employeeable_type',
        'employeeable_id',
        'name',
        'relation',
        'mobile',
        'email',
        'address',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function employeeable(): MorphTo
    {
        return $this->morphTo();
    }
}
