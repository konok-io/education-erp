<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Payment\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payment & Fee Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('payments')->middleware(['auth:sanctum'])->group(function () {

    // Fee Categories
    Route::get('/categories', [PaymentController::class, 'getCategories'])->name('payments.categories');
    Route::post('/categories', [PaymentController::class, 'createCategory'])->name('payments.categories.create');

    // Fee Structure
    Route::get('/structures', [PaymentController::class, 'getStructures'])->name('payments.structures');
    Route::post('/structures', [PaymentController::class, 'createStructure'])->name('payments.structures.create');
    Route::put('/structures/{uuid}', [PaymentController::class, 'updateStructure'])->name('payments.structures.update');

    // Invoices
    Route::get('/invoices', [PaymentController::class, 'getInvoices'])->name('payments.invoices');
    Route::post('/invoices', [PaymentController::class, 'createInvoice'])->name('payments.invoices.create');
    Route::get('/invoices/{uuid}', [PaymentController::class, 'getInvoice'])->name('payments.invoices.show');
    Route::put('/invoices/{uuid}', [PaymentController::class, 'updateInvoice'])->name('payments.invoices.update');
    Route::delete('/invoices/{uuid}', [PaymentController::class, 'deleteInvoice'])->name('payments.invoices.delete');
    Route::post('/invoices/generate', [PaymentController::class, 'generateInvoices'])->name('payments.invoices.generate');

    // Payments
    Route::get('/', [PaymentController::class, 'getPayments'])->name('payments.index');
    Route::post('/', [PaymentController::class, 'collectPayment'])->name('payments.collect');
    Route::put('/{uuid}/verify', [PaymentController::class, 'verifyPayment'])->name('payments.verify');
    Route::get('/receipt/{uuid}', [PaymentController::class, 'getReceipt'])->name('payments.receipt');

    // Waivers
    Route::post('/waivers', [PaymentController::class, 'applyWaiver'])->name('payments.waivers.apply');

    // Installments
    Route::post('/installments', [PaymentController::class, 'createInstallmentPlan'])->name('payments.installments.create');

    // Refunds
    Route::post('/refunds', [PaymentController::class, 'requestRefund'])->name('payments.refunds.request');
    Route::put('/refunds/{uuid}', [PaymentController::class, 'processRefund'])->name('payments.refunds.process');

    // Fines
    Route::post('/fines', [PaymentController::class, 'createFine'])->name('payments.fines.create');

    // Ledger
    Route::get('/ledger', [PaymentController::class, 'getLedger'])->name('payments.ledger');

    // Reports
    Route::get('/reports/collection', [PaymentController::class, 'getCollectionReport'])->name('payments.reports.collection');
    Route::get('/reports/due', [PaymentController::class, 'getDueReport'])->name('payments.reports.due');
    Route::get('/reports/dashboard', [PaymentController::class, 'getDashboard'])->name('payments.dashboard');

    // Export
    Route::get('/export', [PaymentController::class, 'exportPayments'])->name('payments.export');
});
