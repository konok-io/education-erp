<?php

declare(strict_types=1);

namespace App\Models\Student;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMedical extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'student_medical';

    protected $fillable = [
        'uuid',
        'studentable_type',
        'studentable_id',
        'height',
        'weight',
        'blood_group',
        'allergy',
        'allergy_details',
        'chronic_disease',
        'chronic_disease_details',
        'disability',
        'disability_details',
        'medication',
        'medical_note',
        'last_checkup_date',
        'doctor_name',
        'doctor_phone',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'last_checkup_date' => 'date',
    ];

    /**
     * Get the parent student.
     */
    public function studentable(): MorphTo
    {
        return $this->morphTo();
    }
}
