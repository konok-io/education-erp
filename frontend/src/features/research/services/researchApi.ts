/**
 * Research API Service
 */

import { apiClient } from '@/lib/api-client';
import type {
  ResearchProject,
  ResearchTeam,
  ResearchMilestone,
  ResearchGrant,
  FundingAgency,
  Publication,
  Patent,
  Thesis,
  Innovation,
  ResearchRepository,
  ResearchDashboard,
  PaginatedResponse,
} from '../types';

const BASE_URL = '/api/v1/research';

export const researchApi = {
  // Dashboard
  getDashboard: async (): Promise<ResearchDashboard> => {
    const response = await apiClient.get(`${BASE_URL}/dashboard`);
    return response.data;
  },

  // Projects
  getProjects: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    category?: string;
    research_type?: string;
    status?: string;
    department?: string;
  }): Promise<PaginatedResponse<ResearchProject>> => {
    const response = await apiClient.get(`${BASE_URL}/projects`, { params });
    return response.data;
  },

  getProject: async (uuid: string): Promise<ResearchProject> => {
    const response = await apiClient.get(`${BASE_URL}/projects/${uuid}`);
    return response.data;
  },

  createProject: async (data: Partial<ResearchProject>): Promise<ResearchProject> => {
    const response = await apiClient.post(`${BASE_URL}/projects`, data);
    return response.data;
  },

  updateProject: async (uuid: string, data: Partial<ResearchProject>): Promise<ResearchProject> => {
    const response = await apiClient.put(`${BASE_URL}/projects/${uuid}`, data);
    return response.data;
  },

  deleteProject: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/projects/${uuid}`);
  },

  approveProject: async (uuid: string): Promise<ResearchProject> => {
    const response = await apiClient.post(`${BASE_URL}/projects/${uuid}/approve`);
    return response.data;
  },

  completeProject: async (uuid: string): Promise<ResearchProject> => {
    const response = await apiClient.post(`${BASE_URL}/projects/${uuid}/complete`);
    return response.data;
  },

  // Teams
  addTeamMember: async (projectUuid: string, data: Partial<ResearchTeam>): Promise<ResearchTeam> => {
    const response = await apiClient.post(`${BASE_URL}/projects/${projectUuid}/teams`, data);
    return response.data;
  },

  removeTeamMember: async (teamUuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/teams/${teamUuid}`);
  },

  // Milestones
  createMilestone: async (projectUuid: string, data: Partial<ResearchMilestone>): Promise<ResearchMilestone> => {
    const response = await apiClient.post(`${BASE_URL}/projects/${projectUuid}/milestones`, data);
    return response.data;
  },

  updateMilestoneProgress: async (milestoneUuid: string, percentage: number): Promise<ResearchMilestone> => {
    const response = await apiClient.put(`${BASE_URL}/milestones/${milestoneUuid}/progress`, { percentage });
    return response.data;
  },

  // Grants
  getGrants: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<ResearchGrant>> => {
    const response = await apiClient.get(`${BASE_URL}/grants`, { params });
    return response.data;
  },

  createGrant: async (data: Partial<ResearchGrant>): Promise<ResearchGrant> => {
    const response = await apiClient.post(`${BASE_URL}/grants`, data);
    return response.data;
  },

  approveGrant: async (uuid: string): Promise<ResearchGrant> => {
    const response = await apiClient.post(`${BASE_URL}/grants/${uuid}/approve`);
    return response.data;
  },

  releaseGrantAmount: async (uuid: string, amount: number): Promise<ResearchGrant> => {
    const response = await apiClient.post(`${BASE_URL}/grants/${uuid}/release`, { amount });
    return response.data;
  },

  // Funding Agencies
  getFundingAgencies: async (params?: {
    agency_type?: string;
    is_active?: boolean;
  }): Promise<FundingAgency[]> => {
    const response = await apiClient.get(`${BASE_URL}/funding-agencies`, { params });
    return response.data;
  },

  createFundingAgency: async (data: Partial<FundingAgency>): Promise<FundingAgency> => {
    const response = await apiClient.post(`${BASE_URL}/funding-agencies`, data);
    return response.data;
  },

  // Publications
  getPublications: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    publication_type?: string;
    publication_year?: number;
  }): Promise<PaginatedResponse<Publication>> => {
    const response = await apiClient.get(`${BASE_URL}/publications`, { params });
    return response.data;
  },

  getPublication: async (uuid: string): Promise<Publication> => {
    const response = await apiClient.get(`${BASE_URL}/publications/${uuid}`);
    return response.data;
  },

  createPublication: async (data: Partial<Publication>): Promise<Publication> => {
    const response = await apiClient.post(`${BASE_URL}/publications`, data);
    return response.data;
  },

  updatePublication: async (uuid: string, data: Partial<Publication>): Promise<Publication> => {
    const response = await apiClient.put(`${BASE_URL}/publications/${uuid}`, data);
    return response.data;
  },

  deletePublication: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/publications/${uuid}`);
  },

  // Patents
  getPatents: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<Patent>> => {
    const response = await apiClient.get(`${BASE_URL}/patents`, { params });
    return response.data;
  },

  createPatent: async (data: Partial<Patent>): Promise<Patent> => {
    const response = await apiClient.post(`${BASE_URL}/patents`, data);
    return response.data;
  },

  // Theses
  getTheses: async (params?: {
    page?: number;
    per_page?: number;
    department?: string;
    status?: string;
  }): Promise<PaginatedResponse<Thesis>> => {
    const response = await apiClient.get(`${BASE_URL}/theses`, { params });
    return response.data;
  },

  createThesis: async (data: Partial<Thesis>): Promise<Thesis> => {
    const response = await apiClient.post(`${BASE_URL}/theses`, data);
    return response.data;
  },

  // Innovations
  getInnovations: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<Innovation>> => {
    const response = await apiClient.get(`${BASE_URL}/innovations`, { params });
    return response.data;
  },

  createInnovation: async (data: Partial<Innovation>): Promise<Innovation> => {
    const response = await apiClient.post(`${BASE_URL}/innovations`, data);
    return response.data;
  },

  // Repository
  getRepository: async (params?: {
    page?: number;
    per_page?: number;
    document_type?: string;
    access_type?: string;
  }): Promise<PaginatedResponse<ResearchRepository>> => {
    const response = await apiClient.get(`${BASE_URL}/repository`, { params });
    return response.data;
  },

  uploadToRepository: async (data: FormData): Promise<ResearchRepository> => {
    const response = await apiClient.post(`${BASE_URL}/repository`, data, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    return response.data;
  },

  // Reports
  getReports: async (reportType: string): Promise<any> => {
    const response = await apiClient.get(`${BASE_URL}/reports/${reportType}`);
    return response.data;
  },
};
