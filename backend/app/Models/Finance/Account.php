<?php

declare(strict_types=1);

namespace App\Models\Finance;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasUuid, SoftDeletes;

    protected $table = 'accounts';

    protected $fillable = [
        'uuid',
        'account_code',
        'account_name',
        'account_name_bn',
        'parent_id',
        'account_type',
        'account_group',
        'opening_balance',
        'current_balance',
        'dr_cr',
        'is_bank',
        'is_cash',
        'bank_name',
        'branch_name',
        'account_number',
        'routing_number',
        'cost_center_id',
        'is_active',
        'is_system',
        'description',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_bank' => 'boolean',
        'is_cash' => 'boolean',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    // ===================== ACCOUNT TYPES =====================
    public const TYPE_ASSET = 'asset';
    public const TYPE_LIABILITY = 'liability';
    public const TYPE_EQUITY = 'equity';
    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    // ===================== ACCOUNT GROUPS =====================
    public const GROUP_CASH = 'cash';
    public const GROUP_BANK = 'bank';
    public const GROUP_RECEIVABLE = 'receivable';
    public const GROUP_PAYABLE = 'payable';
    public const GROUP_CAPITAL = 'capital';
    public const GROUP_SALES = 'sales';
    public const GROUP_PURCHASE = 'purchase';
    public const GROUP_SALARY = 'salary';
    public const GROUP_UTILITY = 'utility';
    public const GROUP_TAX = 'tax';
    public const GROUP_INVENTORY = 'inventory';
    public const GROUP_FIXED_ASSET = 'fixed_asset';

    // ===================== DR/CR =====================
    public const DR = 'dr';
    public const CR = 'cr';

    // ===================== RELATIONSHIPS =====================

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntryDetail::class, 'account_id');
    }

    // ===================== SCOPES =====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeAssets($query)
    {
        return $query->where('account_type', self::TYPE_ASSET);
    }

    public function scopeLiabilities($query)
    {
        return $query->where('account_type', self::TYPE_LIABILITY);
    }

    public function scopeIncome($query)
    {
        return $query->where('account_type', self::TYPE_INCOME);
    }

    public function scopeExpenses($query)
    {
        return $query->where('account_type', self::TYPE_EXPENSE);
    }

    // ===================== METHODS =====================

    public static function accountTypes(): array
    {
        return [
            self::TYPE_ASSET => 'Assets',
            self::TYPE_LIABILITY => 'Liabilities',
            self::TYPE_EQUITY => 'Equity',
            self::TYPE_INCOME => 'Income',
            self::TYPE_EXPENSE => 'Expense',
        ];
    }

    public static function accountGroups(): array
    {
        return [
            self::GROUP_CASH => 'Cash',
            self::GROUP_BANK => 'Bank',
            self::GROUP_RECEIVABLE => 'Receivable',
            self::GROUP_PAYABLE => 'Payable',
            self::GROUP_CAPITAL => 'Capital',
            self::GROUP_SALES => 'Sales',
            self::GROUP_PURCHASE => 'Purchase',
            self::GROUP_SALARY => 'Salary',
            self::GROUP_UTILITY => 'Utility',
            self::GROUP_TAX => 'Tax',
            self::GROUP_INVENTORY => 'Inventory',
            self::GROUP_FIXED_ASSET => 'Fixed Asset',
        ];
    }

    public function getBalance(): float
    {
        return (float) $this->current_balance;
    }

    public function updateBalance(float $amount, string $type): void
    {
        if ($type === self::DR) {
            $this->current_balance += $amount;
        } else {
            $this->current_balance -= $amount;
        }
        $this->save();
    }
}
