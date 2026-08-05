<?php

declare(strict_types=1);

namespace App\Models\Student;

use App\Enums\Gender;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'student_profiles';

    protected $fillable = [
        'uuid',
        'studentable_type',
        'studentable_id',
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
        'birth_certificate',
        'nid',
        'passport',
        'photo',
        'signature',
        'email',
        'mobile',
        'present_address',
        'permanent_address',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'present_address' => 'array',
        'permanent_address' => 'array',
    ];

    /**
     * Get the parent student.
     */
    public function studentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): ?string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Get full name in Bangla.
     */
    public function getFullNameBnAttribute(): ?string
    {
        return trim("{$this->first_name_bn} {$this->last_name_bn}");
    }

    /**
     * Get photo URL.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    /**
     * Get signature URL.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        return $this->signature ? asset('storage/' . $this->signature) : null;
    }

    /**
     * Get age.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }

        return $this->date_of_birth->age;
    }
}
