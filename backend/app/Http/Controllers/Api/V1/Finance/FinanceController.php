<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Finance;

use App\Http\Controllers\BaseController;
use App\Services\Finance\FinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FinanceController extends BaseController
{
    public function __construct(
        private readonly FinanceService $financeService
    ) {}

    // ===================== ACCOUNTS =====================

    public function getAccounts(Request $request): AnonymousResourceCollection
    {
        $accounts = $this->financeService->getAccounts(
            $request->input('per_page', 50),
            $request->only(['account_type', 'parent_id', 'is_active'])
        );
        return $this->financeService->getAccountsAsResource($accounts);
    }

    public function createAccount(Request $request): JsonResponse
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_code' => 'required|string|unique:accounts,account_code',
            'account_type' => 'required|in:asset,liability,equity,income,expense',
        ]);

        $account = $this->financeService->createAccount($request->all());
        return $this->created($account, 'Account created successfully');
    }

    public function updateAccount(Request $request, string $uuid): JsonResponse
    {
        $account = $this->financeService->updateAccount($uuid, $request->all());
        return $this->updated($account, 'Account updated successfully');
    }

    public function deleteAccount(string $uuid): JsonResponse
    {
        $this->financeService->deleteAccount($uuid);
        return $this->deleted('Account deleted successfully');
    }

    // ===================== JOURNAL ENTRIES =====================

    public function getJournalEntries(Request $request): AnonymousResourceCollection
    {
        $entries = $this->financeService->getJournalEntries(
            $request->input('per_page', 50),
            $request->only(['voucher_type', 'status', 'date_from', 'date_to'])
        );
        return $this->financeService->getJournalEntriesAsResource($entries);
    }

    public function createJournalEntry(Request $request): JsonResponse
    {
        $request->validate([
            'voucher_type' => 'required|in:journal,payment,receipt,contra,adjustment,opening,closing',
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'details' => 'required|array|min:2',
            'details.*.account_id' => 'required|exists:accounts,id',
            'details.*.dr_cr' => 'required|in:dr,cr',
            'details.*.amount' => 'required|numeric|min:0.01',
        ]);

        $entry = $this->financeService->createJournalEntry($request->all(), auth()->id());

        if (!$entry) {
            return $this->error('Entry is not balanced. Debit and Credit must be equal.', 422);
        }

        return $this->created($entry, 'Journal entry created successfully');
    }

    public function updateJournalEntry(Request $request, string $uuid): JsonResponse
    {
        $entry = $this->financeService->updateJournalEntry($uuid, $request->all());
        return $this->updated($entry, 'Journal entry updated successfully');
    }

    public function deleteJournalEntry(string $uuid): JsonResponse
    {
        $this->financeService->deleteJournalEntry($uuid);
        return $this->deleted('Journal entry deleted successfully');
    }

    public function postJournalEntry(string $uuid): JsonResponse
    {
        $this->financeService->postJournalEntry($uuid, auth()->id());
        return $this->success(null, 'Journal entry posted successfully');
    }

    public function approveJournalEntry(string $uuid): JsonResponse
    {
        $this->financeService->approveJournalEntry($uuid, auth()->id());
        return $this->success(null, 'Journal entry approved successfully');
    }

    // ===================== LEDGER =====================

    public function getLedger(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        $ledger = $this->financeService->getLedger(
            $request->input('account_id'),
            $request->input('date_from'),
            $request->input('date_to')
        );

        return $this->success($ledger);
    }

    public function getAccountSummary(string $uuid): JsonResponse
    {
        $summary = $this->financeService->getAccountSummary($uuid);
        return $this->success($summary);
    }

    // ===================== TRIAL BALANCE =====================

    public function getTrialBalance(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $report = $this->financeService->getTrialBalance($request->input('date'));
        return $this->success($report);
    }

    // ===================== PROFIT & LOSS =====================

    public function getProfitLoss(Request $request): JsonResponse
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        $report = $this->financeService->getProfitLoss(
            $request->input('date_from'),
            $request->input('date_to')
        );

        return $this->success($report);
    }

    // ===================== BALANCE SHEET =====================

    public function getBalanceSheet(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $report = $this->financeService->getBalanceSheet($request->input('date'));
        return $this->success($report);
    }

    // ===================== CASH & BANK BOOK =====================

    public function getCashBook(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        $report = $this->financeService->getCashBook(
            $request->input('account_id'),
            $request->input('date_from'),
            $request->input('date_to')
        );

        return $this->success($report);
    }

    public function getBankBook(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date',
        ]);

        $report = $this->financeService->getBankBook(
            $request->input('account_id'),
            $request->input('date_from'),
            $request->input('date_to')
        );

        return $this->success($report);
    }

    // ===================== FISCAL YEAR =====================

    public function getFiscalYears(): JsonResponse
    {
        $years = $this->financeService->getFiscalYears();
        return $this->success($years);
    }

    public function createFiscalYear(Request $request): JsonResponse
    {
        $year = $this->financeService->createFiscalYear($request->all());
        return $this->created($year, 'Fiscal year created successfully');
    }

    public function closeFiscalYear(string $uuid): JsonResponse
    {
        $this->financeService->closeFiscalYear($uuid, auth()->id());
        return $this->success(null, 'Fiscal year closed successfully');
    }

    // ===================== COST CENTERS =====================

    public function getCostCenters(): JsonResponse
    {
        $centers = $this->financeService->getCostCenters();
        return $this->success($centers);
    }

    public function createCostCenter(Request $request): JsonResponse
    {
        $center = $this->financeService->createCostCenter($request->all());
        return $this->created($center, 'Cost center created successfully');
    }

    // ===================== ASSETS =====================

    public function getAssets(Request $request): JsonResponse
    {
        $assets = $this->financeService->getAssets($request->input('per_page', 50));
        return $this->success($assets);
    }

    public function createAsset(Request $request): JsonResponse
    {
        $asset = $this->financeService->createAsset($request->all());
        return $this->created($asset, 'Asset created successfully');
    }

    public function calculateDepreciation(string $uuid): JsonResponse
    {
        $this->financeService->calculateDepreciation($uuid);
        return $this->success(null, 'Depreciation calculated successfully');
    }

    // ===================== BUDGETS =====================

    public function getBudgets(Request $request): JsonResponse
    {
        $budgets = $this->financeService->getBudgets($request->input('per_page', 50));
        return $this->success($budgets);
    }

    public function createBudget(Request $request): JsonResponse
    {
        $budget = $this->financeService->createBudget($request->all());
        return $this->created($budget, 'Budget created successfully');
    }

    // ===================== DASHBOARD =====================

    public function getDashboard(): JsonResponse
    {
        $dashboard = $this->financeService->getDashboard();
        return $this->success($dashboard);
    }

    // ===================== REPORTS =====================

    public function getIncomeReport(Request $request): JsonResponse
    {
        $report = $this->financeService->getIncomeReport(
            $request->input('date_from'),
            $request->input('date_to')
        );
        return $this->success($report);
    }

    public function getExpenseReport(Request $request): JsonResponse
    {
        $report = $this->financeService->getExpenseReport(
            $request->input('date_from'),
            $request->input('date_to')
        );
        return $this->success($report);
    }

    // ===================== EXPORT =====================

    public function exportReport(Request $request): JsonResponse
    {
        $request->validate([
            'report_type' => 'required|in:trial_balance,profit_loss,balance_sheet,ledger',
            'format' => 'required|in:excel,csv,pdf',
        ]);

        $url = $this->financeService->exportReport(
            $request->input('report_type'),
            $request->input('format'),
            $request->only(['date', 'date_from', 'date_to', 'account_id'])
        );

        return $this->success(['url' => $url], 'Export ready');
    }
}
