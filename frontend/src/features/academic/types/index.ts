/**
 * Academic Types
 */

export interface AcademicLevel {
  id: string;
  name: string;
  short_name: string;
  code: string;
  education_type: string;
  duration: number;
  status: string;
  sort_order: number;
  created_at: string;
  updated_at: string;
}

export interface Faculty {
  id: string;
  name: string;
  code: string;
  description: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface Department {
  id: string;
  faculty_id: string;
  faculty?: Faculty;
  name: string;
  code: string;
  description: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface Program {
  id: string;
  department_id: string;
  academic_level_id: string;
  department?: Department;
  academic_level?: AcademicLevel;
  name: string;
  code: string;
  duration: number;
  credit: number;
  description: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface AcademicSession {
  id: string;
  title: string;
  code: string;
  start_date: string;
  end_date: string;
  is_current: boolean;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface Semester {
  id: string;
  program_id: string;
  program?: Program;
  title: string;
  code: string;
  order_no: number;
  duration_months: number;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface AcademicClass {
  id: string;
  program_id: string;
  session_id: string;
  semester_id: string;
  program?: Program;
  session?: AcademicSession;
  semester?: Semester;
  name: string;
  code: string;
  capacity: number;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface Section {
  id: string;
  class_id: string;
  academic_class?: AcademicClass;
  name: string;
  capacity: number;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface Group {
  id: string;
  class_id: string;
  academic_class?: AcademicClass;
  name: string;
  code: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface SubjectCategory {
  id: string;
  name: string;
  code: string;
  description: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface Subject {
  id: string;
  subject_code: string;
  subject_name: string;
  subject_name_bn: string;
  department_id: string;
  subject_category_id: string;
  department?: Department;
  category?: SubjectCategory;
  credit: number;
  full_marks: number;
  pass_marks: number;
  theory_marks: number;
  practical_marks: number;
  is_optional: boolean;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface GradeRule {
  id: string;
  academic_level_id: string;
  academic_level?: AcademicLevel;
  grade_name: string;
  grade_point: number;
  min_percentage: number;
  max_percentage: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface GpaRule {
  id: string;
  academic_level_id: string;
  academic_level?: AcademicLevel;
  name: string;
  type: string;
  min_gpa: number;
  max_gpa: number;
  fail_gpa: number;
  is_current: boolean;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface AcademicCalendar {
  id: string;
  session_id: string;
  session?: AcademicSession;
  title: string;
  description: string;
  event_type: string;
  start_date: string;
  end_date: string;
  is_holiday: boolean;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface AcademicHierarchy {
  academic_levels: AcademicLevel[];
  faculties: Faculty[];
  departments: Department[];
  programs: Program[];
  sessions: AcademicSession[];
  semesters: Semester[];
}

export interface ProgramSubject {
  id: string;
  program_id: string;
  semester_id: string;
  subject_id: string;
  program?: Program;
  semester?: Semester;
  subject?: Subject;
  is_compulsory: boolean;
  status: string;
  created_at: string;
  updated_at: string;
}

export type AcademicFilters = {
  search?: string;
  status?: string;
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
  per_page?: number;
};
