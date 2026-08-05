<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnboardingCompletion extends Model
{
    use HasUuid;

    protected $table = 'onboarding_completions';

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'employee_onboarding_id',
        'checklist_id',
        'is_completed',
        'completed_date',
        'completed_by',
        'remarks',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_date' => 'date',
    ];

    // ===================== RELATIONSHIPS =====================

    public function employeeOnboarding(): BelongsTo
    {
        return $this->belongsTo(EmployeeOnboarding::class, 'employee_onboarding_id');
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(OnboardingChecklist::class, 'checklist_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // ===================== METHODS =====================

    public function markComplete(int $userId, ?string $remarks = null): void
    {
        $this->update([
            'is_completed' => true,
            'completed_date' => now(),
            'completed_by' => $userId,
            'remarks' => $remarks,
        ]);
    }

    public function markIncomplete(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_date' => null,
            'completed_by' => null,
        ]);
    }
}
