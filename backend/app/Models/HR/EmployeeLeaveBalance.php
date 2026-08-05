<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeaveBalance extends Model
{
    use HasUuid;

    protected $table = 'employee_leave_balances';

    protected $fillable = [
        'uuid',
        'employee_id',
        'leave_type_id',
        'fiscal_year',
        'total_days',
        'used_days',
        'pending_days',
        'carried_forward',
        'balance',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'total_days' => 'integer',
        'used_days' => 'integer',
        'pending_days' => 'integer',
        'carried_forward' => 'integer',
        'balance' => 'integer',
    ];

    // ===================== RELATIONSHIPS =====================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    // ===================== METHODS =====================

    public function calculateBalance(): int
    {
        return $this->total_days + $this->carried_forward - $this->used_days - $this->pending_days;
    }

    public function useDays(int $days): void
    {
        $this->used_days += $days;
        $this->balance = $this->calculateBalance();
        $this->save();
    }

    public function addPendingDays(int $days): void
    {
        $this->pending_days += $days;
        $this->balance = $this->calculateBalance();
        $this->save();
    }

    public static function initializeForEmployee(Employee $employee, int $fiscalYear): void
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        
        foreach ($leaveTypes as $leaveType) {
            $existingBalance = self::where('employee_id', $employee->id)
                ->where('leave_type_id', $leaveType->id)
                ->where('fiscal_year', $fiscalYear)
                ->first();

            if (!$existingBalance) {
                $carriedForward = 0;
                if ($leaveType->is_carry_forward) {
                    $prevYear = self::where('employee_id', $employee->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->where('fiscal_year', $fiscalYear - 1)
                        ->first();
                    
                    if ($prevYear) {
                        $carriedForward = min($prevYear->balance, $leaveType->max_carry_forward_days);
                    }
                }

                self::create([
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveType->id,
                    'fiscal_year' => $fiscalYear,
                    'total_days' => $leaveType->leave_days,
                    'used_days' => 0,
                    'pending_days' => 0,
                    'carried_forward' => $carriedForward,
                    'balance' => $leaveType->leave_days + $carriedForward,
                ]);
            }
        }
    }
}
