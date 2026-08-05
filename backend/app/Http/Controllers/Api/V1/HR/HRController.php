<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\HR;

use App\Http\Controllers\BaseController;
use App\Services\HR\HRService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HRController extends BaseController
{
    public function __construct(
        private readonly HRService $hrService
    ) {}

    // ===================== SALARY GRADES =====================

    public function getSalaryGrades(): JsonResponse
    {
        $grades = $this->hrService->getSalaryGrades();
        return $this->success($grades);
    }

    public function createSalaryGrade(Request $request): JsonResponse
    {
        $grade = $this->hrService->createSalaryGrade($request->all());
        return $this->created($grade, 'Salary grade created');
    }

    // ===================== PAYROLL =====================

    public function getPayrolls(Request $request): AnonymousResourceCollection
    {
        $payrolls = $this->hrService->getPayrolls(
            $request->input('per_page', 50),
            $request->only(['employee_id', 'month', 'year', 'status'])
        );
        return \App\Http\Resources\HR\PayrollResource::collection($payrolls);
    }

    public function processPayroll(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $payroll = $this->hrService->processPayroll(
            $request->input('employee_id'),
            $request->input('month'),
            $request->input('year'),
            auth()->id()
        );

        return $this->created($payroll, 'Payroll processed');
    }

    public function processBulkPayroll(Request $request): JsonResponse
    {
        $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $result = $this->hrService->processBulkPayroll(
            $request->input('department_id'),
            $request->input('month'),
            $request->input('year'),
            auth()->id()
        );

        return $this->success($result, 'Bulk payroll processed');
    }

    public function approvePayroll(string $uuid): JsonResponse
    {
        $this->hrService->approvePayroll($uuid, auth()->id());
        return $this->success(null, 'Payroll approved');
    }

    public function payPayroll(string $uuid): JsonResponse
    {
        $this->hrService->payPayroll($uuid, auth()->id());
        return $this->success(null, 'Payroll marked as paid');
    }

    public function getPayslip(string $uuid): JsonResponse
    {
        $payslip = $this->hrService->getPayslip($uuid);
        return $this->success($payslip);
    }

    // ===================== LEAVE TYPES =====================

    public function getLeaveTypes(): JsonResponse
    {
        $types = $this->hrService->getLeaveTypes();
        return $this->success($types);
    }

    public function createLeaveType(Request $request): JsonResponse
    {
        $type = $this->hrService->createLeaveType($request->all());
        return $this->created($type, 'Leave type created');
    }

    // ===================== LEAVES =====================

    public function getLeaves(Request $request): AnonymousResourceCollection
    {
        $leaves = $this->hrService->getLeaves(
            $request->input('per_page', 50),
            $request->only(['employee_id', 'leave_type_id', 'status', 'date_from', 'date_to'])
        );
        return \App\Http\Resources\HR\LeaveResource::collection($leaves);
    }

    public function applyLeave(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $leave = $this->hrService->applyLeave(
            $request->input('employee_id'),
            $request->input('leave_type_id'),
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('reason'),
            auth()->id()
        );

        return $this->created($leave, 'Leave applied');
    }

    public function approveLeave(string $uuid): JsonResponse
    {
        $this->hrService->approveLeave($uuid, auth()->id());
        return $this->success(null, 'Leave approved');
    }

    public function rejectLeave(Request $request, string $uuid): JsonResponse
    {
        $request->validate(['reason' => 'required|string']);
        $this->hrService->rejectLeave($uuid, $request->input('reason'), auth()->id());
        return $this->success(null, 'Leave rejected');
    }

    public function getLeaveBalance(string $employeeId): JsonResponse
    {
        $balance = $this->hrService->getLeaveBalance($employeeId);
        return $this->success($balance);
    }

    // ===================== HOLIDAYS =====================

    public function getHolidays(Request $request): JsonResponse
    {
        $holidays = $this->hrService->getHolidays(
            $request->input('year', now()->year)
        );
        return $this->success($holidays);
    }

    public function createHoliday(Request $request): JsonResponse
    {
        $holiday = $this->hrService->createHoliday($request->all());
        return $this->created($holiday, 'Holiday created');
    }

    // ===================== LOANS =====================

    public function getLoans(Request $request): AnonymousResourceCollection
    {
        $loans = $this->hrService->getLoans(
            $request->input('per_page', 50),
            $request->only(['employee_id', 'status', 'loan_type'])
        );
        return \App\Http\Resources\HR\LoanResource::collection($loans);
    }

    public function createLoan(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'principal_amount' => 'required|numeric|min:0',
            'loan_type' => 'required|string',
        ]);

        $loan = $this->hrService->createLoan(
            $request->input('employee_id'),
            $request->input('loan_type'),
            $request->input('principal_amount'),
            $request->input('interest_rate', 0),
            $request->input('installment_count', 12),
            $request->input('purpose'),
            auth()->id()
        );

        return $this->created($loan, 'Loan created');
    }

    public function approveLoan(string $uuid): JsonResponse
    {
        $this->hrService->approveLoan($uuid, auth()->id());
        return $this->success(null, 'Loan approved');
    }

    public function getLoanBalance(string $employeeId): JsonResponse
    {
        $balance = $this->hrService->getLoanBalance($employeeId);
        return $this->success($balance);
    }

    // ===================== OVERTIME =====================

    public function getOvertimes(Request $request): AnonymousResourceCollection
    {
        $overtimes = $this->hrService->getOvertimes(
            $request->input('per_page', 50),
            $request->only(['employee_id', 'month', 'year', 'status'])
        );
        return \App\Http\Resources\HR\OvertimeResource::collection($overtimes);
    }

    public function createOvertime(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'overtime_date' => 'required|date',
            'hours' => 'required|numeric|min:0.5',
            'overtime_type' => 'required|string',
        ]);

        $overtime = $this->hrService->createOvertime(
            $request->input('employee_id'),
            $request->input('overtime_date'),
            $request->input('hours'),
            $request->input('overtime_type'),
            $request->input('reason'),
            auth()->id()
        );

        return $this->created($overtime, 'Overtime recorded');
    }

    public function approveOvertime(string $uuid): JsonResponse
    {
        $this->hrService->approveOvertime($uuid, auth()->id());
        return $this->success(null, 'Overtime approved');
    }

    // ===================== REPORTS =====================

    public function getPayrollReport(Request $request): JsonResponse
    {
        $report = $this->hrService->getPayrollReport(
            $request->input('month'),
            $request->input('year'),
            $request->input('department_id')
        );
        return $this->success($report);
    }

    public function getLeaveReport(Request $request): JsonResponse
    {
        $report = $this->hrService->getLeaveReport(
            $request->input('year'),
            $request->input('department_id')
        );
        return $this->success($report);
    }

    public function getDashboard(): JsonResponse
    {
        $dashboard = $this->hrService->getDashboard();
        return $this->success($dashboard);
    }

    // ===================== EXPORT =====================

    public function exportPayslips(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|integer',
            'year' => 'required|integer',
            'format' => 'required|in:pdf,excel',
        ]);

        $url = $this->hrService->exportPayslips(
            $request->input('month'),
            $request->input('year'),
            $request->input('format')
        );

        return $this->success(['url' => $url], 'Export ready');
    }
}
