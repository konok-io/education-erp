/**
 * Student Types
 */

export interface Student {
  id: string;
  student_no: string;
  status: StudentStatus;
  admission_date: string;
  remarks?: string;
  full_name?: string;
  photo_url?: string;
  profile?: StudentProfile;
  guardian?: Guardian;
  medical?: StudentMedical;
  documents?: StudentDocument[];
  session?: { id: string; title: string };
  academic_level?: { id: string; name: string };
  faculty?: { id: string; name: string };
  department?: { id: string; name: string };
  program?: { id: string; name: string; code: string };
  semester?: { id: string; title: string };
  class?: { id: string; name: string };
  section?: { id: string; name: string };
  group?: { id: string; name: string };
  campus?: { id: string; name: string; code: string };
  created_at: string;
  updated_at: string;
}

export interface StudentProfile {
  id: string;
  first_name: string;
  last_name?: string;
  full_name: string;
  first_name_bn?: string;
  last_name_bn?: string;
  full_name_bn?: string;
  gender: 'male' | 'female' | 'other';
  date_of_birth?: string;
  age?: number;
  blood_group?: string;
  religion?: string;
  nationality?: string;
  birth_certificate?: string;
  nid?: string;
  passport?: string;
  photo?: string;
  photo_url?: string;
  signature?: string;
  signature_url?: string;
  email?: string;
  mobile?: string;
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

export interface Guardian {
  id: string;
  guardian_type: 'father' | 'mother' | 'guardian' | 'other';
  name: string;
  name_bn?: string;
  relation?: string;
  occupation?: string;
  organization?: string;
  designation?: string;
  mobile: string;
  email?: string;
  nid?: string;
  annual_income?: number;
  photo?: string;
  address?: Address;
  is_emergency_contact: boolean;
}

export interface StudentMedical {
  id: string;
  height?: number;
  weight?: number;
  blood_group?: string;
  allergy: boolean;
  allergy_details?: string;
  chronic_disease: boolean;
  chronic_disease_details?: string;
  disability: boolean;
  disability_details?: string;
  medication?: string;
  medical_note?: string;
  last_checkup_date?: string;
  doctor_name?: string;
  doctor_phone?: string;
}

export interface StudentDocument {
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
  is_expired: boolean;
  is_verified: boolean;
  verified_by?: string;
  verified_at?: string;
  notes?: string;
}

export interface StudentPromotion {
  id: string;
  from_session: { id: string; title: string };
  to_session: { id: string; title: string };
  from_semester?: { id: string; title: string };
  to_semester?: { id: string; title: string };
  from_class: { id: string; name: string };
  to_class: { id: string; name: string };
  from_section?: { id: string; name: string };
  to_section?: { id: string; name: string };
  from_group?: { id: string; name: string };
  to_group?: { id: string; name: string };
  result?: any;
  status: 'promoted' | 'retained' | 'conditional';
  promoted_by: number;
  promotion_date: string;
  remarks?: string;
}

export interface StudentTransfer {
  id: string;
  transfer_type: 'campus' | 'department' | 'program' | 'class' | 'section' | 'group';
  from_campus?: { id: string; name: string };
  to_campus?: { id: string; name: string };
  from_department?: { id: string; name: string };
  to_department?: { id: string; name: string };
  from_program?: { id: string; name: string };
  to_program?: { id: string; name: string };
  from_class?: { id: string; name: string };
  to_class?: { id: string; name: string };
  from_section?: { id: string; name: string };
  to_section?: { id: string; name: string };
  reason: string;
  transfer_date: string;
  approved_by: number;
  status: 'pending' | 'approved' | 'rejected';
  remarks?: string;
}

export type StudentStatus = 
  | 'pending' 
  | 'active' 
  | 'inactive' 
  | 'transferred' 
  | 'graduated' 
  | 'suspended' 
  | 'expelled' 
  | 'dropped' 
  | 'alumni';

export type StudentFilters = {
  search?: string;
  session_id?: string;
  academic_level_id?: string;
  department_id?: string;
  program_id?: string;
  class_id?: string;
  section_id?: string;
  group_id?: string;
  status?: StudentStatus;
  gender?: 'male' | 'female' | 'other';
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
  per_page?: number;
};

export interface StudentStatistics {
  total: number;
  active: number;
  pending: number;
  transferred: number;
  graduated: number;
}
