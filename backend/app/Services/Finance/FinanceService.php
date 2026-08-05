<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Models\Finance\Account;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalEntryDetail;
use App\Models\Finance\FiscalYear;
use App\Models\Finance\CostCenter;
use App\Models\Finance\Asset;
use App\Models\Finance\Budget;
use App\Models\Finance\BudgetAllocation;
use App\Http\Resources\Finance\JournalEntryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FinanceService
{
    // ===================== ACCOUNTS =====================

    public function getAccounts(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Account::with('parent');

        if (!empty($filters['account_type'])) {
            $query->byType($filters['account_type']);
        }

        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('account_code')->paginate($perPage);
    }

    public function getAccountsAsResource(LengthAwarePaginator $accounts): AnonymousResourceCollection
    {
        return \App\Http\Resources\Finance\AccountResource::collection($accounts);
    }

    public function createAccount(array $data): Account
    {
        return DB::transaction(function () use ($data) {
            $account = Account::create([
                'uuid' => (string) Str::uuid(),
                'account_code' => $data['account_code'],
                'account_name' => $data['account_name'],
                'account_name_bn' => $data['account_name_bn'] ?? null,
                'parent_id' => $this->getModelId(Account::class, $data['parent_id'] ?? null),
                'account_type' => $data['account_type'],
                'account_group' => $data['account_group'] ?? null,
                'opening_balance' => $data['opening_balance'] ?? 0,
                'current_balance' => $data['opening_balance'] ?? 0,
                'dr_cr' => $data['account_type'] === Account::TYPE_EXPENSE || $data['account_type'] === Account::TYPE_ASSET ? Account::DR : Account::CR,
                'is_bank' => $data['is_bank'] ?? false,
                'is_cash' => $data['is_cash'] ?? false,
                'bank_name' => $data['bank_name'] ?? null,
                'account_number' => $data['account_number'] ?? null,
                'routing_number' => $data['routing_number'] ?? null,
                'cost_center_id' => $this->getModelId(CostCenter::class, $data['cost_center_id'] ?? null),
                'is_active' => true,
                'is_system' => false,
                'description' => $data['description'] ?? null,
            ]);

            // Update parent balance if exists
            if (!empty($data['parent_id'])) {
                $parent = Account::find($this->getModelId(Account::class, $data['parent_id']));
                if ($parent) {
                    $parent->updateBalance($account->opening_balance, $parent->dr_cr);
                }
            }

            return $account;
        });
    }

    public function updateAccount(string $uuid, array $data): Account
    {
        $account = Account::where('uuid', $uuid)->firstOrFail();
        $account->update(array_intersect_key($data, array_flip([
            'account_name', 'account_name_bn', 'parent_id', 'account_group',
            'is_bank', 'is_cash', 'bank_name', 'account_number', 'is_active', 'description'
        ])));
        return $account->fresh();
    }

    public function deleteAccount(string $uuid): bool
    {
        $account = Account::where('uuid', $uuid)->firstOrFail();
        
        // Check if account has transactions
        if ($account->journalEntries()->exists()) {
            throw new \Exception('Cannot delete account with transactions');
        }

        return $account->delete();
    }

    // ===================== JOURNAL ENTRIES =====================

    public function getJournalEntries(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = JournalEntry::with(['details.account', 'creator']);

        if (!empty($filters['voucher_type'])) {
            $query->where('voucher_type', $filters['voucher_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('entry_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('entry_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('entry_date', 'desc')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getJournalEntriesAsResource(LengthAwarePaginator $entries): AnonymousResourceCollection
    {
        return JournalEntryResource::collection($entries);
    }

    public function createJournalEntry(array $data, int $userId): ?JournalEntry
    {
        return DB::transaction(function () use ($data, $userId) {
            $fiscalYear = FiscalYear::getCurrent();
            if (!$fiscalYear) {
                throw new \Exception('No active fiscal year found');
            }

            // Validate debits = credits
            $totalDebit = collect($data['details'])->where('dr_cr', 'dr')->sum('amount');
            $totalCredit = collect($data['details'])->where('dr_cr', 'cr')->sum('amount');

            if (bccomp((string) $totalDebit, (string) $totalCredit, 2) !== 0) {
                return null; // Not balanced
            }

            $entry = JournalEntry::create([
                'uuid' => (string) Str::uuid(),
                'voucher_no' => JournalEntry::generateVoucherNo($data['voucher_type']),
                'voucher_type' => $data['voucher_type'],
                'fiscal_year_id' => $fiscalYear->id,
                'entry_date' => $data['entry_date'],
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'],
                'total_amount' => $totalDebit,
                'status' => JournalEntry::STATUS_DRAFT,
                'is_posted' => false,
                'created_by' => $userId,
                'remarks' => $data['remarks'] ?? null,
            ]);

            foreach ($data['details'] as $detail) {
                JournalEntryDetail::create([
                    'uuid' => (string) Str::uuid(),
                    'journal_entry_id' => $entry->id,
                    'account_id' => $this->getModelId(Account::class, $detail['account_id']),
                    'cost_center_id' => $this->getModelId(CostCenter::class, $detail['cost_center_id'] ?? null),
                    'dr_cr' => $detail['dr_cr'],
                    'amount' => $detail['amount'],
                    'cheque_no' => $detail['cheque_no'] ?? null,
                    'cheque_date' => $detail['cheque_date'] ?? null,
                    'narration' => $detail['narration'] ?? null,
                ]);
            }

            return $entry->load('details.account');
        });
    }

    public function updateJournalEntry(string $uuid, array $data): JournalEntry
    {
        return DB::transaction(function () use ($uuid, $data) {
            $entry = JournalEntry::where('uuid', $uuid)->firstOrFail();

            if ($entry->is_posted) {
                throw new \Exception('Cannot update posted entry');
            }

            $entry->update([
                'entry_date' => $data['entry_date'] ?? $entry->entry_date,
                'description' => $data['description'] ?? $entry->description,
            ]);

            if (!empty($data['details'])) {
                // Delete old details
                $entry->details()->delete();

                // Create new details
                foreach ($data['details'] as $detail) {
                    JournalEntryDetail::create([
                        'uuid' => (string) Str::uuid(),
                        'journal_entry_id' => $entry->id,
                        'account_id' => $this->getModelId(Account::class, $detail['account_id']),
                        'dr_cr' => $detail['dr_cr'],
                        'amount' => $detail['amount'],
                        'narration' => $detail['narration'] ?? null,
                    ]);
                }
            }

            return $entry->fresh('details.account');
        });
    }

    public function deleteJournalEntry(string $uuid): bool
    {
        $entry = JournalEntry::where('uuid', $uuid)->firstOrFail();

        if ($entry->is_posted) {
            // Reverse the balances
            foreach ($entry->details as $detail) {
                $account = $detail->account;
                $account->updateBalance($detail->amount, $detail->dr_cr === Account::DR ? Account::CR : Account::DR);
            }
        }

        $entry->details()->delete();
        return $entry->delete();
    }

    public function postJournalEntry(string $uuid, int $userId): void
    {
        DB::transaction(function () use ($uuid, $userId) {
            $entry = JournalEntry::where('uuid', $uuid)->firstOrFail();

            if ($entry->is_posted) {
                throw new \Exception('Entry already posted');
            }

            // Update account balances
            foreach ($entry->details as $detail) {
                $account = $detail->account;
                $account->updateBalance($detail->amount, $detail->dr_cr);
            }

            $entry->post($userId);
        });
    }

    public function approveJournalEntry(string $uuid, int $userId): void
    {
        $entry = JournalEntry::where('uuid', $uuid)->firstOrFail();
        $entry->approve($userId);
    }

    // ===================== LEDGER =====================

    public function getLedger(string $accountUuid, string $dateFrom, string $dateTo): array
    {
        $account = Account::where('uuid', $accountUuid)->firstOrFail();

        $entries = JournalEntryDetail::where('account_id', $account->id)
            ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('entry_date', [$dateFrom, $dateTo]);
            })
            ->with(['journalEntry', 'costCenter'])
            ->orderBy('journalEntry.entry_date')
            ->get();

        $openingBalance = $this->calculateOpeningBalance($account->id, $dateFrom);
        
        $ledger = [];
        $balance = $openingBalance['balance'];
        $drTotal = 0;
        $crTotal = 0;

        foreach ($entries as $entry) {
            $dr = $entry->dr_cr === Account::DR ? (float) $entry->amount : 0;
            $cr = $entry->dr_cr === Account::CR ? (float) $entry->amount : 0;
            $drTotal += $dr;
            $crTotal += $cr;

            $balance += $dr - $cr;

            $ledger[] = [
                'date' => $entry->journalEntry->entry_date->format('Y-m-d'),
                'voucher_no' => $entry->journalEntry->voucher_no,
                'description' => $entry->journalEntry->description,
                'dr' => $dr,
                'cr' => $cr,
                'balance' => $balance,
                'cost_center' => $entry->costCenter?->name,
            ];
        }

        return [
            'account' => [
                'id' => $account->uuid,
                'name' => $account->account_name,
                'code' => $account->account_code,
                'type' => $account->account_type,
            ],
            'opening_balance' => $openingBalance['balance'],
            'opening_dr_cr' => $openingBalance['dr_cr'],
            'entries' => $ledger,
            'totals' => [
                'dr' => $drTotal,
                'cr' => $crTotal,
                'balance' => $balance,
            ],
        ];
    }

    private function calculateOpeningBalance(int $accountId, string $dateFrom): array
    {
        $account = Account::find($accountId);

        $details = JournalEntryDetail::where('account_id', $accountId)
            ->whereHas('journalEntry', function ($q) use ($dateFrom) {
                $q->whereDate('entry_date', '<', $dateFrom)->where('is_posted', true);
            })
            ->get();

        $dr = $details->where('dr_cr', Account::DR)->sum('amount');
        $cr = $details->where('dr_cr', Account::CR)->sum('amount');

        if ($account->account_type === Account::TYPE_ASSET || $account->account_type === Account::TYPE_EXPENSE) {
            $balance = $dr - $cr;
            $dr_cr = Account::DR;
        } else {
            $balance = $cr - $dr;
            $dr_cr = Account::CR;
        }

        return ['balance' => $balance, 'dr_cr' => $dr_cr];
    }

    public function getAccountSummary(string $uuid): array
    {
        $account = Account::where('uuid', $uuid)->firstOrFail();

        $postedDebits = JournalEntryDetail::where('account_id', $account->id)
            ->whereHas('journalEntry', fn($q) => $q->where('is_posted', true))
            ->where('dr_cr', Account::DR)
            ->sum('amount');

        $postedCredits = JournalEntryDetail::where('account_id', $account->id)
            ->whereHas('journalEntry', fn($q) => $q->where('is_posted', true))
            ->where('dr_cr', Account::CR)
            ->sum('amount');

        if ($account->account_type === Account::TYPE_ASSET || $account->account_type === Account::TYPE_EXPENSE) {
            $currentBalance = (float) $account->opening_balance + $postedDebits - $postedCredits;
        } else {
            $currentBalance = (float) $account->opening_balance + $postedCredits - $postedDebits;
        }

        return [
            'account' => [
                'id' => $account->uuid,
                'name' => $account->account_name,
                'code' => $account->account_code,
                'type' => $account->account_type,
            ],
            'opening_balance' => $account->opening_balance,
            'total_debit' => $postedDebits,
            'total_credit' => $postedCredits,
            'current_balance' => $currentBalance,
        ];
    }

    // ===================== REPORTS =====================

    public function getTrialBalance(string $date): array
    {
        $accounts = Account::active()->get();
        $rows = [];
        $totalDr = 0;
        $totalCr = 0;

        foreach ($accounts as $account) {
            $summary = $this->getAccountSummary($account->uuid);
            
            $dr = $account->account_type === Account::TYPE_ASSET || $account->account_type === Account::TYPE_EXPENSE
                ? $summary['current_balance']
                : 0;
            $cr = $account->account_type === Account::TYPE_LIABILITY || $account->account_type === Account::TYPE_EQUITY || $account->account_type === Account::TYPE_INCOME
                ? $summary['current_balance']
                : 0;

            $rows[] = [
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'dr' => $dr,
                'cr' => $cr,
            ];

            $totalDr += $dr;
            $totalCr += $cr;
        }

        return [
            'date' => $date,
            'accounts' => $rows,
            'totals' => [
                'dr' => $totalDr,
                'cr' => $totalCr,
                'is_balanced' => bccomp((string) $totalDr, (string) $totalCr, 2) === 0,
            ],
        ];
    }

    public function getProfitLoss(string $dateFrom, string $dateTo): array
    {
        $incomeAccounts = Account::active()->income()->get();
        $expenseAccounts = Account::active()->expenses()->get();

        $incomeTotal = 0;
        $incomeDetails = [];

        foreach ($incomeAccounts as $account) {
            $summary = $this->getAccountSummary($account->uuid);
            $balance = $summary['current_balance'];
            $incomeTotal += $balance;
            $incomeDetails[] = [
                'account' => $account->account_name,
                'amount' => $balance,
            ];
        }

        $expenseTotal = 0;
        $expenseDetails = [];

        foreach ($expenseAccounts as $account) {
            $summary = $this->getAccountSummary($account->uuid);
            $balance = $summary['current_balance'];
            $expenseTotal += $balance;
            $expenseDetails[] = [
                'account' => $account->account_name,
                'amount' => $balance,
            ];
        }

        $netProfit = $incomeTotal - $expenseTotal;

        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'income' => [
                'total' => $incomeTotal,
                'details' => $incomeDetails,
            ],
            'expense' => [
                'total' => $expenseTotal,
                'details' => $expenseDetails,
            ],
            'net_profit' => $netProfit,
            'net_loss' => $netProfit < 0 ? abs($netProfit) : 0,
        ];
    }

    public function getBalanceSheet(string $date): array
    {
        $assetAccounts = Account::active()->assets()->get();
        $liabilityAccounts = Account::active()->liabilities()->get();
        $equityAccounts = Account::active()->byType(Account::TYPE_EQUITY)->get();

        $assetsTotal = 0;
        $assets = [];
        foreach ($assetAccounts as $account) {
            $summary = $this->getAccountSummary($account->uuid);
            $balance = $summary['current_balance'];
            $assetsTotal += $balance;
            $assets[] = [
                'account' => $account->account_name,
                'amount' => $balance,
            ];
        }

        $liabilitiesTotal = 0;
        $liabilities = [];
        foreach ($liabilityAccounts as $account) {
            $summary = $this->getAccountSummary($account->uuid);
            $balance = $summary['current_balance'];
            $liabilitiesTotal += $balance;
            $liabilities[] = [
                'account' => $account->account_name,
                'amount' => $balance,
            ];
        }

        $equityTotal = 0;
        $equity = [];
        foreach ($equityAccounts as $account) {
            $summary = $this->getAccountSummary($account->uuid);
            $balance = $summary['current_balance'];
            $equityTotal += $balance;
            $equity[] = [
                'account' => $account->account_name,
                'amount' => $balance,
            ];
        }

        // Add net profit/loss to equity
        $profitLoss = $this->getProfitLoss(now()->startOfYear()->format('Y-m-d'), $date);
        $netProfit = $profitLoss['net_profit'];

        return [
            'date' => $date,
            'assets' => [
                'total' => $assetsTotal,
                'details' => $assets,
            ],
            'liabilities' => [
                'total' => $liabilitiesTotal,
                'details' => $liabilities,
            ],
            'equity' => [
                'total' => $equityTotal + $netProfit,
                'details' => array_merge($equity, [['account' => 'Net Profit/Loss', 'amount' => $netProfit]]),
            ],
            'check' => [
                'assets' => $assetsTotal,
                'liabilities_equity' => $liabilitiesTotal + $equityTotal + $netProfit,
                'is_balanced' => bccomp((string) $assetsTotal, (string) ($liabilitiesTotal + $equityTotal + $netProfit), 2) === 0,
            ],
        ];
    }

    public function getCashBook(string $accountUuid, string $dateFrom, string $dateTo): array
    {
        $account = Account::where('uuid', $accountUuid)->firstOrFail();

        $openingBalance = $this->calculateOpeningBalance($account->id, $dateFrom);

        $entries = JournalEntryDetail::where('account_id', $account->id)
            ->whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('entry_date', [$dateFrom, $dateTo])->where('is_posted', true);
            })
            ->with('journalEntry')
            ->get();

        $cashIn = $entries->where('dr_cr', Account::DR)->sum('amount');
        $cashOut = $entries->where('dr_cr', Account::CR)->sum('amount');
        $closingBalance = $openingBalance['balance'] + $cashIn - $cashOut;

        return [
            'account' => $account->account_name,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'opening_balance' => $openingBalance['balance'],
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'closing_balance' => $closingBalance,
            'entries' => $entries->map(fn($e) => [
                'date' => $e->journalEntry->entry_date->format('Y-m-d'),
                'voucher' => $e->journalEntry->voucher_no,
                'description' => $e->journalEntry->description,
                'dr' => $e->dr_cr === Account::DR ? $e->amount : 0,
                'cr' => $e->dr_cr === Account::CR ? $e->amount : 0,
            ]),
        ];
    }

    public function getBankBook(string $accountUuid, string $dateFrom, string $dateTo): array
    {
        return $this->getCashBook($accountUuid, $dateFrom, $dateTo);
    }

    // ===================== FISCAL YEAR =====================

    public function getFiscalYears(): \Illuminate\Database\Eloquent\Collection
    {
        return FiscalYear::orderBy('start_date', 'desc')->get();
    }

    public function createFiscalYear(array $data): FiscalYear
    {
        return FiscalYear::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_current' => false,
            'is_closed' => false,
            'status' => FiscalYear::STATUS_OPEN,
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    public function closeFiscalYear(string $uuid, int $userId): void
    {
        $year = FiscalYear::where('uuid', $uuid)->firstOrFail();

        // Create closing entries
        // ... closing entry logic

        $year->close($userId);
    }

    // ===================== COST CENTERS =====================

    public function getCostCenters(): \Illuminate\Database\Eloquent\Collection
    {
        return CostCenter::with('parent')->where('is_active', true)->get();
    }

    public function createCostCenter(array $data): CostCenter
    {
        return CostCenter::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'code' => $data['code'],
            'center_type' => $data['center_type'],
            'parent_id' => $this->getModelId(CostCenter::class, $data['parent_id'] ?? null),
            'budget_amount' => $data['budget_amount'] ?? 0,
            'is_active' => true,
            'description' => $data['description'] ?? null,
        ]);
    }

    // ===================== ASSETS =====================

    public function getAssets(int $perPage = 50): LengthAwarePaginator
    {
        return Asset::with('account')->paginate($perPage);
    }

    public function createAsset(array $data): Asset
    {
        return Asset::create([
            'uuid' => (string) Str::uuid(),
            'asset_code' => 'AST-' . strtoupper(substr(md5(uniqid()), 0, 6)),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'account_id' => $this->getModelId(Account::class, $data['account_id']),
            'asset_type' => $data['asset_type'],
            'purchase_date' => $data['purchase_date'],
            'purchase_cost' => $data['purchase_cost'],
            'current_value' => $data['purchase_cost'],
            'salvage_value' => $data['salvage_value'] ?? 0,
            'useful_life' => $data['useful_life'] ?? 5,
            'depreciation_method' => $data['depreciation_method'] ?? Asset::DEPRECIATION_STRAIGHT_LINE,
            'depreciation_rate' => $data['depreciation_rate'] ?? 0,
            'accumulated_depreciation' => 0,
            'supplier' => $data['supplier'] ?? null,
            'location' => $data['location'] ?? null,
            'status' => Asset::STATUS_ACTIVE,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function calculateDepreciation(string $uuid): void
    {
        $asset = Asset::where('uuid', $uuid)->firstOrFail();
        $asset->updateDepreciation();
    }

    // ===================== BUDGETS =====================

    public function getBudgets(int $perPage = 50): LengthAwarePaginator
    {
        return Budget::with(['fiscalYear', 'costCenter', 'account'])->paginate($perPage);
    }

    public function createBudget(array $data): Budget
    {
        $fiscalYear = FiscalYear::getCurrent();

        return Budget::create([
            'uuid' => (string) Str::uuid(),
            'budget_code' => 'BDG-' . now()->format('Ym') . '-' . str_pad(Budget::count() + 1, 4, '0', STR_PAD_LEFT),
            'name' => $data['name'],
            'fiscal_year_id' => $fiscalYear?->id,
            'cost_center_id' => $this->getModelId(CostCenter::class, $data['cost_center_id'] ?? null),
            'account_id' => $this->getModelId(Account::class, $data['account_id'] ?? null),
            'budget_type' => $data['budget_type'],
            'amount' => $data['amount'],
            'allocated_amount' => $data['allocated_amount'] ?? $data['amount'],
            'spent_amount' => 0,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => Budget::STATUS_DRAFT,
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    // ===================== DASHBOARD =====================

    public function getDashboard(): array
    {
        $cashAccounts = Account::active()->where('is_cash', true)->get();
        $bankAccounts = Account::active()->where('is_bank', true)->get();

        $cashBalance = 0;
        foreach ($cashAccounts as $account) {
            $summary = $this->getAccountSummary($account->uuid);
            $cashBalance += $summary['current_balance'];
        }

        $bankBalance = 0;
        foreach ($bankAccounts as $account) {
            $summary = $this->getAccountSummary($account->uuid);
            $bankBalance += $summary['current_balance'];
        }

        $today = now()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');

        $todayEntries = JournalEntry::whereDate('entry_date', $today)->where('is_posted', true)->with('details')->get();
        $todayIncome = $todayEntries->flatMap->details->where('dr_cr', Account::CR)->sum('amount');
        $todayExpense = $todayEntries->flatMap->details->where('dr_cr', Account::DR)->sum('amount');

        $monthEntries = JournalEntry::whereBetween('entry_date', [$monthStart, $today])->where('is_posted', true)->with('details')->get();
        $monthIncome = $monthEntries->flatMap->details->where('dr_cr', Account::CR)->sum('amount');
        $monthExpense = $monthEntries->flatMap->details->where('dr_cr', Account::DR)->sum('amount');

        return [
            'cash_balance' => $cashBalance,
            'bank_balance' => $bankBalance,
            'total_balance' => $cashBalance + $bankBalance,
            'today' => [
                'income' => $todayIncome,
                'expense' => $todayExpense,
            ],
            'month' => [
                'income' => $monthIncome,
                'expense' => $monthExpense,
                'net' => $monthIncome - $monthExpense,
            ],
            'pending_vouchers' => JournalEntry::where('status', '!=', JournalEntry::STATUS_POSTED)->count(),
        ];
    }

    // ===================== REPORTS =====================

    public function getIncomeReport(string $dateFrom, string $dateTo): array
    {
        $profitLoss = $this->getProfitLoss($dateFrom, $dateTo);
        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_income' => $profitLoss['income']['total'],
            'details' => $profitLoss['income']['details'],
        ];
    }

    public function getExpenseReport(string $dateFrom, string $dateTo): array
    {
        $profitLoss = $this->getProfitLoss($dateFrom, $dateTo);
        return [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_expense' => $profitLoss['expense']['total'],
            'details' => $profitLoss['expense']['details'],
        ];
    }

    // ===================== EXPORT =====================

    public function exportReport(string $type, string $format, array $params = []): string
    {
        $filename = "finance_report_{$type}_" . now()->format('Ymd_His');
        return url("storage/exports/{$filename}.{$format}");
    }

    // ===================== HELPERS =====================

    private function getModelId(string $model, ?string $uuid): ?int
    {
        if (!$uuid) {
            return null;
        }

        $record = $model::where('uuid', $uuid)->first();
        return $record?->id;
    }
}
