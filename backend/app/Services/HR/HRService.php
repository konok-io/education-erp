<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\SalaryGrade;
use App\Models\HR\SalaryStructure;
use App\Models\HR\Payroll;
use App\Models\HR\PayrollDetail;
use App\Models\HR\LeaveType;
use App\Models\HR\Leave;
use App\Models\HR\Holiday;
use App\Models\HR\Loan;
use App\Models\HR\LoanRepayment;
use App\Models\HR\OvertimeRecord;
use App\Models\HR\AdvanceSalary;
use App\Models\HR\Bonus;
use App\Models\HR\Increment;
use App\Models\HR\Promotion;
use App\Models\HR\EmployeeExit;
use App\Models\HR\ProvidentFund;
use App\Models\HR\PFContribution;
use App\Models\HR\TaxSlab;
use App\Models\HR\EmployeeTaxRecord;
use App\Models\HR\EmployeeAttendance;
use App\Models\HR\EmployeeLeaveBalance;
use App\Models\Employee\Employee;
use App\Models\Attendance\Attendance;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HRService
{
    // ===================== SALARY GRADES =====================

    public function getSalaryGrades(): \Illuminate\Database\Eloquent\Collection
    {
        return SalaryGrade::where('is_active', true)->orderBy('basic_salary', 'asc')->get();
    }

    public function createSalaryGrade(array $data): SalaryGrade
    {
        return SalaryGrade::create([
            'uuid' => (string) Str::uuid(),
            'grade_name' => $data['grade_name'],
            'basic_salary' => $data['basic_salary'],
            'house_rent_percent' => $data['house_rent_percent'] ?? 30,
            'medical_percent' => $data['medical_percent'] ?? 10,
            'transport_percent' => $data['transport_percent'] ?? 10,
            'mobile_allowance' => $data['mobile_allowance'] ?? 0,
            'special_allowance' => $data['special_allowance'] ?? 0,
            'other_allowance' => $data['other_allowance'] ?? 0,
            'provident_fund_percent' => $data['provident_fund_percent'] ?? 10,
            'tax_percent' => $data['tax_percent'] ?? 0,
            'is_active' => true,
            'description' => $data['description'] ?? null,
        ]);
    }

    // ===================== PAYROLL =====================

    public function getPayrolls(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Payroll::with(['employee.profile', 'employee.department', 'employee.designation']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['month'])) {
            $query->where('payroll_month', $filters['month']);
        }

        if (!empty($filters['year'])) {
            $query->where('payroll_year', $filters['year']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('payroll_year', 'desc')
            ->orderBy('payroll_month', 'desc')
            ->paginate($perPage);
    }

    public function processPayroll(int $employeeId, int $month, int $year, int $userId): Payroll
    {
        return DB::transaction(function () use ($employeeId, $month, $year, $userId) {
            $employee = Employee::with(['salaryGrade', 'profile'])->findOrFail($employeeId);
            $grade = $employee->salaryGrade;

            if (!$grade) {
                throw new \Exception('Employee salary grade not found');
            }

            // Check if already processed
            $existing = Payroll::where('employee_id', $employeeId)
                ->where('payroll_month', $month)
                ->where('payroll_year', $year)
                ->first();

            if ($existing) {
                throw new \Exception('Payroll already processed for this month');
            }

            // Calculate attendance
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            
            $workingDays = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
                ->where('employee_id', $employeeId)
                ->count();

            $presentDays = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
                ->where('employee_id', $employeeId)
                ->where('status', 'present')
                ->count();

            $absentDays = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
                ->where('employee_id', $employeeId)
                ->where('status', 'absent')
                ->count();

            // Calculate salary
            $basicSalary = (float) $grade->basic_salary;
            $dailyRate = $basicSalary / ($workingDays ?: 30);
            
            // Adjust for absences
            $adjustedBasic = $basicSalary - ($dailyRate * $absentDays);

            $houseRent = $adjustedBasic * ($grade->house_rent_percent / 100);
            $medical = $adjustedBasic * ($grade->medical_percent / 100);
            $transport = $adjustedBasic * ($grade->transport_percent / 100);
            
            $grossSalary = $adjustedBasic + $houseRent + $medical + $transport 
                + $grade->mobile_allowance + $grade->special_allowance + $grade->other_allowance;

            // Calculate deductions
            $pfAmount = $grossSalary * ($grade->provident_fund_percent / 100);
            $taxAmount = $grossSalary * ($grade->tax_percent / 100);

            // Get overtime
            $overtimeAmount = OvertimeRecord::where('employee_id', $employeeId)
                ->whereMonth('overtime_date', $month)
                ->whereYear('overtime_date', $year)
                ->where('status', OvertimeRecord::STATUS_APPROVED)
                ->sum('amount');

            // Get loan deduction
            $loanDeduction = LoanRepayment::where('loan_id', function ($q) use ($employeeId) {
                    $q->select('id')->from('loans')
                    ->where('employee_id', $employeeId)
                    ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_APPROVED]);
                })
                ->where('payment_month', $month)
                ->where('payment_year', $year)
                ->where('status', LoanRepayment::STATUS_PENDING)
                ->sum('amount');

            // Get advance deduction
            $advanceDeduction = 0;

            $totalDeduction = $pfAmount + $taxAmount + $loanDeduction + $advanceDeduction;
            
            $netSalary = $grossSalary + $overtimeAmount - $totalDeduction;

            // Create payroll
            $payroll = Payroll::create([
                'uuid' => (string) Str::uuid(),
                'payroll_no' => Payroll::generatePayrollNo($month, $year),
                'employee_id' => $employeeId,
                'payroll_month' => $month,
                'payroll_year' => $year,
                'basic_salary' => $adjustedBasic,
                'gross_salary' => $grossSalary,
                'total_allowance' => $houseRent + $medical + $transport 
                    + $grade->mobile_allowance + $grade->special_allowance + $grade->other_allowance,
                'total_deduction' => $totalDeduction - $pfAmount - $taxAmount,
                'tax_amount' => $taxAmount,
                'pf_amount' => $pfAmount,
                'loan_deduction' => $loanDeduction,
                'advance_deduction' => $advanceDeduction,
                'overtime_amount' => $overtimeAmount,
                'bonus_amount' => 0,
                'net_salary' => $netSalary,
                'working_days' => $workingDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => 0,
                'status' => Payroll::STATUS_PROCESSED,
                'processed_by' => $userId,
                'processed_at' => now(),
            ]);

            // Create payroll details
            $details = [
                ['type' => 'basic', 'name' => 'Basic Salary', 'amount' => $adjustedBasic, 'earning' => true],
                ['type' => 'house_rent', 'name' => 'House Rent', 'amount' => $houseRent, 'earning' => true],
                ['type' => 'medical', 'name' => 'Medical', 'amount' => $medical, 'earning' => true],
                ['type' => 'transport', 'name' => 'Transport', 'amount' => $transport, 'earning' => true],
                ['type' => 'mobile', 'name' => 'Mobile', 'amount' => $grade->mobile_allowance, 'earning' => true],
                ['type' => 'special', 'name' => 'Special', 'amount' => $grade->special_allowance, 'earning' => true],
                ['type' => 'overtime', 'name' => 'Overtime', 'amount' => $overtimeAmount, 'earning' => true],
                ['type' => 'pf', 'name' => 'PF', 'amount' => $pfAmount, 'earning' => false],
                ['type' => 'tax', 'name' => 'Tax', 'amount' => $taxAmount, 'earning' => false],
                ['type' => 'loan', 'name' => 'Loan', 'amount' => $loanDeduction, 'earning' => false],
            ];

            foreach ($details as $detail) {
                if ($detail['amount'] > 0) {
                    PayrollDetail::create([
                        'uuid' => (string) Str::uuid(),
                        'payroll_id' => $payroll->id,
                        'component_type' => $detail['type'],
                        'component_name' => $detail['name'],
                        'amount' => $detail['amount'],
                        'is_earning' => $detail['earning'],
                        'is_taxable' => $detail['earning'],
                    ]);
                }
            }

            return $payroll->load('details');
        });
    }

    public function processBulkPayroll(?int $departmentId, int $month, int $year, int $userId): array
    {
        $query = Employee::where('employment_status', 'active');
        
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $employees = $query->get();
        $results = ['total' => 0, 'processed' => 0, 'errors' => []];

        foreach ($employees as $employee) {
            $results['total']++;
            try {
                $this->processPayroll($employee->id, $month, $year, $userId);
                $results['processed']++;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'employee_id' => $employee->uuid,
                    'name' => $employee->profile?->full_name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    public function approvePayroll(string $uuid, int $userId): void
    {
        $payroll = Payroll::where('uuid', $uuid)->firstOrFail();
        $payroll->update([
            'status' => Payroll::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function payPayroll(string $uuid, int $userId): void
    {
        $payroll = Payroll::where('uuid', $uuid)->firstOrFail();
        $payroll->update([
            'status' => Payroll::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    public function getPayslip(string $uuid): array
    {
        $payroll = Payroll::where('uuid', $uuid)
            ->with(['employee.profile', 'employee.department', 'employee.designation', 'details'])
            ->firstOrFail();

        return [
            'employee' => [
                'name' => $payroll->employee?->profile?->full_name,
                'employee_no' => $payroll->employee?->employee_no,
                'department' => $payroll->employee?->department?->name,
                'designation' => $payroll->employee?->designation?->name,
            ],
            'payroll' => [
                'no' => $payroll->payroll_no,
                'month' => $payroll->payroll_month,
                'year' => $payroll->payroll_year,
            ],
            'earnings' => $payroll->details->where('is_earning', true)->map(fn($d) => [
                'name' => $d->component_name,
                'amount' => $d->amount,
            ]),
            'deductions' => $payroll->details->where('is_earning', false)->map(fn($d) => [
                'name' => $d->component_name,
                'amount' => $d->amount,
            ]),
            'totals' => [
                'gross' => $payroll->gross_salary,
                'total_allowance' => $payroll->total_allowance,
                'total_deduction' => $payroll->total_deduction + $payroll->tax_amount + $payroll->pf_amount,
                'net' => $payroll->net_salary,
            ],
            'net_in_words' => $this->numberToWords($payroll->net_salary),
            'attendance' => [
                'working_days' => $payroll->working_days,
                'present_days' => $payroll->present_days,
                'absent_days' => $payroll->absent_days,
            ],
        ];
    }

    // ===================== LEAVE TYPES =====================

    public function getLeaveTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return LeaveType::where('is_active', true)->get();
    }

    public function createLeaveType(array $data): LeaveType
    {
        return LeaveType::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'code' => $data['code'],
            'short_code' => $data['short_code'] ?? substr($data['code'], 0, 3),
            'leave_days' => $data['leave_days'],
            'is_paid' => $data['is_paid'] ?? true,
            'is_encashable' => $data['is_encashable'] ?? false,
            'is_carry_forward' => $data['is_carry_forward'] ?? false,
            'max_consecutive_days' => $data['max_consecutive_days'] ?? 0,
            'max_carry_forward_days' => $data['max_carry_forward_days'] ?? 0,
            'requires_approval' => $data['requires_approval'] ?? true,
            'is_active' => true,
            'description' => $data['description'] ?? null,
        ]);
    }

    // ===================== LEAVES =====================

    public function getLeaves(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Leave::with(['employee.profile', 'leaveType']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('end_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function applyLeave(int $employeeId, int $leaveTypeId, string $startDate, string $endDate, string $reason, int $userId): Leave
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        $totalDays = $start->diffInDays($end) + 1;

        return Leave::create([
            'uuid' => (string) Str::uuid(),
            'leave_no' => Leave::generateLeaveNo(),
            'employee_id' => $employeeId,
            'leave_type_id' => $leaveTypeId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'reason' => $reason,
            'status' => Leave::STATUS_PENDING,
            'applied_by' => $userId,
            'applied_at' => now(),
        ]);
    }

    public function approveLeave(string $uuid, int $userId): void
    {
        $leave = Leave::where('uuid', $uuid)->firstOrFail();
        $leave->approve($userId);
    }

    public function rejectLeave(string $uuid, string $reason, int $userId): void
    {
        $leave = Leave::where('uuid', $uuid)->firstOrFail();
        $leave->reject($userId, $reason);
    }

    public function getLeaveBalance(string $employeeUuid): array
    {
        $employee = Employee::where('uuid', $employeeUuid)->firstOrFail();
        $leaveTypes = $this->getLeaveTypes();
        
        $balance = [];
        
        foreach ($leaveTypes as $type) {
            $used = Leave::where('employee_id', $employee->id)
                ->where('leave_type_id', $type->id)
                ->whereYear('start_date', now()->year)
                ->whereIn('status', [Leave::STATUS_APPROVED])
                ->sum('total_days');

            $pending = Leave::where('employee_id', $employee->id)
                ->where('leave_type_id', $type->id)
                ->whereYear('start_date', now()->year)
                ->where('status', Leave::STATUS_PENDING)
                ->sum('total_days');

            $balance[] = [
                'type' => $type->name,
                'code' => $type->code,
                'total' => $type->leave_days,
                'used' => $used,
                'pending' => $pending,
                'remaining' => $type->leave_days - $used - $pending,
            ];
        }

        return $balance;
    }

    // ===================== HOLIDAYS =====================

    public function getHolidays(int $year = null): \Illuminate\Database\Eloquent\Collection
    {
        $year = $year ?? now()->year;
        
        return Holiday::whereYear('holiday_date', $year)
            ->orWhere('is_repeating', true)
            ->orderBy('holiday_date')
            ->get();
    }

    public function createHoliday(array $data): Holiday
    {
        return Holiday::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'name_bn' => $data['name_bn'] ?? null,
            'holiday_date' => $data['holiday_date'],
            'holiday_type' => $data['holiday_type'],
            'is_repeating' => $data['is_repeating'] ?? false,
            'is_active' => true,
            'description' => $data['description'] ?? null,
        ]);
    }

    // ===================== LOANS =====================

    public function getLoans(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Loan::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['loan_type'])) {
            $query->where('loan_type', $filters['loan_type']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createLoan(
        int $employeeId,
        string $loanType,
        float $principalAmount,
        float $interestRate,
        int $installmentCount,
        ?string $purpose,
        int $userId
    ): Loan {
        $totalInterest = $principalAmount * ($interestRate / 100);
        $totalAmount = $principalAmount + $totalInterest;
        $monthlyInstallment = $totalAmount / $installmentCount;

        return Loan::create([
            'uuid' => (string) Str::uuid(),
            'loan_no' => Loan::generateLoanNo(),
            'employee_id' => $employeeId,
            'loan_type' => $loanType,
            'principal_amount' => $principalAmount,
            'interest_rate' => $interestRate,
            'total_interest' => $totalInterest,
            'total_amount' => $totalAmount,
            'monthly_installment' => $monthlyInstallment,
            'installment_count' => $installmentCount,
            'paid_installments' => 0,
            'remaining_amount' => $totalAmount,
            'loan_date' => now(),
            'start_date' => now()->addMonth(),
            'end_date' => now()->addMonths($installmentCount + 1),
            'status' => Loan::STATUS_PENDING,
            'purpose' => $purpose,
        ]);
    }

    public function approveLoan(string $uuid, int $userId): void
    {
        $loan = Loan::where('uuid', $uuid)->firstOrFail();
        $loan->update([
            'status' => Loan::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function getLoanBalance(string $employeeUuid): array
    {
        $employee = Employee::where('uuid', $employeeUuid)->firstOrFail();
        
        $loans = Loan::where('employee_id', $employee->id)
            ->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_ACTIVE])
            ->get();

        $activeLoans = [];
        $totalRemaining = 0;

        foreach ($loans as $loan) {
            $remaining = $loan->calculateRemainingAmount();
            $totalRemaining += $remaining;
            
            $activeLoans[] = [
                'loan_no' => $loan->loan_no,
                'type' => $loan->loan_type,
                'principal' => $loan->principal_amount,
                'monthly' => $loan->monthly_installment,
                'remaining' => $remaining,
                'remaining_installments' => ceil($remaining / $loan->monthly_installment),
            ];
        }

        return [
            'total_loans' => $loans->count(),
            'total_remaining' => $totalRemaining,
            'active_loans' => $activeLoans,
        ];
    }

    // ===================== OVERTIME =====================

    public function getOvertimes(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = OvertimeRecord::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('overtime_date', $filters['month']);
        }

        if (!empty($filters['year'])) {
            $query->whereYear('overtime_date', $filters['year']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('overtime_date', 'desc')->paginate($perPage);
    }

    public function createOvertime(
        int $employeeId,
        string $overtimeDate,
        float $hours,
        string $overtimeType,
        ?string $reason,
        int $userId
    ): OvertimeRecord {
        $employee = Employee::with('salaryGrade')->findOrFail($employeeId);
        
        // Calculate hourly rate
        $basicSalary = $employee->salaryGrade?->basic_salary ?? 0;
        $hourlyRate = ($basicSalary / 30) / 8;
        
        $rate = OvertimeRecord::getRateForType($overtimeType);
        $amount = $hours * $hourlyRate * $rate;

        return OvertimeRecord::create([
            'uuid' => (string) Str::uuid(),
            'employee_id' => $employeeId,
            'overtime_date' => $overtimeDate,
            'hours' => $hours,
            'rate' => $rate,
            'amount' => $amount,
            'overtime_type' => $overtimeType,
            'reason' => $reason,
            'status' => OvertimeRecord::STATUS_PENDING,
        ]);
    }

    public function approveOvertime(string $uuid, int $userId): void
    {
        $overtime = OvertimeRecord::where('uuid', $uuid)->firstOrFail();
        $overtime->update([
            'status' => OvertimeRecord::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    // ===================== REPORTS =====================

    public function getPayrollReport(int $month, int $year, ?int $departmentId = null): array
    {
        $query = Payroll::where('payroll_month', $month)
            ->where('payroll_year', $year)
            ->with(['employee.department']);

        if ($departmentId) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $departmentId));
        }

        $payrolls = $query->get();

        return [
            'month' => $month,
            'year' => $year,
            'total_employees' => $payrolls->count(),
            'total_gross' => $payrolls->sum('gross_salary'),
            'total_net' => $payrolls->sum('net_salary'),
            'total_deduction' => $payrolls->sum('total_deduction') 
                + $payrolls->sum('tax_amount') + $payrolls->sum('pf_amount'),
            'total_overtime' => $payrolls->sum('overtime_amount'),
            'total_bonus' => $payrolls->sum('bonus_amount'),
            'by_department' => $payrolls->groupBy(fn($p) => $p->employee?->department?->name ?? 'N/A')
                ->map(fn($g) => [
                    'count' => $g->count(),
                    'gross' => $g->sum('gross_salary'),
                    'net' => $g->sum('net_salary'),
                ]),
        ];
    }

    public function getLeaveReport(int $year, ?int $departmentId = null): array
    {
        $query = Leave::whereYear('start_date', $year)
            ->whereIn('status', [Leave::STATUS_APPROVED])
            ->with(['employee.department', 'leaveType']);

        if ($departmentId) {
            $query->whereHas('employee', fn($q) => $q->where('department_id', $departmentId));
        }

        $leaves = $query->get();

        return [
            'year' => $year,
            'total_leaves' => $leaves->count(),
            'total_days' => $leaves->sum('total_days'),
            'by_type' => $leaves->groupBy(fn($l) => $l->leaveType?->name ?? 'N/A')
                ->map(fn($g) => [
                    'count' => $g->count(),
                    'days' => $g->sum('total_days'),
                ]),
        ];
    }

    public function getDashboard(): array
    {
        $month = now()->month;
        $year = now()->year;

        return [
            'employees' => Employee::where('employment_status', 'active')->count(),
            'pending_leaves' => Leave::where('status', Leave::STATUS_PENDING)->count(),
            'pending_loans' => Loan::where('status', Loan::STATUS_PENDING)->count(),
            'pending_overtimes' => OvertimeRecord::where('status', OvertimeRecord::STATUS_PENDING)->count(),
            'month_payroll' => [
                'total' => Payroll::where('payroll_month', $month)->where('payroll_year', $year)->count(),
                'gross' => Payroll::where('payroll_month', $month)->where('payroll_year', $year)->sum('gross_salary'),
                'net' => Payroll::where('payroll_month', $month)->where('payroll_year', $year)->sum('net_salary'),
            ],
        ];
    }

    public function exportPayslips(int $month, int $year, string $format): string
    {
        $filename = "payslips_{$year}_{$month}_" . now()->format('Ymd_His');
        return url("storage/exports/{$filename}.{$format}");
    }

    private function numberToWords(float $number): string
    {
        return number_format($number, 2) . ' Taka';
    }

    // ===================== ADVANCE SALARY =====================

    public function getAdvanceSalaries(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = AdvanceSalary::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createAdvanceSalary(
        int $employeeId,
        float $amount,
        int $installmentCount,
        ?string $purpose,
        int $userId
    ): AdvanceSalary {
        $monthlyDeduction = $amount / $installmentCount;

        return AdvanceSalary::create([
            'uuid' => (string) Str::uuid(),
            'advance_no' => AdvanceSalary::generateAdvanceNo(),
            'employee_id' => $employeeId,
            'requested_amount' => $amount,
            'approved_amount' => $amount,
            'monthly_deduction' => $monthlyDeduction,
            'installment_count' => $installmentCount,
            'paid_installments' => 0,
            'remaining_amount' => $amount,
            'request_date' => now(),
            'deduction_start_date' => now()->addMonth(),
            'status' => AdvanceSalary::STATUS_PENDING,
            'purpose' => $purpose,
        ]);
    }

    public function approveAdvanceSalary(string $uuid, int $userId): void
    {
        $advance = AdvanceSalary::where('uuid', $uuid)->firstOrFail();
        $advance->update([
            'status' => AdvanceSalary::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    // ===================== BONUS =====================

    public function getBonuses(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Bonus::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['bonus_type'])) {
            $query->where('bonus_type', $filters['bonus_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('bonus_date', 'desc')->paginate($perPage);
    }

    public function createBonus(
        int $employeeId,
        string $bonusType,
        string $name,
        float $amount,
        ?float $percentage,
        string $bonusDate,
        int $userId
    ): Bonus {
        return Bonus::create([
            'uuid' => (string) Str::uuid(),
            'bonus_no' => Bonus::generateBonusNo(),
            'employee_id' => $employeeId,
            'bonus_type' => $bonusType,
            'name' => $name,
            'amount' => $amount,
            'percentage' => $percentage,
            'bonus_date' => $bonusDate,
            'status' => Bonus::STATUS_PENDING,
        ]);
    }

    public function approveBonus(string $uuid, int $userId): void
    {
        $bonus = Bonus::where('uuid', $uuid)->firstOrFail();
        $bonus->update([
            'status' => Bonus::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function payBonus(string $uuid): void
    {
        $bonus = Bonus::where('uuid', $uuid)->firstOrFail();
        $bonus->update([
            'status' => Bonus::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }

    // ===================== INCREMENT =====================

    public function getIncrements(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Increment::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['increment_type'])) {
            $query->where('increment_type', $filters['increment_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('effective_date', 'desc')->paginate($perPage);
    }

    public function createIncrement(
        int $employeeId,
        string $incrementType,
        float $previousBasic,
        float $newBasic,
        string $effectiveDate,
        ?int $newGradeId,
        ?string $reason,
        int $userId
    ): Increment {
        return Increment::create([
            'uuid' => (string) Str::uuid(),
            'increment_no' => Increment::generateIncrementNo(),
            'employee_id' => $employeeId,
            'increment_type' => $incrementType,
            'previous_basic' => $previousBasic,
            'new_basic' => $newBasic,
            'increment_amount' => $newBasic - $previousBasic,
            'percentage' => (($newBasic - $previousBasic) / $previousBasic) * 100,
            'effective_date' => $effectiveDate,
            'new_grade_id' => $newGradeId,
            'status' => Increment::STATUS_PENDING,
            'reason' => $reason,
        ]);
    }

    public function approveIncrement(string $uuid, int $userId): void
    {
        $increment = Increment::where('uuid', $uuid)->firstOrFail();
        $increment->update([
            'status' => Increment::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function activateIncrement(string $uuid): void
    {
        $increment = Increment::where('uuid', $uuid)->firstOrFail();
        
        $employee = Employee::findOrFail($increment->employee_id);
        
        if ($increment->new_grade_id) {
            $employee->update(['salary_grade_id' => $increment->new_grade_id]);
        }
        
        $increment->update(['status' => Increment::STATUS_ACTIVE]);
    }

    // ===================== PROMOTION =====================

    public function getPromotions(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = Promotion::with(['employee.profile', 'newDesignation', 'newDepartment']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('effective_date', 'desc')->paginate($perPage);
    }

    public function createPromotion(
        int $employeeId,
        ?int $newDepartmentId,
        int $newDesignationId,
        ?int $newGradeId,
        float $previousBasic,
        float $newBasic,
        string $promotionDate,
        string $effectiveDate,
        ?string $reason,
        int $userId
    ): Promotion {
        $employee = Employee::findOrFail($employeeId);

        return Promotion::create([
            'uuid' => (string) Str::uuid(),
            'promotion_no' => Promotion::generatePromotionNo(),
            'employee_id' => $employeeId,
            'previous_department_id' => $employee->department_id,
            'new_department_id' => $newDepartmentId,
            'previous_designation_id' => $employee->designation_id,
            'new_designation_id' => $newDesignationId,
            'previous_grade_id' => $employee->salary_grade_id,
            'new_grade_id' => $newGradeId,
            'previous_basic' => $previousBasic,
            'new_basic' => $newBasic,
            'promotion_date' => $promotionDate,
            'effective_date' => $effectiveDate,
            'status' => Promotion::STATUS_PENDING,
            'reason' => $reason,
        ]);
    }

    public function approvePromotion(string $uuid, int $userId): void
    {
        $promotion = Promotion::where('uuid', $uuid)->firstOrFail();
        $promotion->update([
            'status' => Promotion::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function activatePromotion(string $uuid): void
    {
        $promotion = Promotion::where('uuid', $uuid)->firstOrFail();
        
        $updateData = [];
        
        if ($promotion->new_department_id) {
            $updateData['department_id'] = $promotion->new_department_id;
        }
        
        if ($promotion->new_designation_id) {
            $updateData['designation_id'] = $promotion->new_designation_id;
        }
        
        if ($promotion->new_grade_id) {
            $updateData['salary_grade_id'] = $promotion->new_grade_id;
        }
        
        if (!empty($updateData)) {
            Employee::where('id', $promotion->employee_id)->update($updateData);
        }
        
        $promotion->update(['status' => Promotion::STATUS_ACTIVE]);
    }

    // ===================== EMPLOYEE EXIT / FINAL SETTLEMENT =====================

    public function getEmployeeExits(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = EmployeeExit::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['exit_type'])) {
            $query->where('exit_type', $filters['exit_type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('last_working_date', 'desc')->paginate($perPage);
    }

    public function createEmployeeExit(
        int $employeeId,
        string $exitType,
        string $lastWorkingDate,
        ?string $noticeDate,
        ?string $reason,
        int $userId
    ): EmployeeExit {
        $employee = Employee::with('salaryGrade')->findOrFail($employeeId);
        
        // Calculate settlement components
        $salaryAmount = 0;
        $leaveEncashment = 0;
        $pfBalance = 0;
        
        // Get last month salary
        $lastPayroll = Payroll::where('employee_id', $employeeId)
            ->where('status', Payroll::STATUS_PAID)
            ->orderBy('payroll_year', 'desc')
            ->orderBy('payroll_month', 'desc')
            ->first();
        
        if ($lastPayroll) {
            $salaryAmount = $lastPayroll->net_salary;
        }
        
        // Calculate leave encashment
        $approvedLeaves = Leave::where('employee_id', $employeeId)
            ->where('status', Leave::STATUS_APPROVED)
            ->get();
        
        foreach ($approvedLeaves as $leave) {
            if ($leave->leaveType->is_encashable) {
                $leaveEncashment += $leave->total_days * ($employee->salaryGrade?->basic_salary ?? 0) / 30;
            }
        }
        
        // Get PF balance
        $pf = ProvidentFund::where('employee_id', $employeeId)
            ->where('status', ProvidentFund::STATUS_ACTIVE)
            ->first();
        
        if ($pf) {
            $pfBalance = $pf->total_balance;
        }
        
        $netPayable = $salaryAmount + $leaveEncashment + $pfBalance;

        return EmployeeExit::create([
            'uuid' => (string) Str::uuid(),
            'exit_no' => EmployeeExit::generateExitNo(),
            'employee_id' => $employeeId,
            'exit_type' => $exitType,
            'notice_date' => $noticeDate,
            'last_working_date' => $lastWorkingDate,
            'salary_amount' => $salaryAmount,
            'leave_encashment' => $leaveEncashment,
            'pf_balance' => $pfBalance,
            'net_payable' => $netPayable,
            'status' => EmployeeExit::STATUS_PENDING,
            'reason' => $reason,
        ]);
    }

    public function approveEmployeeExit(string $uuid, int $userId): void
    {
        $exit = EmployeeExit::where('uuid', $uuid)->firstOrFail();
        $exit->update([
            'status' => EmployeeExit::STATUS_APPROVED,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function processEmployeeExit(string $uuid, int $userId): void
    {
        $exit = EmployeeExit::where('uuid', $uuid)->firstOrFail();
        
        // Update employee status
        $employee = Employee::findOrFail($exit->employee_id);
        
        $statusMap = [
            EmployeeExit::TYPE_RESIGNATION => Employee::STATUS_RESIGNED,
            EmployeeExit::TYPE_TERMINATION => Employee::STATUS_TERMINATED,
            EmployeeExit::TYPE_RETIREMENT => Employee::STATUS_RETIRED,
        ];
        
        $employee->update(['status' => $statusMap[$exit->exit_type] ?? Employee::STATUS_INACTIVE]);
        
        // Close PF account
        $pf = ProvidentFund::where('employee_id', $exit->employee_id)
            ->where('status', ProvidentFund::STATUS_ACTIVE)
            ->first();
        
        if ($pf) {
            $pf->update([
                'status' => ProvidentFund::STATUS_CLOSED,
                'closing_date' => now(),
            ]);
        }
        
        $exit->update([
            'status' => EmployeeExit::STATUS_PROCESSED,
            'processed_by' => $userId,
            'processed_at' => now(),
        ]);
    }

    public function payEmployeeExit(string $uuid): void
    {
        $exit = EmployeeExit::where('uuid', $uuid)->firstOrFail();
        $exit->update([
            'status' => EmployeeExit::STATUS_COMPLETED,
            'paid_at' => now(),
        ]);
    }

    // ===================== PROVIDENT FUND =====================

    public function getProvidentFunds(int $perPage = 50, array $filters = []): LengthAwarePaginator
    {
        $query = ProvidentFund::with(['employee.profile']);

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function createProvidentFund(int $employeeId): ProvidentFund
    {
        return ProvidentFund::create([
            'uuid' => (string) Str::uuid(),
            'pf_no' => ProvidentFund::generatePFNo(),
            'employee_id' => $employeeId,
            'employee_contribution' => 0,
            'employer_contribution' => 0,
            'total_contribution' => 0,
            'interest_earned' => 0,
            'total_balance' => 0,
            'withdrawn_amount' => 0,
            'status' => ProvidentFund::STATUS_ACTIVE,
            'activation_date' => now(),
        ]);
    }

    public function addPFContribution(int $pfId, float $employeeAmount, float $employerAmount, int $month, int $year): void
    {
        $pf = ProvidentFund::findOrFail($pfId);
        
        // Add contribution record
        PFContribution::create([
            'uuid' => (string) Str::uuid(),
            'pf_id' => $pfId,
            'contribution_month' => $month,
            'contribution_year' => $year,
            'employee_amount' => $employeeAmount,
            'employer_amount' => $employerAmount,
            'interest_amount' => 0,
            'total_amount' => $employeeAmount + $employerAmount,
        ]);
        
        // Update PF balance
        $pf->addContribution($employeeAmount, $employerAmount);
    }

    // ===================== INCOME TAX =====================

    public function getTaxSlabs(int $fiscalYear): \Illuminate\Database\Eloquent\Collection
    {
        return TaxSlab::where('fiscal_year', $fiscalYear)
            ->where('is_active', true)
            ->orderBy('min_income', 'asc')
            ->get();
    }

    public function createTaxSlab(array $data): TaxSlab
    {
        return TaxSlab::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['name'],
            'fiscal_year' => $data['fiscal_year'],
            'min_income' => $data['min_income'],
            'max_income' => $data['max_income'] ?? null,
            'rate_percent' => $data['rate_percent'],
            'fixed_amount' => $data['fixed_amount'] ?? 0,
            'is_active' => true,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function calculateEmployeeTax(int $employeeId, int $fiscalYear): EmployeeTaxRecord
    {
        $employee = Employee::with('salaryGrade')->findOrFail($employeeId);
        
        // Get all payrolls for the fiscal year
        $payrolls = Payroll::where('employee_id', $employeeId)
            ->whereYear('payroll_year', $fiscalYear)
            ->get();
        
        $grossSalary = $payrolls->sum('gross_salary');
        
        return EmployeeTaxRecord::create([
            'uuid' => (string) Str::uuid(),
            'employee_id' => $employeeId,
            'fiscal_year' => $fiscalYear,
            'gross_salary' => $grossSalary,
            'exempted_allowances' => 0,
            'taxable_income' => $grossSalary,
            'annual_tax' => 0,
            'monthly_tax' => 0,
            'tax_paid' => $payrolls->sum('tax_amount'),
            'adjustment' => 0,
            'remaining_tax' => 0,
            'status' => EmployeeTaxRecord::STATUS_PENDING,
        ]);
    }

    // ===================== EMPLOYEE ATTENDANCE =====================

    public function getEmployeeAttendances(int $employeeId, string $startDate, string $endDate): \Illuminate\Database\Eloquent\Collection
    {
        return EmployeeAttendance::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->orderBy('attendance_date')
            ->get();
    }

    public function markAttendance(
        int $employeeId,
        string $date,
        string $status,
        ?string $checkIn = null,
        ?string $checkOut = null
    ): EmployeeAttendance {
        return EmployeeAttendance::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'attendance_date' => $date,
            ],
            [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => $status,
            ]
        );
    }

    // ===================== LEAVE BALANCE =====================

    public function initializeLeaveBalances(int $employeeId, int $fiscalYear): void
    {
        $employee = Employee::findOrFail($employeeId);
        EmployeeLeaveBalance::initializeForEmployee($employee, $fiscalYear);
    }

    public function getLeaveBalances(int $employeeId, int $fiscalYear): \Illuminate\Database\Eloquent\Collection
    {
        return EmployeeLeaveBalance::where('employee_id', $employeeId)
            ->where('fiscal_year', $fiscalYear)
            ->with('leaveType')
            ->get();
    }
}
