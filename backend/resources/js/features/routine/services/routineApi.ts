/**
 * Routine & Scheduling API
 */

import { apiClient } from '@/lib/api-client';
import type { Routine, TimeSlot, Period, Room, AcademicCalendar, Holiday, RoutineDay, RoutineFilters, RoutineClass } from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== ROUTINE CRUD =====================

export const getRoutines = async (filters?: RoutineFilters): Promise<PaginatedResponse<Routine>> => {
  const response = await apiClient.get('/api/v1/routines', { params: filters });
  return response.data;
};

export const getRoutine = async (uuid: string): Promise<Routine> => {
  const response = await apiClient.get(`/api/v1/routines/${uuid}`);
  return response.data.data;
};

export const createRoutine = async (data: Partial<Routine>): Promise<Routine> => {
  const response = await apiClient.post('/api/v1/routines', data);
  return response.data.data;
};

export const updateRoutine = async (uuid: string, data: Partial<Routine>): Promise<Routine> => {
  const response = await apiClient.put(`/api/v1/routines/${uuid}`, data);
  return response.data.data;
};

export const deleteRoutine = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/routines/${uuid}`);
};

// ===================== BULK =====================

export const bulkCreateRoutines = async (routines: Partial<Routine>[]): Promise<{
  total: number;
  success: number;
  failed: number;
  conflicts: any[];
}> => {
  const response = await apiClient.post('/api/v1/routines/bulk', { routines });
  return response.data.data;
};

// ===================== PUBLISH =====================

export const publishRoutines = async (routine_ids: string[]): Promise<void> => {
  await apiClient.post('/api/v1/routines/publish', { routine_ids });
};

// ===================== GENERATOR =====================

export const generateRoutine = async (data: {
  session_id: string;
  class_id: string;
  section_id?: string;
}): Promise<{ generated: number; conflicts: any[] }> => {
  const response = await apiClient.post('/api/v1/routines/generate', data);
  return response.data.data;
};

// ===================== CONFLICTS =====================

export const checkConflicts = async (data: {
  teacher_id: string;
  day_of_week: number;
  time_slot_id: string;
}): Promise<any> => {
  const response = await apiClient.post('/api/v1/routines/conflicts', data);
  return response.data.data;
};

// ===================== TEACHER/STUDENT/CLASS ROUTINE =====================

export const getTeacherRoutine = async (teacherId: string): Promise<RoutineDay[]> => {
  const response = await apiClient.get(`/api/v1/routines/teacher/${teacherId}`);
  return response.data.data;
};

export const getStudentRoutine = async (studentId: string): Promise<RoutineDay[]> => {
  const response = await apiClient.get(`/api/v1/routines/student/${studentId}`);
  return response.data.data;
};

export const getClassRoutine = async (data: {
  class_id: string;
  section_id?: string;
}): Promise<RoutineDay[]> => {
  const response = await apiClient.get('/api/v1/routines/class', { params: data });
  return response.data.data;
};

// ===================== TIME SLOTS =====================

export const getTimeSlots = async (): Promise<TimeSlot[]> => {
  const response = await apiClient.get('/api/v1/routines/time-slots');
  return response.data.data;
};

export const createTimeSlot = async (data: Partial<TimeSlot>): Promise<TimeSlot> => {
  const response = await apiClient.post('/api/v1/routines/time-slots', data);
  return response.data.data;
};

// ===================== ROOMS =====================

export const getRooms = async (params?: {
  room_type?: string;
  building?: string;
}): Promise<Room[]> => {
  const response = await apiClient.get('/api/v1/routines/rooms', { params });
  return response.data.data;
};

export const createRoom = async (data: Partial<Room>): Promise<Room> => {
  const response = await apiClient.post('/api/v1/routines/rooms', data);
  return response.data.data;
};

// ===================== CALENDAR =====================

export const getCalendar = async (data: {
  session_id?: string;
  start_date: string;
  end_date: string;
}): Promise<AcademicCalendar[]> => {
  const response = await apiClient.get('/api/v1/routines/calendar', { params: data });
  return response.data.data;
};

export const createCalendarEvent = async (data: Partial<AcademicCalendar>): Promise<AcademicCalendar> => {
  const response = await apiClient.post('/api/v1/routines/calendar', data);
  return response.data.data;
};

// ===================== HOLIDAYS =====================

export const getHolidays = async (year?: number): Promise<Holiday[]> => {
  const response = await apiClient.get('/api/v1/routines/holidays', { params: { year } });
  return response.data.data;
};

export const createHoliday = async (data: Partial<Holiday>): Promise<Holiday> => {
  const response = await apiClient.post('/api/v1/routines/holidays', data);
  return response.data.data;
};

export const deleteHoliday = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/routines/holidays/${uuid}`);
};

// ===================== EXPORT =====================

export const exportRoutine = async (data: {
  class_id: string;
  format: 'pdf' | 'excel' | 'csv' | 'ics';
}): Promise<string> => {
  const response = await apiClient.get('/api/v1/routines/export', { params: data });
  return response.data.data.url;
};
