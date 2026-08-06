/**
 * Employee Types
 */

export interface Employee {
  id: string;
  employee_no: string;
  joining_date: string;
  status: EmployeeStatus;
  remarks?: string;
  full_name?: string;
  photo_url?: string;
  profile?: EmployeeProfile;
  department?: { id: string; name: string };
  designation?: { id: string; name: string };
  employment_type?: { id: string; name: string };
  salary_grade?: { id: string; name: string };
  shift?: { id: string; name: string };
  documents?: EmployeeDocument[];
  emergency_contacts?: EmployeeEmergencyContact[];
  salary?: EmployeeSalary;
  campus?: { id: string; name: string };
  created_at: string;
  updated_at: string;
}

export interface EmployeeProfile {
  id: string;
  first_name: string;
  last_name?: string;
  full_name: string;
  first_name_bn?: string;
  last_name_bn?: string;
  gender: 'male' | 'female' | 'other';
  date_of_birth?: string;
  age?: number;
  blood_group?: string;
  religion?: string;
  nationality?: string;
  nid?: string;
  passport?: string;
  email?: string;
  mobile?: string;
  alternate_mobile?: string;
  photo?: string;
  photo_url?: string;
  signature?: string;
  signature_url?: string;
  marital_status?: string;
  father_name?: string;
  mother_name?: string;
  present_address?: Address;
  permanent_address?: Address;
}

export interface Address {
  division?: string;
  district?: string;
  upazila?: string;
  union?: string;
  village?: string;
  post_code?: string;
  address?: string;
}

export interface Designation {
  id: string;
  name: string;
  name_bn?: string;
  code?: string;
  level?: number;
  status: string;
}

export interface EmploymentType {
  id: string;
  name: string;
  name_bn?: string;
  code?: string;
  status: string;
}

export interface SalaryGrade {
  id: string;
  grade_name: string;
  grade_code?: string;
  basic_salary: number;
  house_rent: number;
  medical_allowance: number;
  transport_allowance: number;
  special_allowance: number;
  provident_fund_rate: number;
  tax_percentage: number;
  status: string;
}

export interface Shift {
  id: string;
  shift_name: string;
  shift_code?: string;
  start_time: string;
  end_time: string;
  late_after_minutes: number;
  early_leave_before_minutes: number;
  working_hours: number;
  break_time_minutes: number;
  status: string;
}

export interface EmployeeDocument {
  id: string;
  document_type: string;
  title: string;
  file_path: string;
  file_url?: string;
  file_name: string;
  file_size: number;
  mime_type: string;
  issue_date?: string;
  expiry_date?: string;
  is_verified: boolean;
}

export interface EmployeeEmergencyContact {
  id: string;
  name: string;
  relation?: string;
  mobile: string;
  email?: string;
  address?: string;
  is_primary: boolean;
}

export interface EmployeeSalary {
  id: string;
  basic_salary: number;
  house_rent: number;
  medical_allowance: number;
  transport_allowance: number;
  special_allowance: number;
  gross_salary: number;
  provident_fund: number;
  tax_deduction: number;
  other_deduction: number;
  total_deduction: number;
  net_salary: number;
  effective_date: string;
  payment_method?: string;
  bank_name?: string;
  account_number?: string;
}

export interface EmployeeLeave {
  id: string;
  leave_type: LeaveType;
  start_date: string;
  end_date: string;
  total_days: number;
  reason: string;
  status: LeaveStatus;
  applied_at: string;
}

export type EmployeeStatus = 
  | 'pending' 
  | 'active' 
  | 'inactive' 
  | 'on_leave' 
  | 'suspended' 
  | 'retired' 
  | 'resigned' 
  | 'terminated';

export type LeaveType = 
  | 'casual' 
  | 'medical' 
  | 'annual' 
  | 'maternity' 
  | 'paternity' 
  | 'special' 
  | 'without_pay';

export type LeaveStatus = 
  | 'pending' 
  | 'approved' 
  | 'rejected' 
  | 'cancelled';

export type EmployeeFilters = {
  search?: string;
  department_id?: string;
  designation_id?: string;
  status?: EmployeeStatus;
  employment_type_id?: string;
  shift_id?: string;
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
  per_page?: number;
};

export interface EmployeeStatistics {
  total: number;
  active: number;
  pending: number;
  on_leave: number;
}
