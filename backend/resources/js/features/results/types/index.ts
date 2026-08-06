/**
 * Result & Examination Types
 */

export interface Exam {
  id: string;
  exam_code: string;
  exam_name: string;
  exam_type: ExamType;
  start_date: string;
  end_date?: string;
  total_marks: number;
  pass_marks: number;
  status: ExamStatus;
  is_published: boolean;
  session?: { id: string; title: string };
  academic_level?: { id: string; name: string };
  program?: { id: string; name: string };
  semester?: { id: string; title: string };
}

export interface Result {
  id: string;
  result_no: string;
  total_marks: number;
  obtained_marks: number;
  gpa: number;
  grade: string;
  status: ResultStatus;
  is_published: boolean;
  student?: { id: string; student_no: string; name: string };
  exam?: { id: string; name: string; type: string };
  class?: { id: string; name: string };
  section?: { id: string; name: string };
  details?: ResultDetail[];
}

export interface ResultDetail {
  id: string;
  subject: string;
  total_marks: number;
  obtained_marks: number;
  theory_marks?: number;
  practical_marks?: number;
  viva_marks?: number;
  attendance_marks?: number;
  assignment_marks?: number;
  internal_marks?: number;
  grade: string;
  grade_point: number;
  credit: number;
  is_pass: boolean;
}

export interface MarkEntryItem {
  student_id: string;
  theory?: number;
  practical?: number;
  viva?: number;
  attendance?: number;
  assignment?: number;
  internal?: number;
  teacher_id?: string;
}

export interface GradeRule {
  id: string;
  name: string;
  scale_type: string;
  is_default: boolean;
  ranges: GradeRange[];
}

export interface GradeRange {
  id: string;
  grade: string;
  min_percentage: number;
  max_percentage: number;
  grade_point: number;
}

export interface MeritListItem {
  position: number;
  student_id: string;
  student_no: string;
  name: string;
  obtained_marks: number;
  gpa: number;
  grade: string;
}

export interface FailListItem {
  student: string;
  student_no: string;
  subject: string;
  obtained_marks: number;
  pass_marks: number;
}

export interface Transcript {
  student: {
    id: string;
    name: string;
    student_no: string;
    class: string;
    section: string;
  };
  cgpa: number;
  total_credits: number;
  results: {
    semester: string;
    subject: string;
    credit: number;
    grade: string;
    point: number;
  }[];
}

export interface Marksheet {
  student: {
    id: string;
    name: string;
    student_no: string;
    class: string;
    section: string;
  };
  exam: {
    name: string;
    date: string;
  };
  total_marks: number;
  obtained_marks: number;
  gpa: number;
  grade: string;
  status: string;
  details: {
    subject: string;
    total: number;
    obtained: number;
    grade: string;
    point: number;
  }[];
}

export interface GPAResult {
  gpa: number;
  total_credits: number;
  subjects: {
    subject: string;
    grade: string;
    point: number;
    credit: number;
  }[];
}

export interface CGPAResult {
  cgpa: number;
  total_credits: number;
  total_subjects: number;
}

export interface ResultAnalytics {
  total_students: number;
  passed: number;
  failed: number;
  pass_rate: number;
  average_gpa: number;
  highest_gpa: number;
  lowest_gpa: number;
  grade_distribution: Record<string, number>;
}

export type ExamType = 
  | 'class_test' 
  | 'monthly' 
  | 'mid_term' 
  | 'pre_test' 
  | 'test_exam' 
  | 'final' 
  | 'semester_final' 
  | 'improvement' 
  | 'supplementary' 
  | 'board';

export type ExamStatus = 'draft' | 'pending' | 'ongoing' | 'completed' | 'cancelled';

export type ResultStatus = 'draft' | 'pending' | 'verified' | 'approved' | 'published' | 'archived';
