/**
 * HR, Payroll & Leave Management Types
 * Phase 034 - Enterprise HRM System
 */

// ===================== RECRUITMENT TYPES =====================

export interface JobCircular {
  id: string;
  uuid: string;
  circular_no: string;
  title: string;
  title_bn?: string;
  description?: string;
  requirements?: string;
  benefits?: string;
  job_code?: string;
  department_id?: number;
  department?: { id: number; name: string };
  designation_id?: number;
  designation?: { id: number; name: string };
  employment_type_id?: number;
  employment_type?: { id: number; name: string };
  vacancy: number;
  min_salary?: number;
  max_salary?: number;
  salary_range?: string;
  application_deadline?: string;
  published_date?: string;
  interview_date?: string;
  status: 'draft' | 'published' | 'closed' | 'cancelled';
  is_active: boolean;
  terms_conditions?: string;
  notes?: string;
  application_count?: number;
  shortlisted_count?: number;
  created_at: string;
  updated_at: string;
}

export interface JobApplication {
  id: string;
  uuid: string;
  application_no: string;
  job_circular_id: number;
  job_circular?: JobCircular;
  full_name: string;
  father_name?: string;
  mother_name?: string;
  date_of_birth?: string;
  gender?: string;
  blood_group?: string;
  religion?: string;
  nationality?: string;
  marital_status?: string;
  nid?: string;
  passport?: string;
  email?: string;
  mobile?: string;
  alternative_mobile?: string;
  present_address?: string;
  permanent_address?: string;
  photo?: string;
  cv?: string;
  cover_letter?: string;
  certificates?: string[];
  experience_details?: string;
  education_details?: string;
  applicant_status: ApplicantStatus;
  rejection_reason?: string;
  notes?: string;
  created_at: string;
  updated_at: string;
}

export type ApplicantStatus =
  | 'applied'
  | 'under_review'
  | 'shortlisted'
  | 'interview_scheduled'
  | 'interviewed'
  | 'selected'
  | 'rejected'
  | 'waiting_list'
  | 'withdrawn';

export interface Interview {
  id: string;
  uuid: string;
  interview_no: string;
  job_circular_id: number;
  job_circular?: JobCircular;
  job_application_id: number;
  job_application?: JobApplication;
  interview_date: string;
  start_time?: string;
  end_time?: string;
  venue?: string;
  interview_type: 'personal' | 'panel' | 'written' | 'practical';
  panel_members?: string[];
  total_marks: number;
  obtained_marks?: number;
  questions?: string;
  answers?: string;
  remarks?: string;
  feedback?: string;
  decision: 'pending' | 'selected' | 'rejected' | 'waiting_list' | 'hold';
  rating?: number;
  evaluation_scores?: EvaluationScores;
  offer_extended: boolean;
  offer_date?: string;
  joining_date?: string;
  rejection_reason?: string;
  created_at: string;
  updated_at: string;
}

export interface EvaluationScores {
  education?: number;
  experience?: number;
  technical?: number;
  communication?: number;
  leadership?: number;
  overall?: number;
}

export interface OfferLetter {
  id: string;
  uuid: string;
  offer_no: string;
  job_circular_id: number;
  job_circular?: JobCircular;
  job_application_id: number;
  job_application?: JobApplication;
  interview_id?: number;
  candidate_name: string;
  email?: string;
  mobile?: string;
  designation_id: number;
  designation?: { id: number; name: string };
  department_id?: number;
  department?: { id: number; name: string };
  employment_type_id: number;
  employment_type?: { id: number; name: string };
  salary_grade_id?: number;
  salary_grade?: SalaryGrade;
  offered_salary?: number;
  offer_date: string;
  joining_date: string;
  terms_conditions?: string;
  benefits?: string;
  status: 'draft' | 'sent' | 'accepted' | 'declined' | 'expired' | 'joined';
  response_date?: string;
  response_notes?: string;
  created_at: string;
  updated_at: string;
}

// ===================== ONBOARDING TYPES =====================

export interface OnboardingChecklist {
  id: string;
  uuid: string;
  checklist_name: string;
  category: OnboardingCategory;
  order: number;
  description?: string;
  is_required: boolean;
  is_active: boolean;
}

export type OnboardingCategory =
  | 'account'
  | 'documents'
  | 'equipment'
  | 'training'
  | 'payroll';

export interface EmployeeOnboarding {
  id: string;
  uuid: string;
  onboarding_no: string;
  employee_id: number;
  employee?: EmployeeInfo;
  offer_letter_id?: number;
  start_date: string;
  completion_date?: string;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  assigned_to?: number;
  assigned_user?: { id: number; name: string };
  notes?: string;
  completion_percentage?: number;
  completions?: OnboardingCompletion[];
  created_at: string;
  updated_at: string;
}

export interface OnboardingCompletion {
  id: string;
  uuid: string;
  employee_onboarding_id: number;
  checklist_id: number;
  checklist?: OnboardingChecklist;
  is_completed: boolean;
  completed_date?: string;
  completed_by?: number;
  completed_user?: { id: number; name: string };
  remarks?: string;
}

// ===================== TRANSFER TYPES =====================

export interface EmployeeTransfer {
  id: string;
  uuid: string;
  transfer_no: string;
  employee_id: number;
  employee?: EmployeeInfo;
  from_department_id?: number;
  from_department?: { id: number; name: string };
  to_department_id?: number;
  to_department?: { id: number; name: string };
  from_designation_id?: number;
  from_designation?: { id: number; name: string };
  to_designation_id?: number;
  to_designation?: { id: number; name: string };
  from_campus_id?: number;
  from_campus?: { id: number; name: string };
  to_campus_id?: number;
  to_campus?: { id: number; name: string };
  from_shift_id?: number;
  from_shift?: { id: number; name: string };
  to_shift_id?: number;
  to_shift?: { id: number; name: string };
  reporting_manager_id?: number;
  reporting_manager?: EmployeeInfo;
  transfer_date: string;
  effective_date: string;
  transfer_type: 'department' | 'campus' | 'designation' | 'shift' | 'reporting_manager' | 'combined';
  reason?: string;
  remarks?: string;
  status: 'pending' | 'recommended' | 'approved' | 'cancelled';
  recommended_by?: number;
  recommended_user?: { id: number; name: string };
  recommended_date?: string;
  approved_by?: number;
  approved_user?: { id: number; name: string };
  approved_date?: string;
  created_at: string;
  updated_at: string;
}

// ===================== SERVICE BOOK TYPES =====================

export interface ServiceBookEntry {
  id: string;
  uuid: string;
  employee_id: number;
  entry_no: string;
  entry_date: string;
  event_type: ServiceBookEventType;
  title?: string;
  description?: string;
  metadata?: Record<string, any>;
  approved_by?: number;
  approver?: { id: number; name: string };
  approved_date?: string;
  remarks?: string;
  created_at: string;
  updated_at: string;
}

export type ServiceBookEventType =
  | 'joining'
  | 'promotion'
  | 'transfer'
  | 'salary_revision'
  | 'leave'
  | 'award'
  | 'punishment'
  | 'training'
  | 'performance_review'
  | 'confirmation'
  | 'resignation'
  | 'retirement'
  | 'termination'
  | 'other';

export interface ServiceBookTimeline {
  id: string;
  date: string;
  event_type: ServiceBookEventType;
  event_label: string;
  icon: string;
  title?: string;
  description?: string;
  metadata?: Record<string, any>;
  approved_by?: string;
  remarks?: string;
}

// ===================== TRAINING TYPES =====================

export interface TrainingType {
  id: string;
  uuid: string;
  name: string;
  name_bn?: string;
  code: string;
  description?: string;
  is_active: boolean;
}

export interface TrainingRecord {
  id: string;
  uuid: string;
  training_no: string;
  employee_id: number;
  employee?: EmployeeInfo;
  training_type_id: number;
  training_type?: TrainingType;
  training_name: string;
  organizer?: string;
  venue?: string;
  start_date: string;
  end_date?: string;
  duration_days: number;
  duration_hours: number;
  certificate_number?: string;
  certificate_date?: string;
  result: 'pending' | 'passed' | 'failed' | 'incomplete' | 'excellent' | 'very_good' | 'good';
  feedback?: string;
  score?: number;
  cost?: number;
  certificate_file?: string;
  notes?: string;
  created_at: string;
  updated_at: string;
}

// ===================== AWARD TYPES =====================

export interface AwardType {
  id: string;
  uuid: string;
  name: string;
  name_bn?: string;
  code: string;
  description?: string;
  default_reward?: number;
  is_monetary: boolean;
  is_active: boolean;
}

export interface EmployeeAward {
  id: string;
  uuid: string;
  award_no: string;
  employee_id: number;
  employee?: EmployeeInfo;
  award_type_id: number;
  award_type?: AwardType;
  title?: string;
  award_date: string;
  reason?: string;
  reward_amount?: number;
  reward_type?: 'cash' | 'certificate' | 'trophy' | 'plaque' | 'gift';
  certificate_number?: string;
  certificate_date?: string;
  certificate_file?: string;
  presented_by?: number;
  presenter?: { id: number; name: string };
  notes?: string;
  created_at: string;
  updated_at: string;
}

// ===================== CONFIRMATION TYPES =====================

export interface ConfirmationRecord {
  id: string;
  uuid: string;
  confirmation_no: string;
  employee_id: number;
  employee?: EmployeeInfo;
  probation_start_date: string;
  probation_end_date: string;
  performance_summary?: string;
  recommendation: 'confirm' | 'extend_probation' | 'terminate';
  recommendation_remarks?: string;
  status: 'pending' | 'under_review' | 'recommended' | 'approved' | 'rejected' | 'cancelled';
  recommended_by?: number;
  recommended_user?: { id: number; name: string };
  recommended_date?: string;
  reviewed_by?: number;
  reviewed_user?: { id: number; name: string };
  reviewed_date?: string;
  approved_by?: number;
  approved_user?: { id: number; name: string };
  approved_date?: string;
  confirmation_date?: string;
  confirmation_letter?: string;
  remarks?: string;
  created_at: string;
  updated_at: string;
}

// ===================== CERTIFICATE TYPES =====================

export interface ExperienceCertificate {
  id: string;
  uuid: string;
  certificate_no: string;
  employee_id: number;
  employee?: EmployeeInfo;
  issue_date: string;
  start_date: string;
  end_date: string;
  total_years: number;
  total_months: number;
  duration_formatted?: string;
  experience_summary?: string;
  performance_remarks?: string;
  reason_for_leaving?: string;
  is_verified: boolean;
  verification_code?: string;
  qr_code?: string;
  pdf_file?: string;
  issued_by?: number;
  issuer?: { id: number; name: string };
  authorized_by?: number;
  authorizer?: { id: number; name: string };
  remarks?: string;
  created_at: string;
  updated_at: string;
}

export interface NocCertificate {
  id: string;
  uuid: string;
  certificate_no: string;
  employee_id: number;
  employee?: EmployeeInfo;
  noc_type: 'general' | 'visa' | 'immigration' | 'employment' | 'government';
  issue_date: string;
  purpose?: string;
  content?: string;
  is_verified: boolean;
  verification_code?: string;
  qr_code?: string;
  pdf_file?: string;
  issued_by?: number;
  issuer?: { id: number; name: string };
  authorized_by?: number;
  authorizer?: { id: number; name: string };
  remarks?: string;
  created_at: string;
  updated_at: string;
}

// ===================== HR DASHBOARD STATS =====================

export interface HRDashboardStats {
  employees: {
    total: number;
    teachers: number;
    staff: number;
    active: number;
    inactive: number;
    new_joining: number;
    resigned: number;
    retired: number;
  };
  recruitment: {
    active_circulars: number;
    total_applications: number;
    pending_interviews: number;
    selected: number;
  };
  workflow: {
    pending_confirmation: number;
    pending_promotion: number;
    pending_transfer: number;
    pending_exit_clearance: number;
  };
  payroll: {
    month_gross: number;
    month_net: number;
    pending_payslips: number;
  };
}

// ===================== ENUMS =====================

export const APPLICANT_STATUSES: Record<ApplicantStatus, string> = {
  applied: 'Applied',
  under_review: 'Under Review',
  shortlisted: 'Shortlisted',
  interview_scheduled: 'Interview Scheduled',
  interviewed: 'Interviewed',
  selected: 'Selected',
  rejected: 'Rejected',
  waiting_list: 'Waiting List',
  withdrawn: 'Withdrawn',
};

export const INTERVIEW_DECISIONS: Record<string, string> = {
  pending: 'Pending',
  selected: 'Selected',
  rejected: 'Rejected',
  waiting_list: 'Waiting List',
  hold: 'Hold',
};

export const OFFER_STATUSES: Record<string, string> = {
  draft: 'Draft',
  sent: 'Sent',
  accepted: 'Accepted',
  declined: 'Declined',
  expired: 'Expired',
  joined: 'Joined',
};

export const ONBOARDING_CATEGORIES: Record<OnboardingCategory, string> = {
  account: 'Account Setup',
  documents: 'Documents',
  equipment: 'Equipment',
  training: 'Training',
  payroll: 'Payroll',
};

export const TRANSFER_TYPES: Record<string, string> = {
  department: 'Department Transfer',
  campus: 'Campus Transfer',
  designation: 'Designation Change',
  shift: 'Shift Change',
  reporting_manager: 'Reporting Manager Change',
  combined: 'Combined Transfer',
};

export const TRANSFER_STATUSES: Record<string, string> = {
  pending: 'Pending',
  recommended: 'Recommended',
  approved: 'Approved',
  cancelled: 'Cancelled',
};

export const TRAINING_RESULTS: Record<string, string> = {
  pending: 'Pending',
  passed: 'Passed',
  failed: 'Failed',
  incomplete: 'Incomplete',
  excellent: 'Excellent',
  very_good: 'Very Good',
  good: 'Good',
};

export const CONFIRMATION_STATUSES: Record<string, string> = {
  pending: 'Pending',
  under_review: 'Under Review',
  recommended: 'Recommended',
  approved: 'Approved',
  rejected: 'Rejected',
  cancelled: 'Cancelled',
};

export const NOC_TYPES: Record<string, string> = {
  general: 'General NOC',
  visa: 'Visa NOC',
  immigration: 'Immigration NOC',
  employment: 'Employment NOC',
  government: 'Government NOC',
};

export const JOB_CIRCULAR_STATUSES: Record<string, string> = {
  draft: 'Draft',
  published: 'Published',
  closed: 'Closed',
  cancelled: 'Cancelled',
};

export const SERVICE_BOOK_EVENT_TYPES: Record<ServiceBookEventType, string> = {
  joining: 'Joining',
  promotion: 'Promotion',
  transfer: 'Transfer',
  salary_revision: 'Salary Revision',
  leave: 'Leave',
  award: 'Award',
  punishment: 'Punishment',
  training: 'Training',
  performance_review: 'Performance Review',
  confirmation: 'Confirmation',
  resignation: 'Resignation',
  retirement: 'Retirement',
  termination: 'Termination',
  other: 'Other',
};

// ===================== ORIGINAL TYPES =====================

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

// Additional HR Types
export interface AdvanceSalary {
  id: string;
  advance_no: string;
  employee_id?: number;
  employee?: EmployeeInfo;
  requested_amount: number;
  approved_amount: number;
  monthly_deduction: number;
  installment_count: number;
  paid_installments: number;
  remaining_amount: number;
  request_date: string;
  deduction_start_date: string;
  status: 'pending' | 'approved' | 'active' | 'completed' | 'rejected' | 'cancelled';
  approved_at?: string;
  purpose?: string;
}

export interface Bonus {
  id: string;
  bonus_no: string;
  employee_id?: number;
  employee?: EmployeeInfo;
  bonus_type: BonusType;
  name: string;
  amount: number;
  percentage?: number;
  bonus_date: string;
  status: 'pending' | 'approved' | 'paid' | 'cancelled';
  approved_at?: string;
  paid_at?: string;
}

export interface Increment {
  id: string;
  increment_no: string;
  employee_id?: number;
  employee?: EmployeeInfo;
  increment_type: IncrementType;
  previous_basic: number;
  new_basic: number;
  increment_amount: number;
  percentage: number;
  effective_date: string;
  status: 'pending' | 'approved' | 'active' | 'cancelled';
  approved_at?: string;
  reason?: string;
}

export interface Promotion {
  id: string;
  promotion_no: string;
  employee_id?: number;
  employee?: EmployeeInfo;
  new_designation?: { id: string; name: string };
  new_department?: { id: string; name: string };
  promotion_date: string;
  effective_date: string;
  previous_basic: number;
  new_basic: number;
  status: 'pending' | 'approved' | 'active' | 'cancelled';
  approved_at?: string;
  reason?: string;
}

export interface EmployeeExit {
  id: string;
  exit_no: string;
  employee_id?: number;
  employee?: EmployeeInfo;
  exit_type: ExitType;
  last_working_date: string;
  salary_amount: number;
  leave_encashment: number;
  pf_balance: number;
  net_payable: number;
  status: 'pending' | 'approved' | 'processed' | 'completed';
  approved_at?: string;
  processed_at?: string;
  paid_at?: string;
  reason?: string;
}

export interface ProvidentFund {
  id: string;
  pf_no: string;
  employee_id?: number;
  employee?: EmployeeInfo;
  employee_contribution: number;
  employer_contribution: number;
  total_contribution: number;
  interest_earned: number;
  total_balance: number;
  withdrawn_amount: number;
  status: 'active' | 'closed' | 'frozen';
  activation_date: string;
  closing_date?: string;
}

export interface TaxSlab {
  id: string;
  name: string;
  fiscal_year: number;
  min_income: number;
  max_income?: number;
  rate_percent: number;
  fixed_amount: number;
  is_active: boolean;
  description?: string;
}

export interface EmployeeTaxRecord {
  id: string;
  employee_id?: number;
  fiscal_year: number;
  gross_salary: number;
  exempted_allowances: number;
  taxable_income: number;
  annual_tax: number;
  monthly_tax: number;
  tax_paid: number;
  adjustment: number;
  remaining_tax: number;
  status: 'pending' | 'calculated' | 'adjusted' | 'paid';
}

// Enums for new types
export type BonusType = 'festival' | 'performance' | 'yearly' | 'special';
export type IncrementType = 'annual' | 'performance' | 'promotion' | 'manual';
export type ExitType = 'resignation' | 'termination' | 'retirement' | 'death';

export const BONUS_TYPES: Record<BonusType, string> = {
  festival: 'Festival Bonus',
  performance: 'Performance Bonus',
  yearly: 'Yearly Bonus',
  special: 'Special Bonus',
};

export const INCREMENT_TYPES: Record<IncrementType, string> = {
  annual: 'Annual Increment',
  performance: 'Performance Increment',
  promotion: 'Promotion Increment',
  manual: 'Manual Increment',
};

export const EXIT_TYPES: Record<ExitType, string> = {
  resignation: 'Resignation',
  termination: 'Termination',
  retirement: 'Retirement',
  death: 'Death',
};

// Paginated Response
export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}
