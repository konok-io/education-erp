/**
 * Certificate Types
 */

export interface Certificate {
  id: string;
  certificate_number: string;
  certificate_type: CertificateType;
  certificate_type_label?: string;
  template?: { id: string; template_name: string };
  student_id?: number;
  student_name?: string;
  student_roll?: string;
  registration_no?: string;
  father_name?: string;
  mother_name?: string;
  department?: string;
  class_name?: string;
  section?: string;
  session?: string;
  semester?: string;
  academic_year?: string;
  content?: string;
  metadata?: Record<string, any>;
  verification_url?: string;
  pdf_path?: string;
  signature?: { id: string; signatory_name: string; designation: string };
  seal?: { id: string; seal_name: string };
  issue_date?: string;
  valid_until?: string;
  reason?: string;
  conduct?: string;
  status: CertificateStatus;
  status_label?: string;
  approver?: { id: number; name: string };
  approved_at?: string;
  issuer?: { id: number; name: string };
  issued_at?: string;
  created_at: string;
  updated_at: string;
}

export interface CertificateTemplate {
  id: string;
  template_name: string;
  template_code: string;
  certificate_type: CertificateType;
  template_content?: string;
  template_config?: Record<string, any>;
  background_image?: string;
  header_logo?: string;
  footer_image?: string;
  digital_seal?: string;
  signature_positions?: string;
  qr_position?: string;
  barcode_position?: string;
  css_styles?: string;
  status: TemplateStatus;
  is_default: boolean;
  created_at: string;
  updated_at: string;
}

export interface Transcript {
  id: string;
  transcript_number: string;
  student_id?: number;
  student_name: string;
  student_roll: string;
  registration_no?: string;
  father_name?: string;
  mother_name?: string;
  department?: string;
  program?: string;
  session?: string;
  duration?: string;
  semester_results?: SemesterResult[];
  total_credits?: number;
  cgpa?: number;
  gpa?: number;
  result_status?: ResultStatus;
  result_status_label?: string;
  remarks?: string;
  verification_url?: string;
  pdf_path?: string;
  signature?: { id: string; signatory_name: string; designation: string };
  seal?: { id: string; seal_name: string };
  issue_date?: string;
  status: TranscriptStatus;
  status_label?: string;
  approver?: { id: number; name: string };
  approved_at?: string;
  created_at: string;
  updated_at: string;
}

export interface Marksheet {
  id: string;
  marksheet_number: string;
  student_id?: number;
  student_name: string;
  student_roll: string;
  registration_no?: string;
  father_name?: string;
  mother_name?: string;
  department?: string;
  class_name?: string;
  session?: string;
  semester?: string;
  subject_marks?: SubjectMark[];
  total_marks?: number;
  obtained_marks?: number;
  grade?: string;
  gpa?: number;
  result_status?: ResultStatus;
  result_status_label?: string;
  remarks?: string;
  verification_url?: string;
  pdf_path?: string;
  signature?: { id: string; signatory_name: string; designation: string };
  seal?: { id: string; seal_name: string };
  issue_date?: string;
  status: MarksheetStatus;
  status_label?: string;
  approver?: { id: number; name: string };
  approved_at?: string;
  created_at: string;
  updated_at: string;
}

export interface DigitalSignature {
  id: string;
  signature_name: string;
  signatory_name: string;
  designation: string;
  department?: string;
  signature_image: string;
  signature_type: SignatureType;
  digital_certificate?: string;
  valid_from?: string;
  valid_until?: string;
  metadata?: Record<string, any>;
  status: SignatureStatus;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface DigitalSeal {
  id: string;
  seal_name: string;
  seal_code: string;
  institution_name: string;
  seal_image: string;
  seal_type: SealType;
  encryption_key?: string;
  metadata?: Record<string, any>;
  status: SealStatus;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface CertificateArchive {
  id: string;
  document_type: string;
  document_number?: string;
  student_id?: number;
  student_name?: string;
  student_roll?: string;
  document_category?: string;
  file_path: string;
  file_type?: string;
  file_size?: number;
  file_hash?: string;
  storage_type: StorageType;
  cloud_url?: string;
  description?: string;
  metadata?: Record<string, any>;
  version: string;
  status: ArchiveStatus;
  uploader?: { id: number; name: string };
  created_at: string;
  updated_at: string;
}

export interface CertificateVerification {
  id: string;
  certificate_number?: string;
  verification_token?: string;
  verifier_name?: string;
  verifier_email?: string;
  verifier_ip?: string;
  verification_method: VerificationMethod;
  verified_at?: string;
  status: VerificationStatus;
  remarks?: string;
}

export interface DuplicateCertificateRequest {
  id: string;
  request_number: string;
  certificate_type: CertificateType;
  student_id?: number;
  student_name: string;
  student_roll: string;
  registration_no?: string;
  father_name?: string;
  phone?: string;
  email?: string;
  reason?: string;
  description?: string;
  police_clearance?: string;
  newspaper_ad?: string;
  fee_amount?: number;
  payment_status: PaymentStatus;
  status: DuplicateRequestStatus;
  admin_remarks?: string;
  reviewer?: { id: number; name: string };
  reviewed_at?: string;
  created_at: string;
  updated_at: string;
}

export interface CertificateDashboard {
  total_certificates: number;
  certificates_issued: number;
  pending_approval: number;
  total_transcripts: number;
  transcripts_issued: number;
  total_marksheets: number;
  marksheets_issued: number;
  today_downloads: number;
  verifications_today: number;
  pending_duplicates: number;
  active_templates: number;
  active_signatures: number;
  active_seals: number;
}

export interface SemesterResult {
  semester: string;
  subjects: SubjectMark[];
  total_marks: number;
  obtained_marks: number;
  gpa: number;
}

export interface SubjectMark {
  subject_code: string;
  subject_name: string;
  full_marks: number;
  obtained_marks: number;
  grade: string;
  gpa: number;
}

// Enums
export type CertificateType = 'transfer' | 'character' | 'testimonial' | 'bonafide' | 'course_completion' | 'internship' | 'experience' | 'migration' | 'provisional' | 'passing' | 'merit' | 'appreciation' | 'participation';
export type CertificateStatus = 'draft' | 'pending_approval' | 'approved' | 'issued' | 'rejected' | 'revoked';
export type TemplateStatus = 'active' | 'inactive' | 'draft';
export type TranscriptStatus = 'draft' | 'pending_approval' | 'approved' | 'issued';
export type MarksheetStatus = 'draft' | 'pending_approval' | 'approved' | 'issued';
export type ResultStatus = 'passed' | 'failed' | 'promoted' | 'incomplete';
export type SignatureType = 'image' | 'digital' | 'qr';
export type SignatureStatus = 'active' | 'inactive' | 'expired';
export type SealType = 'official' | 'academic' | 'controller' | 'principal';
export type SealStatus = 'active' | 'inactive';
export type StorageType = 'local' | 'cloud' | 'both';
export type ArchiveStatus = 'active' | 'archived' | 'deleted';
export type VerificationMethod = 'qr' | 'number' | 'manual';
export type VerificationStatus = 'success' | 'failed' | 'invalid';
export type PaymentStatus = 'pending' | 'paid' | 'exempted';
export type DuplicateRequestStatus = 'pending' | 'verified' | 'approved' | 'rejected' | 'issued';

// Constants
export const CERTIFICATE_TYPES: Record<CertificateType, string> = {
  transfer: 'Transfer Certificate',
  character: 'Character Certificate',
  testimonial: 'Testimonial',
  bonafide: 'Bonafide Certificate',
  course_completion: 'Course Completion Certificate',
  internship: 'Internship Certificate',
  experience: 'Experience Certificate',
  migration: 'Migration Certificate',
  provisional: 'Provisional Certificate',
  passing: 'Passing Certificate',
  merit: 'Merit Certificate',
  appreciation: 'Appreciation Certificate',
  participation: 'Participation Certificate',
};

export const CERTIFICATE_STATUSES: Record<CertificateStatus, string> = {
  draft: 'Draft',
  pending_approval: 'Pending Approval',
  approved: 'Approved',
  issued: 'Issued',
  rejected: 'Rejected',
  revoked: 'Revoked',
};

export const TRANSCRIPT_STATUSES: Record<TranscriptStatus, string> = {
  draft: 'Draft',
  pending_approval: 'Pending Approval',
  approved: 'Approved',
  issued: 'Issued',
};

export const MARKSHEET_STATUSES: Record<MarksheetStatus, string> = {
  draft: 'Draft',
  pending_approval: 'Pending Approval',
  approved: 'Approved',
  issued: 'Issued',
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
