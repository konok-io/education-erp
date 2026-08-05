/**
 * HR, Payroll & Leave Management API
 */

import { apiClient } from '@/lib/api-client';
import type {
  SalaryGrade,
  Payroll,
  Payslip,
  LeaveType,
  Leave,
  LeaveBalance,
  Holiday,
  Loan,
  LoanBalance,
  OvertimeRecord,
  HRDashboard,
  PayrollReport,
  LeaveReport,
} from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== SALARY GRADES =====================

export const getSalaryGrades = async (): Promise<SalaryGrade[]> => {
  const response = await apiClient.get('/api/v1/hr/salary-grades');
  return response.data.data;
};

export const createSalaryGrade = async (data: Partial<SalaryGrade>): Promise<SalaryGrade> => {
  const response = await apiClient.post('/api/v1/hr/salary-grades', data);
  return response.data.data;
};

// ===================== PAYROLL =====================

export const getPayrolls = async (params?: {
  employee_id?: string;
  month?: number;
  year?: number;
  status?: string;
  per_page?: number;
}): Promise<PaginatedResponse<Payroll>> => {
  const response = await apiClient.get('/api/v1/hr/payrolls', { params });
  return response.data;
};

export const processPayroll = async (data: {
  employee_id: string;
  month: number;
  year: number;
}): Promise<Payroll> => {
  const response = await apiClient.post('/api/v1/hr/payrolls', data);
  return response.data.data;
};

export const processBulkPayroll = async (data: {
  department_id?: string;
  month: number;
  year: number;
}): Promise<{ total: number; processed: number; errors: any[] }> => {
  const response = await apiClient.post('/api/v1/hr/payrolls/bulk', data);
  return response.data.data;
};

export const approvePayroll = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/hr/payrolls/${uuid}/approve`);
};

export const payPayroll = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/hr/payrolls/${uuid}/pay`);
};

export const getPayslip = async (uuid: string): Promise<Payslip> => {
  const response = await apiClient.get(`/api/v1/hr/payrolls/${uuid}/payslip`);
  return response.data.data;
};

// ===================== LEAVE TYPES =====================

export const getLeaveTypes = async (): Promise<LeaveType[]> => {
  const response = await apiClient.get('/api/v1/hr/leave-types');
  return response.data.data;
};

export const createLeaveType = async (data: Partial<LeaveType>): Promise<LeaveType> => {
  const response = await apiClient.post('/api/v1/hr/leave-types', data);
  return response.data.data;
};

// ===================== LEAVES =====================

export const getLeaves = async (params?: {
  employee_id?: string;
  leave_type_id?: string;
  status?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<Leave>> => {
  const response = await apiClient.get('/api/v1/hr/leaves', { params });
  return response.data;
};

export const applyLeave = async (data: {
  employee_id: string;
  leave_type_id: string;
  start_date: string;
  end_date: string;
  reason: string;
}): Promise<Leave> => {
  const response = await apiClient.post('/api/v1/hr/leaves', data);
  return response.data.data;
};

export const approveLeave = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/hr/leaves/${uuid}/approve`);
};

export const rejectLeave = async (uuid: string, reason: string): Promise<void> => {
  await apiClient.post(`/api/v1/hr/leaves/${uuid}/reject`, { reason });
};

export const getLeaveBalance = async (employeeId: string): Promise<LeaveBalance[]> => {
  const response = await apiClient.get(`/api/v1/hr/leaves/balance/${employeeId}`);
  return response.data.data;
};

// ===================== HOLIDAYS =====================

export const getHolidays = async (year?: number): Promise<Holiday[]> => {
  const response = await apiClient.get('/api/v1/hr/holidays', { params: { year } });
  return response.data.data;
};

export const createHoliday = async (data: Partial<Holiday>): Promise<Holiday> => {
  const response = await apiClient.post('/api/v1/hr/holidays', data);
  return response.data.data;
};

// ===================== LOANS =====================

export const getLoans = async (params?: {
  employee_id?: string;
  status?: string;
  loan_type?: string;
  per_page?: number;
}): Promise<PaginatedResponse<Loan>> => {
  const response = await apiClient.get('/api/v1/hr/loans', { params });
  return response.data;
};

export const createLoan = async (data: {
  employee_id: string;
  principal_amount: number;
  loan_type: string;
  interest_rate?: number;
  installment_count?: number;
  purpose?: string;
}): Promise<Loan> => {
  const response = await apiClient.post('/api/v1/hr/loans', data);
  return response.data.data;
};

export const approveLoan = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/hr/loans/${uuid}/approve`);
};

export const getLoanBalance = async (employeeId: string): Promise<LoanBalance> => {
  const response = await apiClient.get(`/api/v1/hr/loans/balance/${employeeId}`);
  return response.data.data;
};

// ===================== OVERTIME =====================

export const getOvertimes = async (params?: {
  employee_id?: string;
  month?: number;
  year?: number;
  status?: string;
  per_page?: number;
}): Promise<PaginatedResponse<OvertimeRecord>> => {
  const response = await apiClient.get('/api/v1/hr/overtimes', { params });
  return response.data;
};

export const createOvertime = async (data: {
  employee_id: string;
  overtime_date: string;
  hours: number;
  overtime_type: string;
  reason?: string;
}): Promise<OvertimeRecord> => {
  const response = await apiClient.post('/api/v1/hr/overtimes', data);
  return response.data.data;
};

export const approveOvertime = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/hr/overtimes/${uuid}/approve`);
};

// ===================== REPORTS =====================

export const getPayrollReport = async (data: {
  month: number;
  year: number;
  department_id?: string;
}): Promise<PayrollReport> => {
  const response = await apiClient.get('/api/v1/hr/reports/payroll', { params: data });
  return response.data.data;
};

export const getLeaveReport = async (data: {
  year: number;
  department_id?: string;
}): Promise<LeaveReport> => {
  const response = await apiClient.get('/api/v1/hr/reports/leave', { params: data });
  return response.data.data;
};

// ===================== DASHBOARD =====================

export const getHRDashboard = async (): Promise<HRDashboard> => {
  const response = await apiClient.get('/api/v1/hr/dashboard');
  return response.data.data;
};

// ===================== EXPORT =====================

export const exportPayslips = async (data: {
  month: number;
  year: number;
  format: 'pdf' | 'excel';
}): Promise<string> => {
  const response = await apiClient.get('/api/v1/hr/export/payslips', { params: data });
  return response.data.data.url;
};
