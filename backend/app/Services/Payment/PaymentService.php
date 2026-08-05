<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Payment\FeeCategory;
use App\Models\Payment\FeeStructure;
use App\Models\Payment\Invoice;
use App\Models\Payment\Payment;
use App\Models\Payment\Waiver;
use App\Models\Payment\Installment;
use App\Models\Payment\Refund;
use App\Models\Payment\Fine;
use App\Models\Student\Student;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    // ===================== FEE CATEGORIES =====================

    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return FeeCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function createCategory(array $data): FeeCategory
    {
        return FeeCategory::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'code' => $data['code'] ?? strtoupper(substr($data['name'], 0, 3)),
            'category_type' => $data['category_type'],
            'is_system' => false,
            'is_active' => true,
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    // ===================== FEE STRUCTURE =====================

    public function getStructures(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = FeeStructure::with(['category', 'session', 'academicLevel', 'program']);

        if (!empty($filters['category_id'])) {
            $category = FeeCategory::where('uuid', $filters['category_id'])->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if (!empty($filters['session_id'])) {
            $session = \App\Models\Academic\AcademicSession::where('uuid', $filters['session_id'])->first();
            if ($session) {
                $query->where('academic_session_id', $session->id);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createStructure(array $data): FeeStructure
    {
        return FeeStructure::create([
            'uuid' => (string) Str::uuid(),
            'category_id' => $this->getModelId(FeeCategory::class, $data['category_id']),
            'academic_session_id' => $this->getModelId(\App\Models\Academic\AcademicSession::class, $data['session_id']),
            'academic_level_id' => $this->getModelId(\App\Models\Academic\AcademicLevel::class, $data['academic_level_id']),
            'program_id' => $this->getModelId(\App\Models\Academic\Program::class, $data['program_id']),
            'semester_id' => $this->getModelId(\App\Models\Academic\Semester::class, $data['semester_id']),
            'name' => $data['name'],
            'amount' => $data['amount'],
            'frequency' => $data['frequency'] ?? FeeStructure::FREQ_ONE_TIME,
            'effective_date' => $data['effective_date'],
            'expiry_date' => $data['expiry_date'] ?? null,
            'is_mandatory' => $data['is_mandatory'] ?? true,
            'is_active' => true,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function updateStructure(string $uuid, array $data): FeeStructure
    {
        $structure = FeeStructure::where('uuid', $uuid)->firstOrFail();

        $structure->update(array_intersect_key($data, array_flip([
            'name', 'amount', 'frequency', 'effective_date', 'expiry_date', 'is_mandatory', 'is_active', 'description'
        ])));

        return $structure->fresh();
    }

    // ===================== INVOICES =====================

    public function getInvoices(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Invoice::with(['student.profile', 'category', 'session']);

        if (!empty($filters['student_id'])) {
            $student = Student::where('uuid', $filters['student_id'])->first();
            if ($student) {
                $query->where('student_id', $student->id);
            }
        }

        if (!empty($filters['category_id'])) {
            $category = FeeCategory::where('uuid', $filters['category_id'])->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('invoice_no', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getInvoice(string $uuid): Invoice
    {
        return Invoice::where('uuid', $uuid)
            ->with(['student.profile', 'category', 'session', 'payments', 'waivers'])
            ->firstOrFail();
    }

    public function createInvoice(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $student = Student::where('uuid', $data['student_id'])->firstOrFail();
            $category = FeeCategory::findOrFail($data['category_id']);

            $netAmount = $data['amount'] - ($data['discount_amount'] ?? 0);

            $invoice = Invoice::create([
                'uuid' => (string) Str::uuid(),
                'invoice_no' => Invoice::generateInvoiceNo(),
                'student_id' => $student->id,
                'category_id' => $category->id,
                'academic_session_id' => $student->admission_session_id,
                'semester_id' => $data['semester_id'] ?? null,
                'billing_month' => $data['billing_month'] ?? null,
                'billing_year' => $data['billing_year'] ?? null,
                'total_amount' => $data['amount'],
                'discount_amount' => $data['discount_amount'] ?? 0,
                'fine_amount' => 0,
                'waiver_amount' => 0,
                'net_amount' => $netAmount,
                'paid_amount' => 0,
                'due_amount' => $netAmount,
                'due_date' => $data['due_date'] ?? now()->addDays(15),
                'status' => Invoice::STATUS_PENDING,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => auth()->id(),
            ]);

            return $invoice;
        });
    }

    public function updateInvoice(string $uuid, array $data): Invoice
    {
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();

        $invoice->update(array_intersect_key($data, array_flip([
            'total_amount', 'discount_amount', 'due_date', 'status', 'remarks'
        ])));

        $invoice->net_amount = $invoice->total_amount - $invoice->discount_amount;
        $invoice->calculateDue();
        $invoice->save();

        return $invoice->fresh(['student.profile', 'category', 'payments']);
    }

    public function deleteInvoice(string $uuid): bool
    {
        $invoice = Invoice::where('uuid', $uuid)->firstOrFail();
        return $invoice->delete();
    }

    public function generateInvoices(string $sessionUuid, string $classUuid, array $categoryIds): array
    {
        $session = \App\Models\Academic\AcademicSession::where('uuid', $sessionUuid)->firstOrFail();
        $class = \App\Models\Academic\AcademicClass::where('uuid', $classUuid)->firstOrFail();

        $students = Student::where('admission_session_id', $session->id)
            ->where('class_id', $class->id)
            ->get();

        $results = ['total' => 0, 'created' => 0, 'errors' => []];

        foreach ($students as $student) {
            $results['total']++;

            foreach ($categoryIds as $categoryId) {
                $structure = FeeStructure::where('category_id', $categoryId)
                    ->where('academic_session_id', $session->id)
                    ->first();

                if ($structure && $structure->is_mandatory) {
                    try {
                        $this->createInvoice([
                            'student_id' => $student->uuid,
                            'category_id' => $categoryId,
                            'amount' => $structure->amount,
                            'due_date' => $structure->effective_date->addDays(15),
                        ]);
                        $results['created']++;
                    } catch (\Exception $e) {
                        $results['errors'][] = $e->getMessage();
                    }
                }
            }
        }

        return $results;
    }

    // ===================== PAYMENTS =====================

    public function getPayments(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Payment::with(['student.profile', 'invoice']);

        if (!empty($filters['student_id'])) {
            $student = Student::where('uuid', $filters['student_id'])->first();
            if ($student) {
                $query->where('student_id', $student->id);
            }
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['gateway_name'])) {
            $query->where('gateway_name', $filters['gateway_name']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function collectPayment(
        int $invoiceId,
        float $amount,
        string $method,
        ?string $transactionId,
        ?string $gatewayResponse,
        int $userId
    ): Payment {
        return DB::transaction(function () use ($invoiceId, $amount, $method, $transactionId, $gatewayResponse, $userId) {
            $invoice = Invoice::findOrFail($invoiceId);
            $student = $invoice->student;

            $payment = Payment::create([
                'uuid' => (string) Str::uuid(),
                'payment_no' => Payment::generatePaymentNo(),
                'receipt_no' => Payment::generateReceiptNo(),
                'invoice_id' => $invoiceId,
                'student_id' => $student->id,
                'amount' => $amount,
                'payment_type' => 'fee',
                'payment_method' => $method,
                'gateway_name' => in_array($method, ['bkash', 'nagad', 'rocket', 'sslcommerz']) ? $method : null,
                'transaction_id' => $transactionId,
                'gateway_response' => $gatewayResponse,
                'payment_date' => now(),
                'collected_by' => $userId,
                'collected_by_name' => auth()->user()->name,
                'status' => Payment::STATUS_PAID,
            ]);

            // Update invoice
            $invoice->paid_amount += $amount;
            $invoice->calculateDue();
            $invoice->save();

            return $payment;
        });
    }

    public function verifyPayment(string $uuid): void
    {
        $payment = Payment::where('uuid', $uuid)->firstOrFail();
        $payment->update(['status' => Payment::STATUS_PAID]);
    }

    public function getReceipt(string $uuid): array
    {
        $payment = Payment::where('uuid', $uuid)
            ->with(['student.profile', 'invoice', 'collector'])
            ->firstOrFail();

        return [
            'receipt_no' => $payment->receipt_no,
            'payment_no' => $payment->payment_no,
            'date' => $payment->payment_date->format('Y-m-d H:i'),
            'student' => [
                'name' => $payment->student?->profile?->full_name,
                'student_no' => $payment->student?->student_no,
            ],
            'invoice' => [
                'no' => $payment->invoice?->invoice_no,
            ],
            'amount' => $payment->amount,
            'amount_in_words' => $this->numberToWords($payment->amount),
            'payment_method' => $payment->payment_method,
            'transaction_id' => $payment->transaction_id,
            'collected_by' => $payment->collected_by_name,
        ];
    }

    // ===================== WAIVERS =====================

    public function applyWaiver(
        int $invoiceId,
        float $amount,
        string $type,
        string $reason,
        ?float $percentage,
        int $userId
    ): Waiver {
        return DB::transaction(function () use ($invoiceId, $amount, $type, $reason, $percentage, $userId) {
            $invoice = Invoice::findOrFail($invoiceId);

            $waiver = Waiver::create([
                'uuid' => (string) Str::uuid(),
                'invoice_id' => $invoiceId,
                'student_id' => $invoice->student_id,
                'waiver_type' => $type,
                'amount' => $amount,
                'percentage' => $percentage,
                'reason' => $reason,
                'approved_by' => $userId,
                'approved_at' => now(),
                'status' => 'approved',
            ]);

            // Update invoice
            $invoice->waiver_amount += $amount;
            $invoice->net_amount = $invoice->total_amount - $invoice->discount_amount - $invoice->waiver_amount;
            $invoice->calculateDue();
            $invoice->save();

            return $waiver;
        });
    }

    // ===================== INSTALLMENTS =====================

    public function createInstallmentPlan(string $studentUuid, float $totalAmount, array $installments): array
    {
        $student = Student::where('uuid', $studentUuid)->firstOrFail();

        $created = [];
        $installmentNo = 1;

        foreach ($installments as $item) {
            $installment = Installment::create([
                'uuid' => (string) Str::uuid(),
                'student_id' => $student->id,
                'installment_no' => $installmentNo++,
                'amount' => $item['amount'],
                'due_date' => $item['due_date'],
                'status' => Installment::STATUS_PENDING,
            ]);

            $created[] = $installment;
        }

        return [
            'total_amount' => $totalAmount,
            'installments' => $created,
        ];
    }

    // ===================== REFUNDS =====================

    public function requestRefund(int $paymentId, float $amount, string $reason, int $userId): Refund
    {
        $payment = Payment::findOrFail($paymentId);

        return Refund::create([
            'uuid' => (string) Str::uuid(),
            'refund_no' => Refund::generateRefundNo(),
            'payment_id' => $paymentId,
            'student_id' => $payment->student_id,
            'invoice_id' => $payment->invoice_id,
            'amount' => $amount,
            'reason' => $reason,
            'payment_method' => $payment->payment_method,
            'requested_by' => $userId,
            'requested_at' => now(),
            'status' => Refund::STATUS_PENDING,
        ]);
    }

    public function processRefund(string $uuid, string $status, ?string $method, int $userId): Refund
    {
        $refund = Refund::where('uuid', $uuid)->firstOrFail();

        $updateData = [
            'status' => $status === 'approved' ? Refund::STATUS_APPROVED : Refund::STATUS_REJECTED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ];

        if ($status === 'approved') {
            $updateData['refund_method'] = $method;
            $updateData['processed_by'] = $userId;
            $updateData['processed_at'] = now();
            $updateData['status'] = Refund::STATUS_COMPLETED;
        }

        $refund->update($updateData);

        return $refund->fresh();
    }

    // ===================== FINES =====================

    public function createFine(
        string $studentUuid,
        string $type,
        float $amount,
        string $reason,
        ?string $dueDate,
        int $userId
    ): Fine {
        $student = Student::where('uuid', $studentUuid)->firstOrFail();

        return Fine::create([
            'uuid' => (string) Str::uuid(),
            'student_id' => $student->id,
            'fine_type' => $type,
            'amount' => $amount,
            'reason' => $reason,
            'due_date' => $dueDate ?? now()->addDays(7),
            'status' => Fine::STATUS_PENDING,
            'created_by' => $userId,
        ]);
    }

    // ===================== LEDGER =====================

    public function getLedger(string $studentUuid): array
    {
        $student = Student::where('uuid', $studentUuid)->firstOrFail();

        $invoices = Invoice::where('student_id', $student->id)
            ->with('payments', 'waivers')
            ->orderBy('created_at')
            ->get();

        $entries = [];

        foreach ($invoices as $invoice) {
            $entries[] = [
                'date' => $invoice->created_at->format('Y-m-d'),
                'type' => 'invoice',
                'description' => 'Invoice #' . $invoice->invoice_no,
                'amount' => $invoice->net_amount,
                'balance' => 0,
            ];

            foreach ($invoice->payments as $payment) {
                $entries[] = [
                    'date' => $payment->payment_date->format('Y-m-d'),
                    'type' => 'payment',
                    'description' => 'Payment via ' . $payment->payment_method,
                    'amount' => -$payment->amount,
                    'balance' => 0,
                ];
            }

            foreach ($invoice->waivers as $waiver) {
                $entries[] = [
                    'date' => $waiver->approved_at?->format('Y-m-d'),
                    'type' => 'waiver',
                    'description' => 'Waiver - ' . $waiver->reason,
                    'amount' => -$waiver->amount,
                    'balance' => 0,
                ];
            }
        }

        // Calculate running balance
        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['amount'];
            $entry['balance'] = $balance;
        }

        return $entries;
    }

    // ===================== REPORTS =====================

    public function getCollectionReport(?string $dateFrom, ?string $dateTo, ?string $method, ?string $sessionId): array
    {
        $query = Payment::where('status', Payment::STATUS_PAID);

        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }

        if ($method) {
            $query->where('payment_method', $method);
        }

        $payments = $query->get();

        $total = $payments->sum('amount');
        $byMethod = $payments->groupBy('payment_method')->map->sum('amount');
        $daily = $payments->groupBy(fn($p) => $p->payment_date->format('Y-m-d'))->map->sum('amount');

        return [
            'total_collection' => $total,
            'by_method' => $byMethod,
            'daily' => $daily,
            'total_transactions' => $payments->count(),
        ];
    }

    public function getDueReport(?string $sessionId, ?string $classId): array
    {
        $query = Invoice::whereIn('status', [Invoice::STATUS_PENDING, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE]);

        if ($sessionId) {
            $session = \App\Models\Academic\AcademicSession::where('uuid', $sessionId)->first();
            if ($session) {
                $query->where('academic_session_id', $session->id);
            }
        }

        if ($classId) {
            $class = \App\Models\Academic\AcademicClass::where('uuid', $classId)->first();
            if ($class) {
                $query->whereHas('student', fn($q) => $q->where('class_id', $class->id));
            }
        }

        $invoices = $query->with('student.profile', 'category')->get();

        return [
            'total_due' => $invoices->sum('due_amount'),
            'total_invoices' => $invoices->count(),
            'by_category' => $invoices->groupBy(fn($i) => $i->category?->name)->map(fn($g) => [
                'count' => $g->count(),
                'amount' => $g->sum('due_amount'),
            ]),
            'overdue' => $invoices->filter(fn($i) => $i->due_date < now())->count(),
        ];
    }

    public function getDashboard(): array
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $todayCollection = Payment::where('status', Payment::STATUS_PAID)
            ->whereDate('payment_date', $today)
            ->sum('amount');

        $monthCollection = Payment::where('status', Payment::STATUS_PAID)
            ->whereDate('payment_date', '>=', $monthStart)
            ->sum('amount');

        $totalDue = Invoice::whereIn('status', [Invoice::STATUS_PENDING, Invoice::STATUS_PARTIAL, Invoice::STATUS_OVERDUE])
            ->sum('due_amount');

        $pendingInvoices = Invoice::where('status', Invoice::STATUS_PENDING)->count();
        $overdueInvoices = Invoice::where('status', Invoice::STATUS_OVERDUE)->count();

        return [
            'today_collection' => $todayCollection,
            'month_collection' => $monthCollection,
            'total_due' => $totalDue,
            'pending_invoices' => $pendingInvoices,
            'overdue_invoices' => $overdueInvoices,
        ];
    }

    // ===================== EXPORT =====================

    public function exportPayments(string $format, ?string $dateFrom, ?string $dateTo): string
    {
        $filename = "payments_" . now()->format('Ymd_His');
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

    private function numberToWords(float $number): string
    {
        $words = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        return $words[(int) $number] . ' Taka Only';
    }
}
