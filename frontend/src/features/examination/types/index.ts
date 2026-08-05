/**
 * Examination Types
 */

export interface ExamSession {
  id: string;
  session_name: string;
  academic_year: string;
  semester?: string;
  term?: string;
  start_date?: string;
  end_date?: string;
  description?: string;
  status: SessionStatus;
  status_label?: string;
  is_current: boolean;
  exams_count?: number;
}

export interface Exam {
  id: string;
  exam_name: string;
  exam_code: string;
  exam_type: ExamType;
  exam_type_label?: string;
  exam_session_id?: string;
  exam_session?: { id: string; session_name: string };
  class_id?: number;
  section_id?: number;
  start_date: string;
  end_date: string;
  result_publish_date?: string;
  description?: string;
  instructions?: string;
  status: ExamStatus;
  status_label?: string;
  is_published: boolean;
  subjects_count?: number;
}

export interface ExamSubject {
  id: string;
  exam_id?: string;
  exam?: { id: string; exam_name: string };
  subject_id?: number;
  subject_code?: string;
  subject_name: string;
  exam_date: string;
  start_time: string;
  end_time: string;
  duration_minutes: number;
  duration?: string;
  full_marks: number;
  pass_marks: number;
  practical_marks: number;
  theory_marks: number;
  exam_mode: ExamMode;
  exam_mode_label?: string;
  syllabus?: string;
  status: ExamStatus;
  status_label?: string;
}

export interface ExamHall {
  id: string;
  hall_name: string;
  hall_code: string;
  building?: string;
  floor?: string;
  room_no?: string;
  capacity: number;
  rows: number;
  columns: number;
  total_seats?: number;
  description?: string;
  status: HallStatus;
  status_label?: string;
  is_active: boolean;
}

export interface ExamCommittee {
  id: string;
  committee_name: string;
  committee_code: string;
  exam_session_id?: string;
  exam_session?: { id: string; session_name: string };
  chairman_id?: number;
  chairman?: { id: number; name: string };
  controller_id?: number;
  controller?: { id: number; name: string };
  coordinator_id?: number;
  coordinator?: { id: number; name: string };
  responsibilities?: string;
  description?: string;
  effective_from?: string;
  effective_to?: string;
  status: string;
}

export interface ExamInvigilator {
  id: string;
  exam_id?: string;
  exam?: { id: string; exam_name: string };
  user_id?: number;
  user?: { id: number; name: string };
  exam_hall_id?: string;
  hall?: { id: string; hall_name: string };
  exam_subject_id?: string;
  subject?: { id: string; subject_name: string };
  duty_date: string;
  reporting_time?: string;
  role: InvigilatorRole;
  status: InvigilatorStatus;
}

export interface ExamSeatPlan {
  id: string;
  exam_id?: string;
  exam?: { id: string; exam_name: string };
  exam_subject_id?: string;
  subject?: { id: string; subject_name: string };
  exam_hall_id?: string;
  hall?: { id: string; hall_name: string };
  row_number: number;
  column_number: number;
  seat_number: string;
  student_type?: string;
  student_id?: number;
  student_name?: string;
  student_roll?: string;
  registration_no?: string;
  remarks?: string;
}

export interface ExamAdmitCard {
  id: string;
  admit_card_no: string;
  exam_id?: string;
  exam?: { id: string; exam_name: string; exam_code: string };
  student_id?: number;
  student_name: string;
  student_roll: string;
  registration_no?: string;
  class_name?: string;
  section?: string;
  photo?: string;
  signature?: string;
  qr_code?: string;
  barcode?: string;
  verification_url?: string;
  issue_date?: string;
  valid_until?: string;
  status: AdmitCardStatus;
  status_label?: string;
  remarks?: string;
}

export interface ExamAttendance {
  id: string;
  exam_subject_id?: string;
  subject?: { id: string; subject_name: string };
  exam_hall_id?: string;
  hall?: { id: string; hall_name: string };
  student_id?: number;
  student_name?: string;
  student_roll?: string;
  registration_no?: string;
  seat_number?: string;
  status: AttendanceStatus;
  arrival_time?: string;
  remarks?: string;
  recorded_by?: { id: number; name: string };
}

export interface ExamMark {
  id: string;
  exam_subject_id?: string;
  subject?: {
    id: string;
    subject_name: string;
    full_marks: number;
    pass_marks: number;
  };
  student_id?: number;
  student_name: string;
  student_roll: string;
  theory_marks?: number;
  practical_marks?: number;
  total_marks?: number;
  pass_marks?: number;
  result?: MarkResult;
  grade?: string;
  teacher_remarks?: string;
  moderator_remarks?: string;
  status: MarkStatus;
  status_label?: string;
  entered_by?: { id: number; name: string };
  verified_by?: { id: number; name: string };
  approved_by?: { id: number; name: string };
  entered_at?: string;
  verified_at?: string;
  approved_at?: string;
}

export interface ExamMalpractice {
  id: string;
  exam_subject_id?: string;
  subject?: { id: string; subject_name: string };
  exam_hall_id?: string;
  hall?: { id: string; hall_name: string };
  student_id?: number;
  student_name: string;
  student_roll: string;
  seat_number?: string;
  incident_type: IncidentType;
  description: string;
  evidence?: string;
  invigilator_id?: number;
  invigilator?: { id: number; name: string };
  action_taken?: string;
  remarks?: string;
  status: MalpracticeStatus;
}

export interface ExamDashboard {
  total_exams: number;
  upcoming_exams: number;
  ongoing_exams: number;
  completed_exams: number;
  total_halls: number;
  total_seats: number;
  total_invigilators: number;
  total_students: number;
  today_exams: number;
  pending_marks: number;
  pending_admit_cards: number;
}

// Enums
export type SessionStatus = 'upcoming' | 'ongoing' | 'completed';
export type ExamType = 'class_test' | 'monthly' | 'weekly' | 'tutorial' | 'mid_term' | 'pre_test' | 'test' | 'final' | 'board_prep' | 'semester_final' | 'improvement' | 'retake';
export type ExamStatus = 'scheduled' | 'ongoing' | 'completed' | 'cancelled';
export type ExamMode = 'written' | 'practical' | 'viva' | 'project' | 'both';
export type HallStatus = 'active' | 'inactive' | 'reserved';
export type InvigilatorRole = 'invigilator' | 'senior_invigilator' | 'room_incharge';
export type InvigilatorStatus = 'assigned' | 'confirmed' | 'completed' | 'absent';
export type AdmitCardStatus = 'issued' | 'downloaded' | 'used' | 'expired';
export type AttendanceStatus = 'present' | 'absent' | 'late' | 'exempted';
export type MarkResult = 'pass' | 'fail' | 'absent';
export type MarkStatus = 'draft' | 'submitted' | 'verified' | 'approved' | 'locked' | 'published';
export type IncidentType = 'cheating' | 'late_entry' | 'mobile_phone' | 'identity_fraud' | 'behavior' | 'other';
export type MalpracticeStatus = 'reported' | 'under_investigation' | 'resolved' | 'dismissed';

// Constants
export const EXAM_TYPES: Record<ExamType, string> = {
  class_test: 'Class Test',
  monthly: 'Monthly',
  weekly: 'Weekly',
  tutorial: 'Tutorial',
  mid_term: 'Mid Term',
  pre_test: 'Pre-Test',
  test: 'Test',
  final: 'Final',
  board_prep: 'Board Preparation',
  semester_final: 'Semester Final',
  improvement: 'Improvement',
  retake: 'Retake',
};

export const EXAM_MODES: Record<ExamMode, string> = {
  written: 'Written',
  practical: 'Practical',
  viva: 'Viva',
  project: 'Project',
  both: 'Written + Practical',
};

export const EXAM_STATUSES: Record<ExamStatus, string> = {
  scheduled: 'Scheduled',
  ongoing: 'Ongoing',
  completed: 'Completed',
  cancelled: 'Cancelled',
};

export const HALL_STATUSES: Record<HallStatus, string> = {
  active: 'Active',
  inactive: 'Inactive',
  reserved: 'Reserved',
};

export const MARK_STATUSES: Record<MarkStatus, string> = {
  draft: 'Draft',
  submitted: 'Submitted',
  verified: 'Verified',
  approved: 'Approved',
  locked: 'Locked',
  published: 'Published',
};

export const ATTENDANCE_STATUSES: Record<AttendanceStatus, string> = {
  present: 'Present',
  absent: 'Absent',
  late: 'Late',
  exempted: 'Exempted',
};

export const ADMIT_CARD_STATUSES: Record<AdmitCardStatus, string> = {
  issued: 'Issued',
  downloaded: 'Downloaded',
  used: 'Used',
  expired: 'Expired',
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
