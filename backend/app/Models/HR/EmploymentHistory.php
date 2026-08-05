<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentHistory extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employment_histories';

    protected $fillable = [
        'uuid',
        'employee_id',
        'organization_name',
        'designation',
        'from_date',
        'to_date',
        'responsibilities',
        'achievements',
        'last_salary',
        'reason_for_leaving',
        'contact_person',
        'contact_phone',
        'contact_email',
        'reference_letter',
        'is_verified',
        'verification_notes',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'last_salary' => 'decimal:2',
        'is_verified' => 'boolean',
    ];

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // ===================== METHODS =====================

    public function getDurationInMonthsAttribute(): int
    {
        if (!$this->to_date) {
            return 0;
        }
        return $this->from_date->diffInMonths($this->to_date);
    }

    public function getDurationInYearsAttribute(): float
    {
        return $this->duration_in_months / 12;
    }

    public function getDurationFormattedAttribute(): string
    {
        $months = $this->duration_in_months;
        $years = floor($months / 12);
        $remainingMonths = $months % 12;

        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ' year(s)';
        }
        if ($remainingMonths > 0) {
            $parts[] = $remainingMonths . ' month(s)';
        }

        return implode(', ', $parts) ?: 'N/A';
    }

    public function isCurrent(): bool
    {
        return is_null($this->to_date);
    }
}
