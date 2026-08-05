<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeOnboarding extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'employee_onboardings';

    protected $fillable = [
        'uuid',
        'onboarding_no',
        'employee_id',
        'offer_letter_id',
        'start_date',
        'completion_date',
        'status',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'completion_date' => 'date',
    ];

    // ===================== STATUS =====================
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function offerLetter(): BelongsTo
    {
        return $this->belongsTo(OfferLetter::class, 'offer_letter_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function completions(): HasMany
    {
        return $this->hasMany(OnboardingCompletion::class, 'employee_onboarding_id');
    }

    // ===================== METHODS =====================

    public static function generateOnboardingNo(): string
    {
        $prefix = 'ONB';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->completions()->count();
        if ($total === 0) {
            return 0;
        }
        $completed = $this->completions()->where('is_completed', true)->count();
        return ($completed / $total) * 100;
    }

    public function getPendingChecklistsAttribute(): array
    {
        return $this->completions()->where('is_completed', false)->with('checklist')->get()->pluck('checklist')->toArray();
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
