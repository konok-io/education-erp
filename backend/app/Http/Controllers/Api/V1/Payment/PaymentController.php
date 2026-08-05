<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\BaseController;
use App\Http\Resources\Payment\InvoiceResource;
use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends BaseController
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {}

    // ===================== FEE CATEGORIES =====================

    public function getCategories(): JsonResponse
    {
        $categories = $this->paymentService->getCategories();
        return $this->success($categories);
    }

    public function createCategory(Request $request): JsonResponse
    {
        $category = $this->paymentService->createCategory($request->all());
        return $this->created($category, 'Category created');
    }

    // ===================== FEE STRUCTURE =====================

    public function getStructures(Request $request): AnonymousResourceCollection
    {
        $structures = $this->paymentService->getStructures(
            $request->input('per_page', 50),
            $request->only(['category_id', 'session_id', 'academic_level_id'])
        );
        return InvoiceResource::collection($structures);
    }

    public function createStructure(Request $request): JsonResponse
    {
        $structure = $this->paymentService->createStructure($request->all());
        return $this->created($structure, 'Fee structure created');
    }

    public function updateStructure(Request $request, string $uuid): JsonResponse
    {
        $structure = $this->paymentService->updateStructure($uuid, $request->all());
        return $this->updated($structure, 'Fee structure updated');
    }

    // ===================== INVOICES =====================

    public function getInvoices(Request $request): AnonymousResourceCollection
    {
        $invoices = $this->paymentService->getInvoices(
            $request->input('per_page', 50),
            $request->only([
                'student_id', 'category_id', 'session_id', 'status',
                'date_from', 'date_to', 'search'
            ])
        );
        return InvoiceResource::collection($invoices);
    }

    public function getInvoice(string $uuid): JsonResponse
    {
        $invoice = $this->paymentService->getInvoice($uuid);
        return $this->success(new InvoiceResource($invoice));
    }

    public function createInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'category_id' => 'required|exists:fee_categories,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $invoice = $this->paymentService->createInvoice($request->all());
        return $this->created(new InvoiceResource($invoice), 'Invoice created');
    }

    public function updateInvoice(Request $request, string $uuid): JsonResponse
    {
        $invoice = $this->paymentService->updateInvoice($uuid, $request->all());
        return $this->updated(new InvoiceResource($invoice), 'Invoice updated');
    }

    public function deleteInvoice(string $uuid): JsonResponse
    {
        $this->paymentService->deleteInvoice($uuid);
        return $this->deleted('Invoice deleted');
    }

    public function generateInvoices(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:classes,id',
            'category_ids' => 'required|array',
        ]);

        $result = $this->paymentService->generateInvoices(
            $request->input('session_id'),
            $request->input('class_id'),
            $request->input('category_ids')
        );

        return $this->success($result, 'Invoices generated');
    }

    // ===================== PAYMENTS =====================

    public function getPayments(Request $request): AnonymousResourceCollection
    {
        $payments = $this->paymentService->getPayments(
            $request->input('per_page', 50),
            $request->only([
                'student_id', 'invoice_id', 'payment_method', 'status',
                'date_from', 'date_to', 'gateway_name'
            ])
        );
        return InvoiceResource::collection($payments);
    }

    public function collectPayment(Request $request): JsonResponse
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $payment = $this->paymentService->collectPayment(
            $request->input('invoice_id'),
            $request->input('amount'),
            $request->input('payment_method'),
            $request->input('transaction_id'),
            $request->input('gateway_response'),
            auth()->id()
        );

        return $this->success($payment, 'Payment collected');
    }

    public function verifyPayment(string $uuid): JsonResponse
    {
        $this->paymentService->verifyPayment($uuid);
        return $this->success(null, 'Payment verified');
    }

    public function getReceipt(string $uuid): JsonResponse
    {
        $receipt = $this->paymentService->getReceipt($uuid);
        return $this->this->success($receipt);
    }

    // ===================== WAIVERS =====================

    public function applyWaiver(Request $request): JsonResponse
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'waiver_type' => 'required|string',
            'reason' => 'required|string',
        ]);

        $waiver = $this->paymentService->applyWaiver(
            $request->input('invoice_id'),
            $request->input('amount'),
            $request->input('waiver_type'),
            $request->input('reason'),
            $request->input('percentage'),
            auth()->id()
        );

        return $this->success($waiver, 'Waiver applied');
    }

    // ===================== INSTALLMENTS =====================

    public function createInstallmentPlan(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'total_amount' => 'required|numeric|min:0',
            'installments' => 'required|array',
            'installments.*.amount' => 'required|numeric',
            'installments.*.due_date' => 'required|date',
        ]);

        $plan = $this->paymentService->createInstallmentPlan(
            $request->input('student_id'),
            $request->input('total_amount'),
            $request->input('installments')
        );

        return $this->success($plan, 'Installment plan created');
    }

    // ===================== REFUNDS =====================

    public function requestRefund(Request $request): JsonResponse
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
        ]);

        $refund = $this->paymentService->requestRefund(
            $request->input('payment_id'),
            $request->input('amount'),
            $request->input('reason'),
            auth()->id()
        );

        return $this->created($refund, 'Refund requested');
    }

    public function processRefund(Request $request, string $uuid): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'refund_method' => 'nullable|string',
        ]);

        $refund = $this->paymentService->processRefund(
            $uuid,
            $request->input('status'),
            $request->input('refund_method'),
            auth()->id()
        );

        return $this->success($refund, 'Refund processed');
    }

    // ===================== FINES =====================

    public function createFine(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fine_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
        ]);

        $fine = $this->paymentService->createFine(
            $request->input('student_id'),
            $request->input('fine_type'),
            $request->input('amount'),
            $request->input('reason'),
            $request->input('due_date'),
            auth()->id()
        );

        return $this->created($fine, 'Fine created');
    }

    // ===================== LEDGER =====================

    public function getLedger(Request $request): JsonResponse
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $ledger = $this->paymentService->getLedger($request->input('student_id'));
        return $this->success($ledger);
    }

    // ===================== REPORTS =====================

    public function getCollectionReport(Request $request): JsonResponse
    {
        $report = $this->paymentService->getCollectionReport(
            $request->input('date_from'),
            $request->input('date_to'),
            $request->input('payment_method'),
            $request->input('session_id')
        );

        return $this->success($report);
    }

    public function getDueReport(Request $request): JsonResponse
    {
        $report = $this->paymentService->getDueReport(
            $request->input('session_id'),
            $request->input('class_id')
        );

        return $this->success($report);
    }

    public function getDashboard(): JsonResponse
    {
        $dashboard = $this->paymentService->getDashboard();
        return $this->success($dashboard);
    }

    // ===================== EXPORT =====================

    public function exportPayments(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:excel,csv,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $url = $this->paymentService->exportPayments(
            $request->input('format'),
            $request->input('date_from'),
            $request->input('date_to')
        );

        return $this->success(['url' => $url], 'Export ready');
    }
}
