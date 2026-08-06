/**
 * Attendance API
 */

import { apiClient } from '@/lib/api-client';
import type { Attendance, AttendanceFilters, AttendanceMarkItem, AttendanceReport, AttendanceSummary, AttendanceAnalytics, DashboardStats, AttendanceCorrection } from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== LIST =====================

export const getAttendances = async (filters?: AttendanceFilters): Promise<PaginatedResponse<Attendance>> => {
  const response = await apiClient.get('/api/v1/attendance', { params: filters });
  return response.data;
};

// ===================== STUDENT ATTENDANCE =====================

export const markStudentAttendance = async (data: {
  session_id: string;
  class_id: string;
  section_id?: string;
  subject_id?: string;
  date: string;
  attendance: AttendanceMarkItem[];
  entry_method?: string;
}): Promise<{ total: number; success: number; failed: number; errors: string[] }> => {
  const response = await apiClient.post('/api/v1/attendance/student', data);
  return response.data.data;
};

export const getStudentAttendance = async (data: {
  student_id: string;
  session_id?: string;
  class_id?: string;
  start_date: string;
  end_date: string;
}): Promise<Attendance[]> => {
  const response = await apiClient.get(`/api/v1/attendance/student/${data.student_id}`, {
    params: { session_id: data.session_id, class_id: data.class_id, start_date: data.start_date, end_date: data.end_date },
  });
  return response.data.data;
};

// ===================== TEACHER ATTENDANCE =====================

export const markTeacherAttendance = async (data: {
  date: string;
  attendance: AttendanceMarkItem[];
  entry_method?: string;
}): Promise<{ total: number; success: number; failed: number; errors: string[] }> => {
  const response = await apiClient.post('/api/v1/attendance/teacher', data);
  return response.data.data;
};

export const getTeacherAttendance = async (data: {
  teacher_id: string;
  start_date: string;
  end_date: string;
}): Promise<Attendance[]> => {
  const response = await apiClient.get(`/api/v1/attendance/teacher/${data.teacher_id}`, {
    params: { start_date: data.start_date, end_date: data.end_date },
  });
  return response.data.data;
};

// ===================== EMPLOYEE ATTENDANCE =====================

export const markEmployeeAttendance = async (data: {
  date: string;
  attendance: AttendanceMarkItem[];
  entry_method?: string;
}): Promise<{ total: number; success: number; failed: number; errors: string[] }> => {
  const response = await apiClient.post('/api/v1/attendance/employee', data);
  return response.data.data;
};

export const getEmployeeAttendance = async (data: {
  employee_id: string;
  start_date: string;
  end_date: string;
}): Promise<Attendance[]> => {
  const response = await apiClient.get(`/api/v1/attendance/employee/${data.employee_id}`, {
    params: { start_date: data.start_date, end_date: data.end_date },
  });
  return response.data.data;
};

// ===================== QR ATTENDANCE =====================

export const verifyQRCode = async (qr_data: string): Promise<any> => {
  const response = await apiClient.post('/api/v1/attendance/qr/verify', { qr_data });
  return response.data.data;
};

export const markByQR = async (data: {
  qr_data: string;
  status: string;
  date: string;
}): Promise<Attendance> => {
  const response = await apiClient.post('/api/v1/attendance/qr/mark', data);
  return response.data.data;
};

// ===================== APPROVAL =====================

export const approveAttendance = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/attendance/${uuid}/approve`);
};

export const bulkApprove = async (attendance_ids: string[]): Promise<{ approved: number }> => {
  const response = await apiClient.post('/api/v1/attendance/approve/bulk', { attendance_ids });
  return response.data.data;
};

// ===================== CORRECTION =====================

export const requestCorrection = async (data: {
  attendance_id: string;
  new_status: string;
  reason: string;
}): Promise<AttendanceCorrection> => {
  const response = await apiClient.post('/api/v1/attendance/correction', data);
  return response.data.data;
};

export const reviewCorrection = async (uuid: string, data: {
  status: 'approved' | 'rejected';
  review_notes?: string;
}): Promise<void> => {
  await apiClient.put(`/api/v1/attendance/correction/${uuid}`, data);
};

export const getCorrectionRequests = async (params?: {
  status?: string;
  per_page?: number;
}): Promise<PaginatedResponse<AttendanceCorrection>> => {
  const response = await apiClient.get('/api/v1/attendance/corrections', { params });
  return response.data;
};

// ===================== REPORTS =====================

export const getAttendanceReport = async (data: {
  type: 'student' | 'teacher' | 'employee';
  start_date: string;
  end_date: string;
  session_id?: string;
  class_id?: string;
  section_id?: string;
}): Promise<AttendanceReport> => {
  const response = await apiClient.get('/api/v1/attendance/report', { params: data });
  return response.data.data;
};

export const getClassAttendanceSummary = async (data: {
  class_id: string;
  date: string;
}): Promise<AttendanceSummary> => {
  const response = await apiClient.get('/api/v1/attendance/report/class-summary', { params: data });
  return response.data.data;
};

// ===================== ANALYTICS =====================

export const getAttendanceAnalytics = async (data: {
  type: 'student' | 'teacher' | 'employee';
  start_date: string;
  end_date: string;
  group_by?: 'day' | 'week' | 'month' | 'class' | 'section';
}): Promise<AttendanceAnalytics> => {
  const response = await apiClient.get('/api/v1/attendance/analytics', { params: data });
  return response.data.data;
};

export const getDashboardStats = async (date?: string): Promise<DashboardStats> => {
  const response = await apiClient.get('/api/v1/attendance/dashboard', { params: { date } });
  return response.data.data;
};

// ===================== IMPORT/EXPORT =====================

export const importAttendance = async (file: File, type: 'student' | 'teacher' | 'employee'): Promise<{
  total: number;
  success: number;
  failed: number;
  errors: string[];
}> => {
  const formData = new FormData();
  formData.append('file', file);
  formData.append('type', type);

  const response = await apiClient.post('/api/v1/attendance/import', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data;
};

export const exportAttendance = async (data: {
  format: 'excel' | 'csv' | 'pdf';
  type: 'student' | 'teacher' | 'employee';
  start_date: string;
  end_date: string;
  session_id?: string;
  class_id?: string;
  section_id?: string;
}): Promise<string> => {
  const response = await apiClient.get('/api/v1/attendance/export', { params: data });
  return response.data.data.url;
};
