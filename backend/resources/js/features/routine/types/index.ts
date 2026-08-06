/**
 * Routine & Scheduling Types
 */

export interface Routine {
  id: string;
  routine_code: string;
  routine_type: RoutineType;
  day_of_week: number;
  day_name: string;
  version: number;
  is_published: boolean;
  published_at?: string;
  status: RoutineStatus;
  remarks?: string;
  class?: { id: string; name: string };
  section?: { id: string; name: string };
  subject?: { id: string; name: string; code?: string };
  teacher?: { id: string; name: string; teacher_no?: string };
  room?: { id: string; room_number: string; room_name: string; building?: string };
  time_slot?: { id: string; name: string; start_time: string; end_time: string; duration: number };
  period?: { id: string; name: string; number: number };
  created_at: string;
  updated_at: string;
}

export interface TimeSlot {
  id: string;
  slot_name: string;
  start_time: string;
  end_time: string;
  duration_minutes: number;
  break_before: number;
  break_after: number;
  slot_order: number;
  status: string;
}

export interface Period {
  id: string;
  period_name: string;
  period_number: number;
  time_slot_id: string;
  duration_minutes: number;
  is_break: boolean;
  break_type?: string;
  status: string;
}

export interface Room {
  id: string;
  room_number: string;
  room_name: string;
  building?: string;
  floor?: string;
  capacity: number;
  current_capacity: number;
  room_type: RoomType;
  has_projector: boolean;
  has_ac: boolean;
  has_computer: boolean;
  status: string;
}

export interface AcademicCalendar {
  id: string;
  title: string;
  title_bn?: string;
  description?: string;
  event_type: CalendarEventType;
  start_date: string;
  end_date: string;
  is_all_day: boolean;
  color?: string;
  status: string;
}

export interface Holiday {
  id: string;
  title: string;
  title_bn?: string;
  description?: string;
  holiday_type: HolidayType;
  date: string;
  end_date?: string;
  is_recurring: boolean;
  recurring_year?: number;
  color?: string;
  status: string;
}

export interface RoutineDay {
  day: string;
  classes: RoutineClass[];
}

export interface RoutineClass {
  id: string;
  time: string;
  subject: string;
  teacher?: string;
  room?: string;
  type: RoutineType;
}

export interface RoutineFilters {
  session_id?: string;
  class_id?: string;
  section_id?: string;
  teacher_id?: string;
  day_of_week?: number;
  routine_type?: RoutineType;
  status?: RoutineStatus;
  is_published?: boolean;
  per_page?: number;
}

export type RoutineType = 
  | 'class' 
  | 'teacher' 
  | 'student' 
  | 'exam' 
  | 'practical' 
  | 'laboratory' 
  | 'special';

export type RoutineStatus = 'draft' | 'published' | 'archived';

export type RoomType = 
  | 'classroom' 
  | 'laboratory' 
  | 'computer_lab' 
  | 'library' 
  | 'seminar_hall' 
  | 'conference_room' 
  | 'other';

export type CalendarEventType = 
  | 'class_start' 
  | 'semester_start' 
  | 'semester_end' 
  | 'exam' 
  | 'holiday' 
  | 'admission' 
  | 'registration' 
  | 'result' 
  | 'event' 
  | 'other';

export type HolidayType = 
  | 'national' 
  | 'weekly' 
  | 'religious' 
  | 'special' 
  | 'emergency';

export const DAYS_OF_WEEK = [
  { value: 0, label: 'Saturday' },
  { value: 1, label: 'Sunday' },
  { value: 2, label: 'Monday' },
  { value: 3, label: 'Tuesday' },
  { value: 4, label: 'Wednesday' },
  { value: 5, label: 'Thursday' },
  { value: 6, label: 'Friday' },
] as const;
