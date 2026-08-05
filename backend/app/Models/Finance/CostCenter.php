<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostCenter extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'cost_centers';

    protected $fillable = [
        'uuid',
        'name',
        'name_bn',
        'code',
        'center_type',
        'parent_id',
        'budget_amount',
        'spent_amount',
        'is_active',
        'description',
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ===================== TYPES =====================
    public const TYPE_DEPARTMENT = 'department';
    public const TYPE_CAMPUS = 'campus';
    public const TYPE_PROJECT = 'project';
    public const TYPE_EVENT = 'event';
    public const TYPE_RESEARCH = 'research';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'cost_center_id');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntryDetail::class, 'cost_center_id');
    }

    public static function types(): array
    {
        return [
            self::TYPE_DEPARTMENT => 'Department',
            self::TYPE_CAMPUS => 'Campus',
            self::TYPE_PROJECT => 'Project',
            self::TYPE_EVENT => 'Event',
            self::TYPE_RESEARCH => 'Research',
        ];
    }

    public function getRemainingBudget(): float
    {
        return (float) ($this->budget_amount - $this->spent_amount);
    }

    public function isOverBudget(): bool
    {
        return $this->spent_amount > $this->budget_amount;
    }
}
