/**
 * CRM, Communication & Helpdesk Management API
 * Phase 035 - Enterprise CRM System
 */

import { apiClient } from '@/lib/api-client';
import type {
  CrmContact,
  CrmLead,
  CrmTicket,
  CrmCampaign,
  CrmCommunication,
  CrmAnnouncement,
  CrmFeedback,
  CrmSurvey,
  CrmDashboardStats,
  Priority,
  PipelineStage,
} from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== DASHBOARD =====================

export const getCrmDashboard = async (): Promise<CrmDashboardStats> => {
  const response = await apiClient.get('/api/v1/crm/dashboard');
  return response.data.data;
};

// ===================== CONTACTS =====================

export const getContacts = async (params?: {
  contact_type?: string;
  status?: string;
  search?: string;
  per_page?: number;
}): Promise<PaginatedResponse<CrmContact>> => {
  const response = await apiClient.get('/api/v1/crm/contacts', { params });
  return response.data;
};

export const createContact = async (data: Partial<CrmContact>): Promise<CrmContact> => {
  const response = await apiClient.post('/api/v1/crm/contacts', data);
  return response.data.data;
};

export const updateContact = async (uuid: string, data: Partial<CrmContact>): Promise<CrmContact> => {
  const response = await apiClient.put(`/api/v1/crm/contacts/${uuid}`, data);
  return response.data.data;
};

// ===================== LEADS =====================

export const getLeads = async (params?: {
  pipeline_stage?: PipelineStage;
  status?: string;
  priority?: Priority;
  lead_source?: string;
  assigned_counselor_id?: string;
  search?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<CrmLead>> => {
  const response = await apiClient.get('/api/v1/crm/leads', { params });
  return response.data;
};

export const createLead = async (data: Partial<CrmLead>): Promise<CrmLead> => {
  const response = await apiClient.post('/api/v1/crm/leads', data);
  return response.data.data;
};

export const updateLeadStage = async (uuid: string, stage: PipelineStage): Promise<CrmLead> => {
  const response = await apiClient.post(`/api/v1/crm/leads/${uuid}/stage`, { stage });
  return response.data.data;
};

export const assignLeadCounselor = async (uuid: string, counselorId: string): Promise<CrmLead> => {
  const response = await apiClient.post(`/api/v1/crm/leads/${uuid}/assign`, { counselor_id: counselorId });
  return response.data.data;
};

export const getLeadPipeline = async (): Promise<any[]> => {
  const response = await apiClient.get('/api/v1/crm/leads/pipeline');
  return response.data.data;
};

// ===================== TICKETS =====================

export const getTickets = async (params?: {
  category?: string;
  status?: string;
  priority?: Priority;
  assigned_to?: string;
  created_by?: string;
  search?: string;
  per_page?: number;
}): Promise<PaginatedResponse<CrmTicket>> => {
  const response = await apiClient.get('/api/v1/crm/tickets', { params });
  return response.data;
};

export const createTicket = async (data: Partial<CrmTicket>): Promise<CrmTicket> => {
  const response = await apiClient.post('/api/v1/crm/tickets', data);
  return response.data.data;
};

export const assignTicket = async (uuid: string, assigneeId: string): Promise<CrmTicket> => {
  const response = await apiClient.post(`/api/v1/crm/tickets/${uuid}/assign`, { assignee_id: assigneeId });
  return response.data.data;
};

export const updateTicketStatus = async (uuid: string, status: string): Promise<CrmTicket> => {
  const response = await apiClient.post(`/api/v1/crm/tickets/${uuid}/status`, { status });
  return response.data.data;
};

export const addTicketReply = async (
  uuid: string,
  data: { message: string; attachments?: string[]; is_internal?: boolean }
): Promise<any> => {
  const response = await apiClient.post(`/api/v1/crm/tickets/${uuid}/reply`, data);
  return response.data.data;
};

// ===================== CAMPAIGNS =====================

export const getCampaigns = async (params?: {
  campaign_type?: string;
  channel?: string;
  status?: string;
  date_from?: string;
  date_to?: string;
  per_page?: number;
}): Promise<PaginatedResponse<CrmCampaign>> => {
  const response = await apiClient.get('/api/v1/crm/campaigns', { params });
  return response.data;
};

export const createCampaign = async (data: Partial<CrmCampaign>): Promise<CrmCampaign> => {
  const response = await apiClient.post('/api/v1/crm/campaigns', data);
  return response.data.data;
};

export const updateCampaignStatus = async (uuid: string, status: string): Promise<CrmCampaign> => {
  const response = await apiClient.post(`/api/v1/crm/campaigns/${uuid}/status`, { status });
  return response.data.data;
};

// ===================== COMMUNICATIONS =====================

export const getCommunications = async (params?: {
  channel?: string;
  direction?: string;
  delivery_status?: string;
  contact_id?: string;
  per_page?: number;
}): Promise<PaginatedResponse<CrmCommunication>> => {
  const response = await apiClient.get('/api/v1/crm/communications', { params });
  return response.data;
};

export const sendCommunication = async (data: {
  contact_id?: string;
  channel: string;
  subject?: string;
  content: string;
  recipient_email?: string;
  recipient_mobile?: string;
}): Promise<CrmCommunication> => {
  const response = await apiClient.post('/api/v1/crm/communications', data);
  return response.data.data;
};

// ===================== ANNOUNCEMENTS =====================

export const getAnnouncements = async (params?: {
  announcement_type?: string;
  status?: string;
  per_page?: number;
}): Promise<PaginatedResponse<CrmAnnouncement>> => {
  const response = await apiClient.get('/api/v1/crm/announcements', { params });
  return response.data;
};

export const createAnnouncement = async (data: Partial<CrmAnnouncement>): Promise<CrmAnnouncement> => {
  const response = await apiClient.post('/api/v1/crm/announcements', data);
  return response.data.data;
};

export const publishAnnouncement = async (uuid: string): Promise<CrmAnnouncement> => {
  const response = await apiClient.post(`/api/v1/crm/announcements/${uuid}/publish`);
  return response.data.data;
};

// ===================== FEEDBACK =====================

export const getFeedbacks = async (params?: {
  feedback_type?: string;
  status?: string;
  rating?: number;
  per_page?: number;
}): Promise<PaginatedResponse<CrmFeedback>> => {
  const response = await apiClient.get('/api/v1/crm/feedback', { params });
  return response.data;
};

export const submitFeedback = async (data: Partial<CrmFeedback>): Promise<CrmFeedback> => {
  const response = await apiClient.post('/api/v1/crm/feedback', data);
  return response.data.data;
};

// ===================== SURVEYS =====================

export const getSurveys = async (params?: {
  survey_type?: string;
  status?: string;
  per_page?: number;
}): Promise<PaginatedResponse<CrmSurvey>> => {
  const response = await apiClient.get('/api/v1/crm/surveys', { params });
  return response.data;
};

export const createSurvey = async (data: Partial<CrmSurvey>): Promise<CrmSurvey> => {
  const response = await apiClient.post('/api/v1/crm/surveys', data);
  return response.data.data;
};

export const submitSurveyResponse = async (uuid: string, data: { responses: any; comments?: string }): Promise<any> => {
  const response = await apiClient.post(`/api/v1/crm/surveys/${uuid}/respond`, data);
  return response.data.data;
};
