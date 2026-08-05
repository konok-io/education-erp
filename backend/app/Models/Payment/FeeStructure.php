<?php

declare(strict_types=1);

namespace App\Models\Payment;

use App\Models\Academic\AcademicLevel;
use App\Models\Academic\AcademicSession;
use App\Models\Academic\Program;
use App\Models\Academic\Semester;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStructure extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'fee_structures';

    protected $fillable = [
        'uuid',
        'category_id',
        'academic_session_id',
        'academic_level_id',
        'program_id',
        'semester_id',
        'name',
        'amount',
        'frequency',
        'effective_date',
        'expiry_date',
        'is_mandatory',
        'is_active',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
    ];

    // ===================== FREQUENCY =====================
    public const FREQ_ONE_TIME = 'one_time';
    public const FREQ_MONTHLY = 'monthly';
    public const FREQ_QUARTERLY = 'quarterly';
    public const FREQ_HALF_YEARLY = 'half_yearly';
    public const FREQ_YEARLY = 'yearly';
    public const FREQ_CUSTOM = 'custom';

    public function category(): BelongsTo
    {
        return $this->belongsTo(FeeCategory::class, 'category_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    public function academicLevel(): BelongsTo
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public static function frequencies(): array
    {
        return [
            self::FREQ_ONE_TIME => 'One Time',
            self::FREQ_MONTHLY => 'Monthly',
            self::FREQ_QUARTERLY => 'Quarterly',
            self::FREQ_HALF_YEARLY => 'Half Yearly',
            self::FREQ_YEARLY => 'Yearly',
            self::FREQ_CUSTOM => 'Custom',
        ];
    }
}
