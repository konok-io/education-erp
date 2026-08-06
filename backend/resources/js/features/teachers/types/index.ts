/**
 * Teacher Types
 */

export interface Teacher {
  id: string;
  teacher_no: string;
  employment_type: EmploymentType;
  joining_date: string;
  status: TeacherStatus;
  remarks?: string;
  full_name?: string;
  photo_url?: string;
  profile?: TeacherProfile;
  department?: { id: string; name: string };
  qualifications?: TeacherQualification[];
  experiences?: TeacherExperience[];
  documents?: TeacherDocument[];
  subject_assignments?: SubjectAssignment[];
  class_assignments?: ClassAssignment[];
  salary?: TeacherSalary;
  campus?: { id: string; name: string };
  created_at: string;
  updated_at: string;
}

export interface TeacherProfile {
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
  photo?: string;
  photo_url?: string;
  signature?: string;
  signature_url?: string;
  email?: string;
  mobile?: string;
  alternate_mobile?: string;
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

export interface TeacherQualification {
  id: string;
  degree: string;
  degree_bn?: string;
  institution: string;
  board_university?: string;
  subject?: string;
  passing_year?: number;
  result?: string;
  result_point?: number;
  attachment?: string;
  is_verified: boolean;
}

export interface TeacherExperience {
  id: string;
  organization: string;
  organization_bn?: string;
  designation?: string;
  department?: string;
  joining_date?: string;
  resign_date?: string;
  duration?: string;
  is_current: boolean;
  responsibilities?: string;
  document?: string;
  remarks?: string;
}

export interface TeacherDocument {
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

export interface SubjectAssignment {
  id: string;
  subject: { id: string; name: string };
  program: { id: string; name: string };
  session: { id: string; title: string };
  semester?: { id: string; title: string };
  is_class_teacher: boolean;
}

export interface ClassAssignment {
  id: string;
  class: { id: string; name: string };
  section?: { id: string; name: string };
  session: { id: string; title: string };
  is_primary_teacher: boolean;
  weekly_classes: number;
}

export interface TeacherSalary {
  id: string;
  basic_salary: number;
  house_rent: number;
  medical_allowance: number;
  transport_allowance: number;
  other_allowance: number;
  gross_salary: number;
  tax_deduction: number;
  provident_fund: number;
  other_deduction: number;
  total_deduction: number;
  net_salary: number;
  effective_date: string;
  payment_method?: string;
  bank_name?: string;
  account_number?: string;
}

export interface TeacherLeave {
  id: string;
  leave_type: LeaveType;
  start_date: string;
  end_date: string;
  total_days: number;
  reason: string;
  status: LeaveStatus;
  applied_at: string;
  approved_at?: string;
  rejected_at?: string;
  rejection_reason?: string;
}

export type EmploymentType = 
  | 'permanent' 
  | 'contractual' 
  | 'part_time' 
  | 'guest' 
  | 'visiting';

export type TeacherStatus = 
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
  | 'earned' 
  | 'maternity' 
  | 'special' 
  | 'without_pay';

export type LeaveStatus = 
  | 'pending' 
  | 'approved' 
  | 'rejected' 
  | 'cancelled';

export type TeacherFilters = {
  search?: string;
  department_id?: string;
  status?: TeacherStatus;
  employment_type?: EmploymentType;
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
  per_page?: number;
};

export interface TeacherStatistics {
  total: number;
  active: number;
  pending: number;
  on_leave: number;
}
