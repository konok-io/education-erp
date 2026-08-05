<?php

declare(strict_types=1);

namespace App\Models\Employee;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeProfile extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_profiles';

    protected $fillable = [
        'uuid',
        'employeeable_type',
        'employeeable_id',
        'first_name',
        'last_name',
        'full_name',
        'first_name_bn',
        'last_name_bn',
        'full_name_bn',
        'gender',
        'date_of_birth',
        'blood_group',
        'religion',
        'nationality',
        'nid',
        'passport',
        'email',
        'mobile',
        'alternate_mobile',
        'photo',
        'signature',
        'marital_status',
        'father_name',
        'mother_name',
        'present_address',
        'permanent_address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'present_address' => 'array',
        'permanent_address' => 'array',
    ];

    public function employeeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullNameAttribute(): ?string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature ? asset('storage/' . $this->signature) : null;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
