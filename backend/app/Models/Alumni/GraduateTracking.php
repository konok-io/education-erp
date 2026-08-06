<?php

declare(strict_types=1);

namespace App\Models\Alumni;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduateTracking extends Model
{
    use HasFactory;

    protected $table = 'graduate_tracking';

    const EMPLOYMENT_EMPLOYED = 'employed';
    const EMPLOYMENT_UNEMPLOYED = 'unemployed';
    const EMPLOYMENT_SELF_EMPLOYED = 'self_employed';
    const EMPLOYMENT_HIGHER_STUDY = 'higher_study';
    const EMPLOYMENT_ENTREPRENEUR = 'entrepreneur';
    const EMPLOYMENT_GOVERNMENT = 'government_service';
    const EMPLOYMENT_ABROAD = 'abroad';

    protected $fillable = [
        'uuid',
        'alumni_id',
        'name',
        'graduation_year',
        'department',
        'degree',
        'employment_status',
        'current_organization',
        'designation',
        'work_description',
        'current_salary',
        'currency',
        'employment_type',
        'industry',
        'skills',
        'location_type',
        'country',
        'city',
        'higher_study_year',
        'university',
        'study_country',
        'degree_pursuing',
        'achievements',
        'publications',
        'notes',
    ];

    protected $casts = [
        'graduation_year' => 'integer',
        'current_salary' => 'decimal:2',
        'higher_study_year' => 'integer',
    ];

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(AlumniProfile::class, 'alumni_id');
    }

    public static function employmentStatuses(): array
    {
        return [
            self::EMPLOYMENT_EMPLOYED => 'Employed',
            self::EMPLOYMENT_UNEMPLOYED => 'Unemployed',
            self::EMPLOYMENT_SELF_EMPLOYED => 'Self Employed',
            self::EMPLOYMENT_HIGHER_STUDY => 'Higher Study',
            self::EMPLOYMENT_ENTREPRENEUR => 'Entrepreneur',
            self::EMPLOYMENT_GOVERNMENT => 'Government Service',
            self::EMPLOYMENT_ABROAD => 'Working Abroad',
        ];
    }
}
