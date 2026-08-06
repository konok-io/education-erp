<?php

declare(strict_types=1);

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'asset_assignments';

    const STATUS_ACTIVE = 'active';
    const STATUS_RETURNED = 'returned';
    const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'uuid',
        'assignment_no',
        'asset_id',
        'assignee_id',
        'assignee_type',
        'assigned_by',
        'assignment_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'purpose',
        'return_notes',
        'return_condition',
        'notes',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    public static function generateAssignmentNo(): string
    {
        $prefix = 'ASG';
        $year = date('Y');
        $lastAssignment = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        $sequence = $lastAssignment ? ((int) substr($lastAssignment->assignment_no, -5)) + 1 : 1;
        return sprintf('%s-%s-%05d', $prefix, $year, $sequence);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assignee_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }

    public function markAsReturned(string $condition, ?string $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_RETURNED,
            'actual_return_date' => now(),
            'return_condition' => $condition,
            'return_notes' => $notes,
        ]);

        $this->asset->update(['status' => Asset::STATUS_AVAILABLE]);
    }
}
