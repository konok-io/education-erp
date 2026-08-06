/**
 * Alumni API Service
 */

import { apiClient } from '@/lib/api-client';
import type {
  AlumniProfile,
  Employer,
  Job,
  JobApplication,
  Internship,
  Placement,
  AlumniEvent,
  EventRegistration,
  Mentorship,
  Donation,
  FundraisingCampaign,
  AlumniDashboard,
  PaginatedResponse,
} from '../types';

const BASE_URL = '/api/v1/alumni';

export const alumniApi = {
  // Dashboard
  getDashboard: async (): Promise<AlumniDashboard> => {
    const response = await apiClient.get(`${BASE_URL}/dashboard`);
    return response.data;
  },

  // Alumni Profiles
  getAlumni: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    department?: string;
    passing_year?: number;
    membership_type?: string;
    employment_status?: string;
  }): Promise<PaginatedResponse<AlumniProfile>> => {
    const response = await apiClient.get(BASE_URL, { params });
    return response.data;
  },

  getAlumniProfile: async (uuid: string): Promise<AlumniProfile> => {
    const response = await apiClient.get(`${BASE_URL}/${uuid}`);
    return response.data;
  },

  createAlumniProfile: async (data: Partial<AlumniProfile>): Promise<AlumniProfile> => {
    const response = await apiClient.post(BASE_URL, data);
    return response.data;
  },

  updateAlumniProfile: async (uuid: string, data: Partial<AlumniProfile>): Promise<AlumniProfile> => {
    const response = await apiClient.put(`${BASE_URL}/${uuid}`, data);
    return response.data;
  },

  deleteAlumniProfile: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/${uuid}`);
  },

  verifyAlumniProfile: async (uuid: string): Promise<AlumniProfile> => {
    const response = await apiClient.post(`${BASE_URL}/${uuid}/verify`);
    return response.data;
  },

  // Employers
  getEmployers: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    industry?: string;
  }): Promise<PaginatedResponse<Employer>> => {
    const response = await apiClient.get(`${BASE_URL}/employers`, { params });
    return response.data;
  },

  getEmployer: async (uuid: string): Promise<Employer> => {
    const response = await apiClient.get(`${BASE_URL}/employers/${uuid}`);
    return response.data;
  },

  createEmployer: async (data: Partial<Employer>): Promise<Employer> => {
    const response = await apiClient.post(`${BASE_URL}/employers`, data);
    return response.data;
  },

  updateEmployer: async (uuid: string, data: Partial<Employer>): Promise<Employer> => {
    const response = await apiClient.put(`${BASE_URL}/employers/${uuid}`, data);
    return response.data;
  },

  deleteEmployer: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/employers/${uuid}`);
  },

  verifyEmployer: async (uuid: string): Promise<Employer> => {
    const response = await apiClient.post(`${BASE_URL}/employers/${uuid}/verify`);
    return response.data;
  },

  // Jobs
  getJobs: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    job_type?: string;
    work_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<Job>> => {
    const response = await apiClient.get(`${BASE_URL}/jobs`, { params });
    return response.data;
  },

  getJob: async (uuid: string): Promise<Job> => {
    const response = await apiClient.get(`${BASE_URL}/jobs/${uuid}`);
    return response.data;
  },

  createJob: async (data: Partial<Job>): Promise<Job> => {
    const response = await apiClient.post(`${BASE_URL}/jobs`, data);
    return response.data;
  },

  updateJob: async (uuid: string, data: Partial<Job>): Promise<Job> => {
    const response = await apiClient.put(`${BASE_URL}/jobs/${uuid}`, data);
    return response.data;
  },

  deleteJob: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/jobs/${uuid}`);
  },

  publishJob: async (uuid: string): Promise<Job> => {
    const response = await apiClient.post(`${BASE_URL}/jobs/${uuid}/publish`);
    return response.data;
  },

  // Job Applications
  applyForJob: async (jobId: string, data: Partial<JobApplication>): Promise<JobApplication> => {
    const response = await apiClient.post(`${BASE_URL}/jobs/${jobId}/apply`, data);
    return response.data;
  },

  getJobApplications: async (jobId: string): Promise<PaginatedResponse<JobApplication>> => {
    const response = await apiClient.get(`${BASE_URL}/jobs/${jobId}/applications`);
    return response.data;
  },

  updateApplicationStatus: async (applicationId: string, status: string): Promise<JobApplication> => {
    const response = await apiClient.post(`${BASE_URL}/applications/${applicationId}/status`, { status });
    return response.data;
  },

  // Internships
  getInternships: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    internship_type?: string;
  }): Promise<PaginatedResponse<Internship>> => {
    const response = await apiClient.get(`${BASE_URL}/internships`, { params });
    return response.data;
  },

  getInternship: async (uuid: string): Promise<Internship> => {
    const response = await apiClient.get(`${BASE_URL}/internships/${uuid}`);
    return response.data;
  },

  createInternship: async (data: Partial<Internship>): Promise<Internship> => {
    const response = await apiClient.post(`${BASE_URL}/internships`, data);
    return response.data;
  },

  applyForInternship: async (internshipId: string, data: Partial<any>): Promise<any> => {
    const response = await apiClient.post(`${BASE_URL}/internships/${internshipId}/apply`, data);
    return response.data;
  },

  // Placements
  getPlacements: async (params?: {
    page?: number;
    per_page?: number;
    employer_id?: string;
    status?: string;
    year?: number;
  }): Promise<PaginatedResponse<Placement>> => {
    const response = await apiClient.get(`${BASE_URL}/placements`, { params });
    return response.data;
  },

  createPlacement: async (data: Partial<Placement>): Promise<Placement> => {
    const response = await apiClient.post(`${BASE_URL}/placements`, data);
    return response.data;
  },

  // Events
  getEvents: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    event_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<AlumniEvent>> => {
    const response = await apiClient.get(`${BASE_URL}/events`, { params });
    return response.data;
  },

  getEvent: async (uuid: string): Promise<AlumniEvent> => {
    const response = await apiClient.get(`${BASE_URL}/events/${uuid}`);
    return response.data;
  },

  createEvent: async (data: Partial<AlumniEvent>): Promise<AlumniEvent> => {
    const response = await apiClient.post(`${BASE_URL}/events`, data);
    return response.data;
  },

  updateEvent: async (uuid: string, data: Partial<AlumniEvent>): Promise<AlumniEvent> => {
    const response = await apiClient.put(`${BASE_URL}/events/${uuid}`, data);
    return response.data;
  },

  publishEvent: async (uuid: string): Promise<AlumniEvent> => {
    const response = await apiClient.post(`${BASE_URL}/events/${uuid}/publish`);
    return response.data;
  },

  registerForEvent: async (eventId: string, data: Partial<EventRegistration>): Promise<EventRegistration> => {
    const response = await apiClient.post(`${BASE_URL}/events/${eventId}/register`, data);
    return response.data;
  },

  // Mentorships
  getMentorships: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<Mentorship>> => {
    const response = await apiClient.get(`${BASE_URL}/mentorships`, { params });
    return response.data;
  },

  createMentorship: async (data: Partial<Mentorship>): Promise<Mentorship> => {
    const response = await apiClient.post(`${BASE_URL}/mentorships`, data);
    return response.data;
  },

  // Donations
  getDonations: async (params?: {
    page?: number;
    per_page?: number;
    campaign_id?: string;
    payment_status?: string;
  }): Promise<PaginatedResponse<Donation>> => {
    const response = await apiClient.get(`${BASE_URL}/donations`, { params });
    return response.data;
  },

  createDonation: async (data: Partial<Donation>): Promise<Donation> => {
    const response = await apiClient.post(`${BASE_URL}/donations`, data);
    return response.data;
  },

  // Campaigns
  getCampaigns: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<FundraisingCampaign>> => {
    const response = await apiClient.get(`${BASE_URL}/campaigns`, { params });
    return response.data;
  },

  getCampaign: async (uuid: string): Promise<FundraisingCampaign> => {
    const response = await apiClient.get(`${BASE_URL}/campaigns/${uuid}`);
    return response.data;
  },

  createCampaign: async (data: Partial<FundraisingCampaign>): Promise<FundraisingCampaign> => {
    const response = await apiClient.post(`${BASE_URL}/campaigns`, data);
    return response.data;
  },

  // Reports
  getReports: async (reportType: string): Promise<any> => {
    const response = await apiClient.get(`${BASE_URL}/reports/${reportType}`);
    return response.data;
  },
};
