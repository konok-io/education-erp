/**
 * Admission Management API
 */

import { apiClient } from '@/lib/api-client';
import type { 
  AdmissionCampaign, 
  AdmissionApplication, 
  AdmissionDocument, 
  AdmissionPayment,
  ApplicantDashboard,
  AdmissionDashboardStats,
  MeritListItem,
  AdmissionReport
} from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== CAMPAIGNS =====================

export const getCampaigns = async (params?: {
  status?: string;
  academic_level_id?: string;
  per_page?: number;
}): Promise<PaginatedResponse<AdmissionCampaign>> => {
  const response = await apiClient.get('/api/v1/admissions/campaigns', { params });
  return response.data;
};

export const createCampaign = async (data: Partial<AdmissionCampaign>): Promise<AdmissionCampaign> => {
  const response = await apiClient.post('/api/v1/admissions/campaigns', data);
  return response.data.data;
};

export const updateCampaign = async (uuid: string, data: Partial<AdmissionCampaign>): Promise<AdmissionCampaign> => {
  const response = await apiClient.put(`/api/v1/admissions/campaigns/${uuid}`, data);
  return response.data.data;
};

export const toggleCampaign = async (uuid: string): Promise<AdmissionCampaign> => {
  const response = await apiClient.post(`/api/v1/admissions/campaigns/${uuid}/toggle`);
  return response.data.data;
};

// ===================== APPLICATIONS =====================

export const getApplications = async (params?: {
  campaign_id?: string;
  status?: string;
  quota?: string;
  payment_status?: string;
  search?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<AdmissionApplication>> => {
  const response = await apiClient.get('/api/v1/admissions', { params });
  return response.data;
};

export const getApplication = async (uuid: string): Promise<AdmissionApplication> => {
  const response = await apiClient.get(`/api/v1/admissions/${uuid}`);
  return response.data.data;
};

export const createApplication = async (data: Partial<AdmissionApplication>): Promise<AdmissionApplication> => {
  const response = await apiClient.post('/api/v1/admissions', data);
  return response.data.data;
};

export const updateApplication = async (uuid: string, data: Partial<AdmissionApplication>): Promise<AdmissionApplication> => {
  const response = await apiClient.put(`/api/v1/admissions/${uuid}`, data);
  return response.data.data;
};

export const submitApplication = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/admissions/${uuid}/submit`);
};

// ===================== DOCUMENTS =====================

export const uploadDocument = async (data: {
  application_id: string;
  document_type: string;
  file: File;
}): Promise<AdmissionDocument> => {
  const formData = new FormData();
  formData.append('application_id', data.application_id);
  formData.append('document_type', data.document_type);
  formData.append('file', data.file);

  const response = await apiClient.post('/api/v1/admissions/documents', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data;
};

export const verifyDocument = async (uuid: string, data: {
  is_verified: boolean;
  rejection_reason?: string;
}): Promise<void> => {
  await apiClient.put(`/api/v1/admissions/documents/${uuid}/verify`, data);
};

// ===================== PAYMENTS =====================

export const initiatePayment = async (data: {
  application_id: string;
  amount: number;
  payment_method: string;
  transaction_id?: string;
}): Promise<AdmissionPayment> => {
  const response = await apiClient.post('/api/v1/admissions/payment', data);
  return response.data.data;
};

export const verifyPayment = async (uuid: string): Promise<void> => {
  await apiClient.put(`/api/v1/admissions/payment/${uuid}/verify`);
};

// ===================== MERIT & APPROVAL =====================

export const generateMeritList = async (campaignId: string): Promise<MeritListItem[]> => {
  const response = await apiClient.post('/api/v1/admissions/merit', { campaign_id: campaignId });
  return response.data.data;
};

export const updateMeritPosition = async (uuid: string, meritPosition: number): Promise<AdmissionApplication> => {
  const response = await apiClient.put(`/api/v1/admissions/${uuid}/merit`, { merit_position: meritPosition });
  return response.data.data;
};

export const approveApplication = async (uuid: string): Promise<{
  student: { id: string; student_no: string };
  user: { id: string; email: string };
}> => {
  const response = await apiClient.post(`/api/v1/admissions/${uuid}/approve`);
  return response.data.data;
};

export const rejectApplication = async (uuid: string, reason: string): Promise<void> => {
  await apiClient.post(`/api/v1/admissions/${uuid}/reject`, { reason });
};

// ===================== INTERVIEW =====================

export const scheduleInterview = async (uuid: string, data: {
  interview_date: string;
  interview_time: string;
  interview_venue: string;
}): Promise<AdmissionApplication> => {
  const response = await apiClient.post(`/api/v1/admissions/${uuid}/interview`, data);
  return response.data.data;
};

// ===================== DASHBOARD =====================

export const getDashboard = async (campaignId?: string): Promise<AdmissionDashboardStats> => {
  const response = await apiClient.get('/api/v1/admissions/dashboard/stats', { 
    params: campaignId ? { campaign_id: campaignId } : {} 
  });
  return response.data.data;
};

export const getApplicantDashboard = async (applicationNo: string): Promise<ApplicantDashboard> => {
  const response = await apiClient.get(`/api/v1/admissions/dashboard/applicant/${applicationNo}`);
  return response.data.data;
};

// ===================== REPORTS =====================

export const getAdmissionReport = async (data: {
  campaign_id: string;
  status?: string;
  quota?: string;
}): Promise<AdmissionReport> => {
  const response = await apiClient.get('/api/v1/admissions/report', { params: data });
  return response.data.data;
};

// ===================== EXPORT =====================

export const exportApplications = async (data: {
  campaign_id: string;
  format: 'excel' | 'csv' | 'pdf';
  status?: string;
  quota?: string;
}): Promise<string> => {
  const response = await apiClient.get('/api/v1/admissions/export', { params: data });
  return response.data.data.url;
};
