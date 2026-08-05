/**
 * Student API
 */

import { apiClient } from '@/lib/api-client';
import type { Student, StudentFilters, StudentStatistics, StudentDocument, StudentPromotion, StudentTransfer } from '../types';
import type { PaginatedResponse } from '@/types';

export interface CreateStudentData {
  // Academic
  campus_id?: number;
  session_id: number;
  academic_level_id: number;
  faculty_id?: number;
  department_id?: number;
  program_id: number;
  semester_id?: number;
  class_id?: number;
  section_id?: number;
  group_id?: number;
  
  // Profile
  first_name: string;
  last_name?: string;
  first_name_bn?: string;
  last_name_bn?: string;
  gender: 'male' | 'female' | 'other';
  date_of_birth?: string;
  blood_group?: string;
  religion?: string;
  nationality?: string;
  birth_certificate?: string;
  nid?: string;
  passport?: string;
  email: string;
  mobile?: string;
  present_address?: any;
  permanent_address?: any;
  password?: string;
  
  // Guardian
  guardian?: {
    guardian_type: string;
    name: string;
    mobile: string;
    [key: string]: any;
  };
  
  // Medical
  medical?: {
    height?: number;
    weight?: number;
    blood_group?: string;
    allergy?: boolean;
    [key: string]: any;
  };
}

// ===================== STUDENT CRUD =====================

export const getStudents = async (filters?: StudentFilters): Promise<PaginatedResponse<Student>> => {
  const response = await apiClient.get('/api/v1/students', { params: filters });
  return response.data;
};

export const getStudent = async (uuid: string): Promise<Student> => {
  const response = await apiClient.get(`/api/v1/students/${uuid}`);
  return response.data.data;
};

export const getStudentByNumber = async (studentNo: string): Promise<Student> => {
  const response = await apiClient.get(`/api/v1/students/by-number/${studentNo}`);
  return response.data.data;
};

export const createStudent = async (data: CreateStudentData): Promise<Student> => {
  const response = await apiClient.post('/api/v1/students', data);
  return response.data.data;
};

export const updateStudent = async (uuid: string, data: Partial<CreateStudentData>): Promise<Student> => {
  const response = await apiClient.put(`/api/v1/students/${uuid}`, data);
  return response.data.data;
};

export const deleteStudent = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/students/${uuid}`);
};

export const searchStudents = async (query: string, perPage = 20): Promise<PaginatedResponse<Student>> => {
  const response = await apiClient.get('/api/v1/students/search', {
    params: { q: query, per_page: perPage },
  });
  return response.data;
};

// ===================== PROFILE =====================

export const updateStudentProfile = async (uuid: string, data: any): Promise<void> => {
  await apiClient.post(`/api/v1/students/${uuid}/profile`, data);
};

export const updateStudentPhoto = async (uuid: string, photo: File): Promise<string> => {
  const formData = new FormData();
  formData.append('photo', photo);
  
  const response = await apiClient.post(`/api/v1/students/${uuid}/photo`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data.photo_url;
};

// ===================== GUARDIAN =====================

export const updateStudentGuardian = async (uuid: string, data: any): Promise<void> => {
  await apiClient.post(`/api/v1/students/${uuid}/guardian`, data);
};

// ===================== MEDICAL =====================

export const updateStudentMedical = async (uuid: string, data: any): Promise<void> => {
  await apiClient.post(`/api/v1/students/${uuid}/medical`, data);
};

// ===================== DOCUMENTS =====================

export const getStudentDocuments = async (uuid: string): Promise<StudentDocument[]> => {
  const response = await apiClient.get(`/api/v1/students/${uuid}/documents`);
  return response.data.data;
};

export const uploadStudentDocument = async (uuid: string, data: {
  document_type: string;
  title: string;
  file: File;
  issue_date?: string;
  expiry_date?: string;
}): Promise<StudentDocument> => {
  const formData = new FormData();
  formData.append('document_type', data.document_type);
  formData.append('title', data.title);
  formData.append('file', data.file);
  if (data.issue_date) formData.append('issue_date', data.issue_date);
  if (data.expiry_date) formData.append('expiry_date', data.expiry_date);
  
  const response = await apiClient.post(`/api/v1/students/${uuid}/documents`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data;
};

export const deleteStudentDocument = async (uuid: string, documentUuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/students/${uuid}/documents/${documentUuid}`);
};

// ===================== STATUS =====================

export const updateStudentStatus = async (uuid: string, status: string, remarks?: string): Promise<void> => {
  await apiClient.post(`/api/v1/students/${uuid}/status`, { status, remarks });
};

// ===================== PROMOTION =====================

export const promoteStudent = async (uuid: string, data: {
  to_session_id: number;
  to_class_id: number;
  to_semester_id?: number;
  to_section_id?: number;
  to_group_id?: number;
  status?: string;
  result?: any;
  remarks?: string;
}): Promise<StudentPromotion> => {
  const response = await apiClient.post(`/api/v1/students/${uuid}/promote`, data);
  return response.data.data;
};

export const getPromotionHistory = async (uuid: string): Promise<StudentPromotion[]> => {
  const response = await apiClient.get(`/api/v1/students/${uuid}/promotions`);
  return response.data.data;
};

// ===================== TRANSFER =====================

export const transferStudent = async (uuid: string, data: {
  transfer_type: string;
  to_campus_id?: number;
  to_department_id?: number;
  to_program_id?: number;
  to_class_id?: number;
  to_section_id?: number;
  to_group_id?: number;
  reason: string;
  remarks?: string;
}): Promise<StudentTransfer> => {
  const response = await apiClient.post(`/api/v1/students/${uuid}/transfer`, data);
  return response.data.data;
};

export const getTransferHistory = async (uuid: string): Promise<StudentTransfer[]> => {
  const response = await apiClient.get(`/api/v1/students/${uuid}/transfers`);
  return response.data.data;
};

// ===================== QR CODE =====================

export const generateStudentQRCode = async (uuid: string): Promise<string> => {
  const response = await apiClient.get(`/api/v1/students/${uuid}/qr-code`);
  return response.data.data.qr_code;
};

// ===================== IMPORT/EXPORT =====================

export const importStudents = async (file: File, sessionId: number): Promise<{
  total: number;
  success: number;
  failed: number;
  errors: string[];
}> => {
  const formData = new FormData();
  formData.append('file', file);
  formData.append('session_id', sessionId.toString());
  
  const response = await apiClient.post('/api/v1/students/import', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data;
};

export const exportStudents = async (format: 'excel' | 'csv' | 'pdf', filters?: StudentFilters): Promise<string> => {
  const response = await apiClient.get('/api/v1/students/export', {
    params: { format, ...filters },
  });
  return response.data.data.url;
};

// ===================== STATISTICS =====================

export const getStudentStatistics = async (filters?: Partial<StudentFilters>): Promise<StudentStatistics> => {
  const response = await apiClient.get('/api/v1/students/statistics', { params: filters });
  return response.data.data;
};

export const getActiveStudentCount = async (sessionId?: string): Promise<number> => {
  const response = await apiClient.get('/api/v1/students/active-count', {
    params: { session_id: sessionId },
  });
  return response.data.data.count;
};
