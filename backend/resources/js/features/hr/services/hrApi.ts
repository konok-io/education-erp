/**
 * HR, Payroll & Leave Management API
 * Phase 034 - Enterprise HRM System
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
  // New HRM Types
  JobCircular,
  JobApplication,
  Interview,
  OfferLetter,
  EmployeeOnboarding,
  EmployeeTransfer,
  ServiceBookEntry,
  ServiceBookTimeline,
  TrainingType,
  TrainingRecord,
  AwardType,
  EmployeeAward,
  ConfirmationRecord,
  ExperienceCertificate,
  NocCertificate,
  HRDashboardStats,
} from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== RECRUITMENT API =====================

export const getJobCirculars = async (params?: {
  status?: string;
  department_id?: string;
  search?: string;
  per_page?: number;
}): Promise<PaginatedResponse<JobCircular>> => {
  const response = await apiClient.get('/api/v1/hr/recruitment/circulars', { params });
  return response.data;
};

export const createJobCircular = async (data: Partial<JobCircular>): Promise<JobCircular> => {
  const response = await apiClient.post('/api/v1/hr/recruitment/circulars', data);
  return response.data.data;
};

export const publishJobCircular = async (uuid: string): Promise<JobCircular> => {
  const response = await apiClient.post(`/api/v1/hr/recruitment/circulars/${uuid}/publish`);
  return response.data.data;
};

export const closeJobCircular = async (uuid: string): Promise<JobCircular> => {
  const response = await apiClient.post(`/api/v1/hr/recruitment/circulars/${uuid}/close`);
  return response.data.data;
};

export const getJobApplications = async (params?: {
  job_circular_id?: string;
  status?: string;
  search?: string;
  per_page?: number;
}): Promise<PaginatedResponse<JobApplication>> => {
  const response = await apiClient.get('/api/v1/hr/recruitment/applications', { params });
  return response.data;
};

export const createJobApplication = async (data: Partial<JobApplication>): Promise<JobApplication> => {
  const response = await apiClient.post('/api/v1/hr/recruitment/applications', data);
  return response.data.data;
};

export const updateApplicationStatus = async (
  uuid: string,
  data: { status: string; reason?: string }
): Promise<JobApplication> => {
  const response = await apiClient.post(`/api/v1/hr/recruitment/applications/${uuid}/status`, data);
  return response.data.data;
};

export const getInterviews = async (params?: {
  job_circular_id?: string;
  decision?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<Interview>> => {
  const response = await apiClient.get('/api/v1/hr/recruitment/interviews', { params });
  return response.data;
};

export const scheduleInterview = async (data: Partial<Interview>): Promise<Interview> => {
  const response = await apiClient.post('/api/v1/hr/recruitment/interviews', data);
  return response.data.data;
};

export const evaluateCandidate = async (
  uuid: string,
  data: Partial<Interview>
): Promise<Interview> => {
  const response = await apiClient.post(`/api/v1/hr/recruitment/interviews/${uuid}/evaluate`, data);
  return response.data.data;
};

export const getOfferLetters = async (params?: {
  status?: string;
  per_page?: number;
}): Promise<PaginatedResponse<OfferLetter>> => {
  const response = await apiClient.get('/api/v1/hr/recruitment/offers', { params });
  return response.data;
};

export const createOfferLetter = async (data: Partial<OfferLetter>): Promise<OfferLetter> => {
  const response = await apiClient.post('/api/v1/hr/recruitment/offers', data);
  return response.data.data;
};

export const sendOfferLetter = async (uuid: string): Promise<OfferLetter> => {
  const response = await apiClient.post(`/api/v1/hr/recruitment/offers/${uuid}/send`);
  return response.data.data;
};

export const acceptOfferLetter = async (uuid: string): Promise<OfferLetter> => {
  const response = await apiClient.post(`/api/v1/hr/recruitment/offers/${uuid}/accept`);
  return response.data.data;
};

export const declineOfferLetter = async (uuid: string, reason?: string): Promise<OfferLetter> => {
  const response = await apiClient.post(`/api/v1/hr/recruitment/offers/${uuid}/decline`, { reason });
  return response.data.data;
};

export const markJoined = async (uuid: string): Promise<OfferLetter> => {
  const response = await apiClient.post(`/api/v1/hr/recruitment/offers/${uuid}/joined`);
  return response.data.data;
};

export const getRecruitmentStats = async (): Promise<any> => {
  const response = await apiClient.get('/api/v1/hr/recruitment/stats');
  return response.data.data;
};

// ===================== ONBOARDING API =====================

export const getOnboardingChecklists = async (category?: string): Promise<any[]> => {
  const response = await apiClient.get('/api/v1/hr/onboarding/checklists', {
    params: { category },
  });
  return response.data.data;
};

export const createOnboardingChecklist = async (data: any): Promise<any> => {
  const response = await apiClient.post('/api/v1/hr/onboarding/checklists', data);
  return response.data.data;
};

export const getOnboardings = async (params?: {
  status?: string;
  employee_id?: string;
  per_page?: number;
}): Promise<PaginatedResponse<EmployeeOnboarding>> => {
  const response = await apiClient.get('/api/v1/hr/onboarding', { params });
  return response.data;
};

export const startOnboarding = async (data: {
  employee_id: string;
  start_date: string;
  assigned_to?: string;
}): Promise<EmployeeOnboarding> => {
  const response = await apiClient.post('/api/v1/hr/onboarding', data);
  return response.data.data;
};

export const completeOnboardingChecklist = async (
  uuid: string,
  data: { checklist_id: string; remarks?: string }
): Promise<any> => {
  const response = await apiClient.post(`/api/v1/hr/onboarding/${uuid}/checklist`, data);
  return response.data.data;
};

export const getOnboardingProgress = async (uuid: string): Promise<any> => {
  const response = await apiClient.get(`/api/v1/hr/onboarding/${uuid}/progress`);
  return response.data.data;
};

export const getOnboardingStats = async (): Promise<any> => {
  const response = await apiClient.get('/api/v1/hr/onboarding/stats');
  return response.data.data;
};

// ===================== TRANSFER API =====================

export const getTransfers = async (params?: {
  status?: string;
  employee_id?: string;
  from_department_id?: string;
  to_department_id?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<EmployeeTransfer>> => {
  const response = await apiClient.get('/api/v1/hr/transfers', { params });
  return response.data;
};

export const createTransfer = async (data: Partial<EmployeeTransfer>): Promise<EmployeeTransfer> => {
  const response = await apiClient.post('/api/v1/hr/transfers', data);
  return response.data.data;
};

export const recommendTransfer = async (uuid: string): Promise<EmployeeTransfer> => {
  const response = await apiClient.post(`/api/v1/hr/transfers/${uuid}/recommend`);
  return response.data.data;
};

export const approveTransfer = async (uuid: string): Promise<EmployeeTransfer> => {
  const response = await apiClient.post(`/api/v1/hr/transfers/${uuid}/approve`);
  return response.data.data;
};

export const cancelTransfer = async (uuid: string): Promise<EmployeeTransfer> => {
  const response = await apiClient.post(`/api/v1/hr/transfers/${uuid}/cancel`);
  return response.data.data;
};

export const getTransferStats = async (): Promise<any> => {
  const response = await apiClient.get('/api/v1/hr/transfers/stats');
  return response.data.data;
};

// ===================== SERVICE BOOK API =====================

export const getServiceBooks = async (params?: {
  employee_id?: string;
  event_type?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<ServiceBookEntry>> => {
  const response = await apiClient.get('/api/v1/hr/service-book', { params });
  return response.data;
};

export const getEmployeeServiceBook = async (employeeId: string): Promise<ServiceBookEntry[]> => {
  const response = await apiClient.get(`/api/v1/hr/service-book/employee/${employeeId}`);
  return response.data.data;
};

export const getServiceBookTimeline = async (employeeId: string): Promise<ServiceBookTimeline[]> => {
  const response = await apiClient.get(`/api/v1/hr/service-book/employee/${employeeId}/timeline`);
  return response.data.data;
};

export const getEmployeeTenure = async (employeeId: string): Promise<any> => {
  const response = await apiClient.get(`/api/v1/hr/service-book/employee/${employeeId}/tenure`);
  return response.data.data;
};

export const createServiceBookEntry = async (data: Partial<ServiceBookEntry>): Promise<ServiceBookEntry> => {
  const response = await apiClient.post('/api/v1/hr/service-book', data);
  return response.data.data;
};

// ===================== TRAINING API =====================

export const getTrainingTypes = async (): Promise<TrainingType[]> => {
  const response = await apiClient.get('/api/v1/hr/training/types');
  return response.data.data;
};

export const createTrainingType = async (data: Partial<TrainingType>): Promise<TrainingType> => {
  const response = await apiClient.post('/api/v1/hr/training/types', data);
  return response.data.data;
};

export const getTrainingRecords = async (params?: {
  employee_id?: string;
  training_type_id?: string;
  result?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<TrainingRecord>> => {
  const response = await apiClient.get('/api/v1/hr/training', { params });
  return response.data;
};

export const createTrainingRecord = async (data: Partial<TrainingRecord>): Promise<TrainingRecord> => {
  const response = await apiClient.post('/api/v1/hr/training', data);
  return response.data.data;
};

export const updateTrainingResult = async (
  uuid: string,
  data: { result: string; feedback?: string; score?: number }
): Promise<TrainingRecord> => {
  const response = await apiClient.post(`/api/v1/hr/training/${uuid}/result`, data);
  return response.data.data;
};

export const getEmployeeTrainingHistory = async (employeeId: string): Promise<TrainingRecord[]> => {
  const response = await apiClient.get(`/api/v1/hr/training/employee/${employeeId}`);
  return response.data.data;
};

export const getTrainingStats = async (): Promise<any> => {
  const response = await apiClient.get('/api/v1/hr/training/stats');
  return response.data.data;
};

// ===================== AWARDS API =====================

export const getAwardTypes = async (): Promise<AwardType[]> => {
  const response = await apiClient.get('/api/v1/hr/awards/types');
  return response.data.data;
};

export const createAwardType = async (data: Partial<AwardType>): Promise<AwardType> => {
  const response = await apiClient.post('/api/v1/hr/awards/types', data);
  return response.data.data;
};

export const getAwards = async (params?: {
  employee_id?: string;
  award_type_id?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<EmployeeAward>> => {
  const response = await apiClient.get('/api/v1/hr/awards', { params });
  return response.data;
};

export const createAward = async (data: Partial<EmployeeAward>): Promise<EmployeeAward> => {
  const response = await apiClient.post('/api/v1/hr/awards', data);
  return response.data.data;
};

export const getEmployeeAwards = async (employeeId: string): Promise<EmployeeAward[]> => {
  const response = await apiClient.get(`/api/v1/hr/awards/employee/${employeeId}`);
  return response.data.data;
};

export const getAwardStats = async (): Promise<any> => {
  const response = await apiClient.get('/api/v1/hr/awards/stats');
  return response.data.data;
};

// ===================== CONFIRMATION API =====================

export const getConfirmations = async (params?: {
  status?: string;
  employee_id?: string;
  pending?: boolean;
  per_page?: number;
}): Promise<PaginatedResponse<ConfirmationRecord>> => {
  const response = await apiClient.get('/api/v1/hr/confirmation', { params });
  return response.data;
};

export const createConfirmation = async (data: Partial<ConfirmationRecord>): Promise<ConfirmationRecord> => {
  const response = await apiClient.post('/api/v1/hr/confirmation', data);
  return response.data.data;
};

export const recommendConfirmation = async (
  uuid: string,
  data: { recommendation: string; remarks?: string }
): Promise<ConfirmationRecord> => {
  const response = await apiClient.post(`/api/v1/hr/confirmation/${uuid}/recommend`, data);
  return response.data.data;
};

export const approveConfirmation = async (uuid: string): Promise<ConfirmationRecord> => {
  const response = await apiClient.post(`/api/v1/hr/confirmation/${uuid}/approve`);
  return response.data.data;
};

export const getConfirmationStats = async (): Promise<any> => {
  const response = await apiClient.get('/api/v1/hr/confirmation/stats');
  return response.data.data;
};

// ===================== CERTIFICATE API =====================

export const getExperienceCertificates = async (params?: {
  employee_id?: string;
  is_verified?: boolean;
  per_page?: number;
}): Promise<PaginatedResponse<ExperienceCertificate>> => {
  const response = await apiClient.get('/api/v1/hr/certificates/experience', { params });
  return response.data;
};

export const createExperienceCertificate = async (data: Partial<ExperienceCertificate>): Promise<ExperienceCertificate> => {
  const response = await apiClient.post('/api/v1/hr/certificates/experience', data);
  return response.data.data;
};

export const generateExperiencePdf = async (uuid: string): Promise<string> => {
  const response = await apiClient.get(`/api/v1/hr/certificates/experience/${uuid}/pdf`);
  return response.data.data.path;
};

export const verifyExperienceCertificate = async (code: string): Promise<any> => {
  const response = await apiClient.get(`/api/v1/hr/certificates/experience/verify/${code}`);
  return response.data.data;
};

export const getNocCertificates = async (params?: {
  employee_id?: string;
  noc_type?: string;
  is_verified?: boolean;
  per_page?: number;
}): Promise<PaginatedResponse<NocCertificate>> => {
  const response = await apiClient.get('/api/v1/hr/certificates/noc', { params });
  return response.data;
};

export const createNocCertificate = async (data: Partial<NocCertificate>): Promise<NocCertificate> => {
  const response = await apiClient.post('/api/v1/hr/certificates/noc', data);
  return response.data.data;
};

export const generateNocPdf = async (uuid: string): Promise<string> => {
  const response = await apiClient.get(`/api/v1/hr/certificates/noc/${uuid}/pdf`);
  return response.data.data.path;
};

export const verifyNocCertificate = async (code: string): Promise<any> => {
  const response = await apiClient.get(`/api/v1/hr/certificates/noc/verify/${code}`);
  return response.data.data;
};

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
