<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntryDetail extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'journal_entry_details';

    protected $fillable = [
        'uuid',
        'journal_entry_id',
        'account_id',
        'cost_center_id',
        'dr_cr',
        'amount',
        'cheque_no',
        'cheque_date',
        'narration',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cheque_date' => 'date',
    ];

    // ===================== DR/CR =====================
    public const DR = 'dr';
    public const CR = 'cr';

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function isDebit(): bool
    {
        return $this->dr_cr === self::DR;
    }

    public function isCredit(): bool
    {
        return $this->dr_cr === self::CR;
    }
}
