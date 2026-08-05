<?php

declare(strict_types=1);

namespace App\Models\HR;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExitClearance extends Model
{
    use HasUuid;

    protected $table = 'exit_clearances';

    protected $fillable = [
        'uuid',
        'clearance_no',
        'employee_exit_id',
        'employee_id',
        'clearance_type',
        'is_cleared',
        'clearance_date',
        'cleared_by',
        'remarks',
        'pending_items',
        'dues_amount',
    ];

    protected $casts = [
        'is_cleared' => 'boolean',
        'clearance_date' => 'date',
        'dues_amount' => 'decimal:2',
    ];

    // ===================== TYPES =====================
    public const TYPE_LIBRARY = 'library';
    public const TYPE_ACCOUNTS = 'accounts';
    public const TYPE_ICT = 'ict';
    public const TYPE_TRANSPORT = 'transport';
    public const TYPE_HOSTEL = 'hostel';
    public const TYPE_ADMINISTRATION = 'administration';
    public const TYPE_SECURITY = 'security';
    public const TYPE_STORE = 'store';

    // ===================== RELATIONSHIPS =====================

    public function employeeExit(): BelongsTo
    {
        return $this->belongsTo(EmployeeExit::class, 'employee_exit_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function clearedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cleared_by');
    }

    // ===================== METHODS =====================

    public static function clearanceTypes(): array
    {
        return [
            self::TYPE_LIBRARY => 'Library',
            self::TYPE_ACCOUNTS => 'Accounts',
            self::TYPE_ICT => 'ICT/IT',
            self::TYPE_TRANSPORT => 'Transport',
            self::TYPE_HOSTEL => 'Hostel',
            self::TYPE_ADMINISTRATION => 'Administration',
            self::TYPE_SECURITY => 'Security',
            self::TYPE_STORE => 'Store',
        ];
    }

    public static function generateClearanceNo(): string
    {
        $prefix = 'ECLR';
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;
        return sprintf('%s/%s/%05d', $prefix, $year, $count);
    }

    public function markCleared(int $userId, ?string $remarks = null): void
    {
        $this->update([
            'is_cleared' => true,
            'clearance_date' => now(),
            'cleared_by' => $userId,
            'remarks' => $remarks,
        ]);
    }

    public function markPending(?string $pendingItems = null): void
    {
        $this->update([
            'is_cleared' => false,
            'clearance_date' => null,
            'cleared_by' => null,
            'pending_items' => $pendingItems,
        ]);
    }
}
