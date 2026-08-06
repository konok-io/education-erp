/**
 * Admission Management Types
 */

export interface AdmissionCampaign {
  id: string;
  title: string;
  title_bn?: string;
  application_fee: number;
  late_fee: number;
  start_date: string;
  end_date: string;
  result_date?: string;
  admission_date?: string;
  total_seats: number;
  status: CampaignStatus;
  is_active: boolean;
  description?: string;
  requirements?: string;
  eligibility_criteria?: string;
  session?: { id: string; title: string };
  academic_level?: { id: string; name: string };
}

export interface AdmissionApplication {
  id: string;
  application_no: string;
  applicant_name: string;
  father_name: string;
  mother_name: string;
  guardian_name?: string;
  guardian_relation?: string;
  date_of_birth: string;
  gender: 'male' | 'female' | 'other';
  religion?: string;
  nationality?: string;
  blood_group?: string;
  email: string;
  mobile: string;
  present_address?: string;
  permanent_address?: string;
  ssc_gpa?: number;
  ssc_board?: string;
  ssc_group?: string;
  ssc_passing_year?: number;
  hsc_gpa?: number;
  hsc_board?: string;
  hsc_group?: string;
  hsc_passing_year?: number;
  quota: QuotaType;
  selected_shift?: string;
  status: ApplicationStatus;
  payment_status: PaymentStatus;
  payment_amount?: number;
  payment_date?: string;
  payment_method?: string;
  transaction_id?: string;
  merit_position?: number;
  is_waiting: boolean;
  waiting_position?: number;
  interview_date?: string;
  interview_time?: string;
  interview_venue?: string;
  remarks?: string;
  submitted_at?: string;
  campaign?: { id: string; title: string };
  documents?: AdmissionDocument[];
  payments?: AdmissionPayment[];
  created_at: string;
}

export interface AdmissionDocument {
  id: string;
  application_id: string;
  document_type: DocumentType;
  document_name: string;
  file_path?: string;
  file_name?: string;
  file_size?: number;
  is_verified: boolean;
  verified_at?: string;
  rejection_reason?: string;
}

export interface AdmissionPayment {
  id: string;
  application_id: string;
  payment_no: string;
  amount: number;
  payment_type: PaymentType;
  payment_method: PaymentMethod;
  transaction_id?: string;
  bank_name?: string;
  payment_date?: string;
  status: PaymentStatus;
  verified_at?: string;
  refund_amount?: number;
  refund_date?: string;
}

export interface QuotaConfiguration {
  id: string;
  quota_type: QuotaType;
  campaign_id: string;
  percentage: number;
  reserved_seats: number;
  min_gpa: number;
  required_documents?: string[];
  is_active: boolean;
}

export interface ApplicantDashboard {
  application: {
    no: string;
    status: ApplicationStatus;
    payment_status: PaymentStatus;
    merit_position?: number;
    is_waiting: boolean;
    interview_date?: string;
    interview_venue?: string;
  };
  campaign?: {
    title: string;
    application_fee: number;
  };
  documents: {
    type: DocumentType;
    name: string;
    is_verified: boolean;
  }[];
  payments: {
    amount: number;
    method: PaymentMethod;
    status: PaymentStatus;
  }[];
}

export interface AdmissionDashboardStats {
  total_applications: number;
  pending: number;
  approved: number;
  paid: number;
  merit: number;
}

export interface MeritListItem {
  position: number;
  application_no: string;
  name: string;
  gpa: number;
  status: ApplicationStatus;
}

export interface AdmissionReport {
  total: number;
  by_status: Record<string, number>;
  by_quota: Record<string, number>;
  by_gender: Record<string, number>;
}

export type CampaignStatus = 
  | 'draft' 
  | 'open' 
  | 'closed' 
  | 'processing' 
  | 'published' 
  | 'completed' 
  | 'archived';

export type ApplicationStatus = 
  | 'draft' 
  | 'submitted' 
  | 'pending_payment' 
  | 'pending_document' 
  | 'document_verified' 
  | 'test_scheduled' 
  | 'test_completed' 
  | 'interview_scheduled' 
  | 'merit' 
  | 'waiting' 
  | 'rejected' 
  | 'approved' 
  | 'admitted' 
  | 'cancelled';

export type PaymentStatus = 'unpaid' | 'pending' | 'paid' | 'failed' | 'refunded' | 'cancelled';

export type QuotaType = 
  | 'general' 
  | 'freedom_fighter' 
  | 'tribal' 
  | 'disabled' 
  | 'women' 
  | 'employee';

export type DocumentType = 
  | 'photo' 
  | 'signature' 
  | 'ssc_certificate' 
  | 'ssc_marksheet' 
  | 'hsc_certificate' 
  | 'hsc_marksheet' 
  | 'birth_certificate' 
  | 'nid' 
  | 'passport' 
  | 'character_certificate' 
  | 'tc' 
  | 'quota_certificate' 
  | 'other';

export type PaymentType = 'application' | 'admission' | 'late' | 'processing';

export type PaymentMethod = 'bkash' | 'nagad' | 'rocket' | 'sslcommerz' | 'cash' | 'bank';

export const QUOTA_LABELS: Record<QuotaType, string> = {
  general: 'General',
  freedom_fighter: 'Freedom Fighter',
  tribal: 'Tribal',
  disabled: 'Disabled',
  women: 'Women',
  employee: 'Employee Children',
};

export const STATUS_LABELS: Record<ApplicationStatus, string> = {
  draft: 'Draft',
  submitted: 'Submitted',
  pending_payment: 'Pending Payment',
  pending_document: 'Pending Document',
  document_verified: 'Document Verified',
  test_scheduled: 'Test Scheduled',
  test_completed: 'Test Completed',
  interview_scheduled: 'Interview Scheduled',
  merit: 'In Merit List',
  waiting: 'Waiting List',
  rejected: 'Rejected',
  approved: 'Approved',
  admitted: 'Admitted',
  cancelled: 'Cancelled',
};

export const DOCUMENT_LABELS: Record<DocumentType, string> = {
  photo: 'Applicant Photo',
  signature: 'Signature',
  ssc_certificate: 'SSC Certificate',
  ssc_marksheet: 'SSC Marksheet',
  hsc_certificate: 'HSC Certificate',
  hsc_marksheet: 'HSC Marksheet',
  birth_certificate: 'Birth Certificate',
  nid: 'National ID',
  passport: 'Passport',
  character_certificate: 'Character Certificate',
  tc: 'Transfer Certificate',
  quota_certificate: 'Quota Certificate',
  other: 'Other',
};
