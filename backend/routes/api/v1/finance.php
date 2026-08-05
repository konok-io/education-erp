<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Finance\FinanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Finance & Accounting Routes
|--------------------------------------------------------------------------
*/

Route::prefix('finance')->middleware(['auth:sanctum'])->group(function () {

    // Accounts
    Route::get('/accounts', [FinanceController::class, 'getAccounts'])->name('finance.accounts');
    Route::post('/accounts', [FinanceController::class, 'createAccount'])->name('finance.accounts.create');
    Route::put('/accounts/{uuid}', [FinanceController::class, 'updateAccount'])->name('finance.accounts.update');
    Route::delete('/accounts/{uuid}', [FinanceController::class, 'deleteAccount'])->name('finance.accounts.delete');

    // Journal Entries
    Route::get('/journal', [FinanceController::class, 'getJournalEntries'])->name('finance.journal');
    Route::post('/journal', [FinanceController::class, 'createJournalEntry'])->name('finance.journal.create');
    Route::put('/journal/{uuid}', [FinanceController::class, 'updateJournalEntry'])->name('finance.journal.update');
    Route::delete('/journal/{uuid}', [FinanceController::class, 'deleteJournalEntry'])->name('finance.journal.delete');
    Route::post('/journal/{uuid}/post', [FinanceController::class, 'postJournalEntry'])->name('finance.journal.post');
    Route::post('/journal/{uuid}/approve', [FinanceController::class, 'approveJournalEntry'])->name('finance.journal.approve');

    // Ledger
    Route::get('/ledger', [FinanceController::class, 'getLedger'])->name('finance.ledger');
    Route::get('/accounts/{uuid}/summary', [FinanceController::class, 'getAccountSummary'])->name('finance.accounts.summary');

    // Reports
    Route::get('/reports/trial-balance', [FinanceController::class, 'getTrialBalance'])->name('finance.reports.trial-balance');
    Route::get('/reports/profit-loss', [FinanceController::class, 'getProfitLoss'])->name('finance.reports.profit-loss');
    Route::get('/reports/balance-sheet', [FinanceController::class, 'getBalanceSheet'])->name('finance.reports.balance-sheet');
    Route::get('/reports/cash-book', [FinanceController::class, 'getCashBook'])->name('finance.reports.cash-book');
    Route::get('/reports/bank-book', [FinanceController::class, 'getBankBook'])->name('finance.reports.bank-book');
    Route::get('/reports/income', [FinanceController::class, 'getIncomeReport'])->name('finance.reports.income');
    Route::get('/reports/expense', [FinanceController::class, 'getExpenseReport'])->name('finance.reports.expense');

    // Fiscal Year
    Route::get('/fiscal-years', [FinanceController::class, 'getFiscalYears'])->name('finance.fiscal-years');
    Route::post('/fiscal-years', [FinanceController::class, 'createFiscalYear'])->name('finance.fiscal-years.create');
    Route::post('/fiscal-years/{uuid}/close', [FinanceController::class, 'closeFiscalYear'])->name('finance.fiscal-years.close');

    // Cost Centers
    Route::get('/cost-centers', [FinanceController::class, 'getCostCenters'])->name('finance.cost-centers');
    Route::post('/cost-centers', [FinanceController::class, 'createCostCenter'])->name('finance.cost-centers.create');

    // Assets
    Route::get('/assets', [FinanceController::class, 'getAssets'])->name('finance.assets');
    Route::post('/assets', [FinanceController::class, 'createAsset'])->name('finance.assets.create');
    Route::post('/assets/{uuid}/depreciation', [FinanceController::class, 'calculateDepreciation'])->name('finance.assets.depreciation');

    // Budgets
    Route::get('/budgets', [FinanceController::class, 'getBudgets'])->name('finance.budgets');
    Route::post('/budgets', [FinanceController::class, 'createBudget'])->name('finance.budgets.create');

    // Dashboard
    Route::get('/dashboard', [FinanceController::class, 'getDashboard'])->name('finance.dashboard');

    // Export
    Route::get('/export', [FinanceController::class, 'exportReport'])->name('finance.export');
});
