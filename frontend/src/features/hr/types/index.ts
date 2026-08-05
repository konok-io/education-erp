/**
 * HR, Payroll & Leave Management Types
 */

export interface SalaryGrade {
  id: string;
  grade_name: string;
  basic_salary: number;
  house_rent_percent: number;
  medical_percent: number;
  transport_percent: number;
  mobile_allowance: number;
  special_allowance: number;
  other_allowance: number;
  provident_fund_percent: number;
  tax_percent: number;
  is_active: boolean;
}

export interface Payroll {
  id: string;
  payroll_no: string;
  month: number;
  year: number;
  basic_salary: number;
  gross_salary: number;
  total_allowance: number;
  total_deduction: number;
  tax_amount: number;
  pf_amount: number;
  loan_deduction: number;
  advance_deduction: number;
  overtime_amount: number;
  bonus_amount: number;
  net_salary: number;
  working_days: number;
  present_days: number;
  absent_days: number;
  late_days: number;
  status: PayrollStatus;
  paid_at?: string;
  employee?: EmployeeInfo;
  details?: PayrollDetail[];
  created_at: string;
}

export interface PayrollDetail {
  type: string;
  name: string;
  amount: number;
  is_earning: boolean;
}

export interface Payslip {
  employee: {
    name: string;
    employee_no: string;
    department?: string;
    designation?: string;
  };
  payroll: {
    no: string;
    month: number;
    year: number;
  };
  earnings: { name: string; amount: number }[];
  deductions: { name: string; amount: number }[];
  totals: {
    gross: number;
    total_allowance: number;
    total_deduction: number;
    net: number;
  };
  net_in_words: string;
  attendance: {
    working_days: number;
    present_days: number;
    absent_days: number;
  };
}

export interface LeaveType {
  id: string;
  name: string;
  name_bn?: string;
  code: string;
  short_code: string;
  leave_days: number;
  is_paid: boolean;
  is_encashable: boolean;
  is_carry_forward: boolean;
  max_consecutive_days: number;
  is_active: boolean;
}

export interface Leave {
  id: string;
  leave_no: string;
  start_date: string;
  end_date: string;
  total_days: number;
  reason: string;
  status: LeaveStatus;
  applied_at: string;
  approved_at?: string;
  rejection_reason?: string;
  employee?: EmployeeInfo;
  leave_type?: { id: string; name: string; code: string };
  created_at: string;
}

export interface LeaveBalance {
  type: string;
  code: string;
  total: number;
  used: number;
  pending: number;
  remaining: number;
}

export interface Holiday {
  id: string;
  name: string;
  name_bn?: string;
  holiday_date: string;
  holiday_type: HolidayType;
  is_repeating: boolean;
  is_active: boolean;
}

export interface Loan {
  id: string;
  loan_no: string;
  loan_type: LoanType;
  principal_amount: number;
  interest_rate: number;
  total_interest: number;
  total_amount: number;
  monthly_installment: number;
  installment_count: number;
  paid_installments: number;
  remaining_amount: number;
  loan_date: string;
  status: LoanStatus;
  purpose?: string;
  approved_at?: string;
  employee?: EmployeeInfo;
  created_at: string;
}

export interface LoanBalance {
  total_loans: number;
  total_remaining: number;
  active_loans: {
    loan_no: string;
    type: string;
    principal: number;
    monthly: number;
    remaining: number;
    remaining_installments: number;
  }[];
}

export interface OvertimeRecord {
  id: string;
  overtime_date: string;
  hours: number;
  rate: number;
  amount: number;
  overtime_type: OvertimeType;
  reason?: string;
  status: OvertimeStatus;
  approved_at?: string;
  employee?: EmployeeInfo;
  created_at: string;
}

export interface EmployeeInfo {
  id: string;
  employee_no: string;
  name: string;
  department?: string;
  designation?: string;
}

export interface HRDashboard {
  employees: number;
  pending_leaves: number;
  pending_loans: number;
  pending_overtimes: number;
  month_payroll: {
    total: number;
    gross: number;
    net: number;
  };
}

export interface PayrollReport {
  month: number;
  year: number;
  total_employees: number;
  total_gross: number;
  total_net: number;
  total_deduction: number;
  total_overtime: number;
  total_bonus: number;
  by_department: Record<string, { count: number; gross: number; net: number }>;
}

export interface LeaveReport {
  year: number;
  total_leaves: number;
  total_days: number;
  by_type: Record<string, { count: number; days: number }>;
}

// Enums
export type PayrollStatus = 'draft' | 'processed' | 'approved' | 'paid' | 'cancelled';
export type LeaveStatus = 'pending' | 'approved' | 'rejected' | 'cancelled';
export type HolidayType = 'weekly' | 'national' | 'religious' | 'institution' | 'emergency';
export type LoanType = 'personal' | 'house' | 'vehicle' | 'emergency' | 'festival';
export type LoanStatus = 'pending' | 'approved' | 'active' | 'completed' | 'rejected' | 'cancelled';
export type OvertimeType = 'normal' | 'weekend' | 'holiday' | 'night';
export type OvertimeStatus = 'pending' | 'approved' | 'processed' | 'rejected';

export const LOAN_TYPES: Record<LoanType, string> = {
  personal: 'Personal Loan',
  house: 'House Building Loan',
  vehicle: 'Vehicle Loan',
  emergency: 'Emergency Loan',
  festival: 'Festival Loan',
};

export const OVERTIME_TYPES: Record<OvertimeType, string> = {
  normal: 'Normal Overtime',
  weekend: 'Weekend Overtime',
  holiday: 'Holiday Overtime',
  night: 'Night Shift',
};

export const HOLIDAY_TYPES: Record<HolidayType, string> = {
  weekly: 'Weekly Holiday',
  national: 'National Holiday',
  religious: 'Religious Holiday',
  institution: 'Institution Holiday',
  emergency: 'Emergency Holiday',
};
