<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalYear extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'fiscal_years';

    protected $fillable = [
        'uuid',
        'name',
        'start_date',
        'end_date',
        'is_current',
        'is_closed',
        'closed_at',
        'closed_by',
        'status',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    // ===================== STATUS =====================
    public const STATUS_OPEN = 'open';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'fiscal_year_id');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public static function getCurrent(): ?self
    {
        return self::current()->first();
    }

    public function close(int $userId): void
    {
        $this->update([
            'is_closed' => true,
            'is_current' => false,
            'status' => self::STATUS_CLOSED,
            'closed_at' => now(),
            'closed_by' => $userId,
        ]);
    }
}
