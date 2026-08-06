/**
 * Employee API
 */

import { apiClient } from '@/lib/api-client';
import type { Employee, EmployeeFilters, EmployeeStatistics, EmployeeSalary, EmployeeLeave, Designation, EmploymentType, SalaryGrade, Shift } from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== EMPLOYEE CRUD =====================

export const getEmployees = async (filters?: EmployeeFilters): Promise<PaginatedResponse<Employee>> => {
  const response = await apiClient.get('/api/v1/employees', { params: filters });
  return response.data;
};

export const getEmployee = async (uuid: string): Promise<Employee> => {
  const response = await apiClient.get(`/api/v1/employees/${uuid}`);
  return response.data.data;
};

export const createEmployee = async (data: any): Promise<Employee> => {
  const response = await apiClient.post('/api/v1/employees', data);
  return response.data.data;
};

export const updateEmployee = async (uuid: string, data: any): Promise<Employee> => {
  const response = await apiClient.put(`/api/v1/employees/${uuid}`, data);
  return response.data.data;
};

export const deleteEmployee = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/employees/${uuid}`);
};

export const searchEmployees = async (query: string, perPage = 20): Promise<PaginatedResponse<Employee>> => {
  const response = await apiClient.get('/api/v1/employees/search', {
    params: { q: query, per_page: perPage },
  });
  return response.data;
};

// ===================== PROFILE =====================

export const updateEmployeeProfile = async (uuid: string, data: any): Promise<void> => {
  await apiClient.post(`/api/v1/employees/${uuid}/profile`, data);
};

export const updateEmployeePhoto = async (uuid: string, photo: File): Promise<string> => {
  const formData = new FormData();
  formData.append('photo', photo);
  
  const response = await apiClient.post(`/api/v1/employees/${uuid}/photo`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data.photo_url;
};

// ===================== STATUS =====================

export const updateEmployeeStatus = async (uuid: string, status: string, remarks?: string): Promise<void> => {
  await apiClient.post(`/api/v1/employees/${uuid}/status`, { status, remarks });
};

// ===================== SALARY =====================

export const updateEmployeeSalary = async (uuid: string, data: Partial<EmployeeSalary>): Promise<EmployeeSalary> => {
  const response = await apiClient.post(`/api/v1/employees/${uuid}/salary`, data);
  return response.data.data;
};

// ===================== LEAVE =====================

export const getLeaveHistory = async (uuid: string): Promise<EmployeeLeave[]> => {
  const response = await apiClient.get(`/api/v1/employees/${uuid}/leaves`);
  return response.data.data;
};

export const applyLeave = async (uuid: string, data: {
  leave_type: string;
  start_date: string;
  end_date: string;
  total_days?: number;
  reason: string;
}): Promise<EmployeeLeave> => {
  const response = await apiClient.post(`/api/v1/employees/${uuid}/leaves`, data);
  return response.data.data;
};

// ===================== QR CODE =====================

export const generateEmployeeQRCode = async (uuid: string): Promise<string> => {
  const response = await apiClient.get(`/api/v1/employees/${uuid}/qr-code`);
  return response.data.data.qr_code;
};

// ===================== IMPORT/EXPORT =====================

export const importEmployees = async (file: File): Promise<{
  total: number;
  success: number;
  failed: number;
  errors: string[];
}> => {
  const formData = new FormData();
  formData.append('file', file);
  
  const response = await apiClient.post('/api/v1/employees/import', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data;
};

export const exportEmployees = async (format: 'excel' | 'csv' | 'pdf', filters?: EmployeeFilters): Promise<string> => {
  const response = await apiClient.get('/api/v1/employees/export', {
    params: { format, ...filters },
  });
  return response.data.data.url;
};

// ===================== STATISTICS =====================

export const getEmployeeStatistics = async (): Promise<EmployeeStatistics> => {
  const response = await apiClient.get('/api/v1/employees/statistics');
  return response.data.data;
};

export const getActiveEmployeeCount = async (): Promise<number> => {
  const response = await apiClient.get('/api/v1/employees/active-count');
  return response.data.data.count;
};

// ===================== LOOKUPS =====================

export const getDepartments = async (): Promise<any[]> => {
  const response = await apiClient.get('/api/v1/employees/lookups/departments');
  return response.data.data;
};

export const getDesignations = async (): Promise<Designation[]> => {
  const response = await apiClient.get('/api/v1/employees/lookups/designations');
  return response.data.data;
};

export const getShifts = async (): Promise<Shift[]> => {
  const response = await apiClient.get('/api/v1/employees/lookups/shifts');
  return response.data.data;
};

export const getSalaryGrades = async (): Promise<SalaryGrade[]> => {
  const response = await apiClient.get('/api/v1/employees/lookups/salary-grades');
  return response.data.data;
};
