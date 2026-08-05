/**
 * Teacher API
 */

import { apiClient } from '@/lib/api-client';
import type { Teacher, TeacherFilters, TeacherStatistics, TeacherQualification, TeacherExperience, SubjectAssignment, ClassAssignment, TeacherSalary, TeacherLeave } from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== TEACHER CRUD =====================

export const getTeachers = async (filters?: TeacherFilters): Promise<PaginatedResponse<Teacher>> => {
  const response = await apiClient.get('/api/v1/teachers', { params: filters });
  return response.data;
};

export const getTeacher = async (uuid: string): Promise<Teacher> => {
  const response = await apiClient.get(`/api/v1/teachers/${uuid}`);
  return response.data.data;
};

export const getTeacherByNumber = async (teacherNo: string): Promise<Teacher> => {
  const response = await apiClient.get(`/api/v1/teachers/by-number/${teacherNo}`);
  return response.data.data;
};

export const createTeacher = async (data: any): Promise<Teacher> => {
  const response = await apiClient.post('/api/v1/teachers', data);
  return response.data.data;
};

export const updateTeacher = async (uuid: string, data: any): Promise<Teacher> => {
  const response = await apiClient.put(`/api/v1/teachers/${uuid}`, data);
  return response.data.data;
};

export const deleteTeacher = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/teachers/${uuid}`);
};

export const searchTeachers = async (query: string, perPage = 20): Promise<PaginatedResponse<Teacher>> => {
  const response = await apiClient.get('/api/v1/teachers/search', {
    params: { q: query, per_page: perPage },
  });
  return response.data;
};

// ===================== PROFILE =====================

export const updateTeacherProfile = async (uuid: string, data: any): Promise<void> => {
  await apiClient.post(`/api/v1/teachers/${uuid}/profile`, data);
};

export const updateTeacherPhoto = async (uuid: string, photo: File): Promise<string> => {
  const formData = new FormData();
  formData.append('photo', photo);
  
  const response = await apiClient.post(`/api/v1/teachers/${uuid}/photo`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data.photo_url;
};

// ===================== QUALIFICATIONS =====================

export const addTeacherQualification = async (uuid: string, data: Partial<TeacherQualification>): Promise<TeacherQualification> => {
  const response = await apiClient.post(`/api/v1/teachers/${uuid}/qualifications`, data);
  return response.data.data;
};

export const updateTeacherQualification = async (uuid: string, qualUuid: string, data: Partial<TeacherQualification>): Promise<TeacherQualification> => {
  const response = await apiClient.put(`/api/v1/teachers/${uuid}/qualifications/${qualUuid}`, data);
  return response.data.data;
};

export const deleteTeacherQualification = async (uuid: string, qualUuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/teachers/${uuid}/qualifications/${qualUuid}`);
};

// ===================== EXPERIENCES =====================

export const addTeacherExperience = async (uuid: string, data: Partial<TeacherExperience>): Promise<TeacherExperience> => {
  const response = await apiClient.post(`/api/v1/teachers/${uuid}/experiences`, data);
  return response.data.data;
};

export const updateTeacherExperience = async (uuid: string, expUuid: string, data: Partial<TeacherExperience>): Promise<TeacherExperience> => {
  const response = await apiClient.put(`/api/v1/teachers/${uuid}/experiences/${expUuid}`, data);
  return response.data.data;
};

export const deleteTeacherExperience = async (uuid: string, expUuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/teachers/${uuid}/experiences/${expUuid}`);
};

// ===================== SUBJECT ASSIGNMENT =====================

export const getAssignedSubjects = async (uuid: string): Promise<SubjectAssignment[]> => {
  const response = await apiClient.get(`/api/v1/teachers/${uuid}/subjects`);
  return response.data.data;
};

export const assignSubjects = async (uuid: string, assignments: {
  subject_id: number;
  program_id: number;
  semester_id?: number;
  session_id: number;
  is_class_teacher?: boolean;
}[]): Promise<void> => {
  await apiClient.post(`/api/v1/teachers/${uuid}/subjects`, { assignments });
};

export const removeSubject = async (uuid: string, assignmentUuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/teachers/${uuid}/subjects/${assignmentUuid}`);
};

// ===================== CLASS ASSIGNMENT =====================

export const getAssignedClasses = async (uuid: string): Promise<ClassAssignment[]> => {
  const response = await apiClient.get(`/api/v1/teachers/${uuid}/classes`);
  return response.data.data;
};

export const assignClasses = async (uuid: string, assignments: {
  class_id: number;
  section_id?: number;
  session_id: number;
  is_primary_teacher?: boolean;
  weekly_classes?: number;
}[]): Promise<void> => {
  await apiClient.post(`/api/v1/teachers/${uuid}/classes`, { assignments });
};

export const removeClass = async (uuid: string, assignmentUuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/teachers/${uuid}/classes/${assignmentUuid}`);
};

// ===================== SALARY =====================

export const getTeacherSalary = async (uuid: string): Promise<TeacherSalary> => {
  const response = await apiClient.get(`/api/v1/teachers/${uuid}/salary`);
  return response.data.data;
};

export const updateTeacherSalary = async (uuid: string, data: Partial<TeacherSalary>): Promise<TeacherSalary> => {
  const response = await apiClient.post(`/api/v1/teachers/${uuid}/salary`, data);
  return response.data.data;
};

// ===================== LEAVE =====================

export const getLeaveHistory = async (uuid: string): Promise<TeacherLeave[]> => {
  const response = await apiClient.get(`/api/v1/teachers/${uuid}/leaves`);
  return response.data.data;
};

export const applyLeave = async (uuid: string, data: {
  leave_type: string;
  start_date: string;
  end_date: string;
  total_days?: number;
  reason: string;
}): Promise<TeacherLeave> => {
  const response = await apiClient.post(`/api/v1/teachers/${uuid}/leaves`, data);
  return response.data.data;
};

// ===================== STATUS =====================

export const updateTeacherStatus = async (uuid: string, status: string, remarks?: string): Promise<void> => {
  await apiClient.post(`/api/v1/teachers/${uuid}/status`, { status, remarks });
};

// ===================== QR CODE =====================

export const generateTeacherQRCode = async (uuid: string): Promise<string> => {
  const response = await apiClient.get(`/api/v1/teachers/${uuid}/qr-code`);
  return response.data.data.qr_code;
};

// ===================== IMPORT/EXPORT =====================

export const importTeachers = async (file: File): Promise<{
  total: number;
  success: number;
  failed: number;
  errors: string[];
}> => {
  const formData = new FormData();
  formData.append('file', file);
  
  const response = await apiClient.post('/api/v1/teachers/import', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data;
};

export const exportTeachers = async (format: 'excel' | 'csv' | 'pdf', filters?: TeacherFilters): Promise<string> => {
  const response = await apiClient.get('/api/v1/teachers/export', {
    params: { format, ...filters },
  });
  return response.data.data.url;
};

// ===================== STATISTICS =====================

export const getTeacherStatistics = async (): Promise<TeacherStatistics> => {
  const response = await apiClient.get('/api/v1/teachers/statistics');
  return response.data.data;
};

export const getActiveTeacherCount = async (): Promise<number> => {
  const response = await apiClient.get('/api/v1/teachers/active-count');
  return response.data.data.count;
};
