/**
 * Attendance Types
 */

export interface Attendance {
  id: string;
  attendance_no: string;
  attendance_type: AttendanceType;
  attendance_date: string;
  attendance_time: string;
  status: AttendanceStatus;
  late_minutes: number;
  entry_method: EntryMethod;
  remarks?: string;
  is_approved: boolean;
  approved_at?: string;
  student?: StudentInfo;
  teacher?: TeacherInfo;
  employee?: EmployeeInfo;
  session?: { id: string; title: string };
  class?: { id: string; name: string };
  section?: { id: string; name: string };
  subject?: { id: string; name: string };
  created_at: string;
  updated_at: string;
}

export interface StudentInfo {
  id: string;
  student_no: string;
  name: string;
}

export interface TeacherInfo {
  id: string;
  teacher_no: string;
  name: string;
}

export interface EmployeeInfo {
  id: string;
  employee_no: string;
  name: string;
}

export interface AttendanceCorrection {
  id: string;
  attendance_id: string;
  old_status: AttendanceStatus;
  new_status: AttendanceStatus;
  reason: string;
  status: CorrectionStatus;
  review_notes?: string;
  created_at: string;
}

export interface AttendanceMarkItem {
  student_id?: string;
  teacher_id?: string;
  employee_id?: string;
  status: AttendanceStatus;
  late_minutes?: number;
  remarks?: string;
}

export interface AttendanceSummary {
  date: string;
  total_students?: number;
  total_teachers?: number;
  total_employees?: number;
  total: number;
  present: number;
  absent: number;
  late: number;
  leave: number;
  half_day: number;
  unmarked?: number;
  percentage: number;
}

export interface AttendanceReport {
  total: number;
  present: number;
  absent: number;
  late: number;
  leave: number;
  half_day: number;
  present_percentage: number;
  records: Attendance[];
}

export interface AttendanceAnalytics {
  total: number;
  present: number;
  absent: number;
  late: number;
  leave: number;
  percentage: number;
  by_status: Record<string, number>;
  by_date: Record<string, number>;
}

export interface DashboardStats {
  student: AttendanceStats;
  teacher: AttendanceStats;
  employee: AttendanceStats;
  pending_approvals: number;
  pending_corrections: number;
}

export interface AttendanceStats {
  total: number;
  present: number;
  absent: number;
  late: number;
}

export type AttendanceType = 'student' | 'teacher' | 'employee';

export type AttendanceStatus = 
  | 'present' 
  | 'absent' 
  | 'late' 
  | 'leave' 
  | 'half_day' 
  | 'holiday' 
  | 'weekend' 
  | 'exam_duty' 
  | 'official_tour' 
  | 'remote';

export type EntryMethod = 
  | 'manual' 
  | 'qr' 
  | 'barcode' 
  | 'rfid' 
  | 'fingerprint' 
  | 'face' 
  | 'gps' 
  | 'api' 
  | 'mobile';

export type CorrectionStatus = 'pending' | 'approved' | 'rejected';

export type AttendanceFilters = {
  type?: AttendanceType;
  date?: string;
  session_id?: string;
  class_id?: string;
  section_id?: string;
  subject_id?: string;
  status?: AttendanceStatus;
  is_approved?: boolean;
  entry_method?: EntryMethod;
  per_page?: number;
};
