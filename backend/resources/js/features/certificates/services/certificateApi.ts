/**
 * Certificate API Service
 */

import { apiClient } from '@/lib/api-client';
import type {
  Certificate,
  CertificateTemplate,
  Transcript,
  Marksheet,
  DigitalSignature,
  DigitalSeal,
  CertificateArchive,
  CertificateVerification,
  DuplicateCertificateRequest,
  CertificateDashboard,
  PaginatedResponse,
} from '../types';

const BASE_URL = '/api/v1/certificates';

export const certificateApi = {
  // Dashboard
  getDashboard: async (): Promise<CertificateDashboard> => {
    const response = await apiClient.get(`${BASE_URL}/dashboard`);
    return response.data;
  },

  // Certificates
  getCertificates: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    certificate_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<Certificate>> => {
    const response = await apiClient.get(BASE_URL, { params });
    return response.data;
  },

  getCertificate: async (uuid: string): Promise<Certificate> => {
    const response = await apiClient.get(`${BASE_URL}/${uuid}`);
    return response.data;
  },

  createCertificate: async (data: Partial<Certificate>): Promise<Certificate> => {
    const response = await apiClient.post(BASE_URL, data);
    return response.data;
  },

  updateCertificate: async (uuid: string, data: Partial<Certificate>): Promise<Certificate> => {
    const response = await apiClient.put(`${BASE_URL}/${uuid}`, data);
    return response.data;
  },

  deleteCertificate: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/${uuid}`);
  },

  approveCertificate: async (uuid: string): Promise<Certificate> => {
    const response = await apiClient.post(`${BASE_URL}/${uuid}/approve`);
    return response.data;
  },

  issueCertificate: async (uuid: string): Promise<Certificate> => {
    const response = await apiClient.post(`${BASE_URL}/${uuid}/issue`);
    return response.data;
  },

  rejectCertificate: async (uuid: string, reason?: string): Promise<Certificate> => {
    const response = await apiClient.post(`${BASE_URL}/${uuid}/reject`, { reason });
    return response.data;
  },

  verifyCertificate: async (token: string): Promise<Certificate | null> => {
    try {
      const response = await apiClient.get(`${BASE_URL}/verify/${token}`);
      return response.data;
    } catch {
      return null;
    }
  },

  // Templates
  getTemplates: async (params?: {
    page?: number;
    per_page?: number;
    certificate_type?: string;
    status?: string;
  }): Promise<PaginatedResponse<CertificateTemplate>> => {
    const response = await apiClient.get(`${BASE_URL}/templates`, { params });
    return response.data;
  },

  getTemplate: async (uuid: string): Promise<CertificateTemplate> => {
    const response = await apiClient.get(`${BASE_URL}/templates/${uuid}`);
    return response.data;
  },

  createTemplate: async (data: Partial<CertificateTemplate>): Promise<CertificateTemplate> => {
    const response = await apiClient.post(`${BASE_URL}/templates`, data);
    return response.data;
  },

  updateTemplate: async (uuid: string, data: Partial<CertificateTemplate>): Promise<CertificateTemplate> => {
    const response = await apiClient.put(`${BASE_URL}/templates/${uuid}`, data);
    return response.data;
  },

  deleteTemplate: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/templates/${uuid}`);
  },

  // Transcripts
  getTranscripts: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
  }): Promise<PaginatedResponse<Transcript>> => {
    const response = await apiClient.get(`${BASE_URL}/transcripts`, { params });
    return response.data;
  },

  getTranscript: async (uuid: string): Promise<Transcript> => {
    const response = await apiClient.get(`${BASE_URL}/transcripts/${uuid}`);
    return response.data;
  },

  createTranscript: async (data: Partial<Transcript>): Promise<Transcript> => {
    const response = await apiClient.post(`${BASE_URL}/transcripts`, data);
    return response.data;
  },

  updateTranscript: async (uuid: string, data: Partial<Transcript>): Promise<Transcript> => {
    const response = await apiClient.put(`${BASE_URL}/transcripts/${uuid}`, data);
    return response.data;
  },

  approveTranscript: async (uuid: string): Promise<Transcript> => {
    const response = await apiClient.post(`${BASE_URL}/transcripts/${uuid}/approve`);
    return response.data;
  },

  issueTranscript: async (uuid: string): Promise<Transcript> => {
    const response = await apiClient.post(`${BASE_URL}/transcripts/${uuid}/issue`);
    return response.data;
  },

  verifyTranscript: async (token: string): Promise<Transcript | null> => {
    try {
      const response = await apiClient.get(`${BASE_URL}/transcripts/verify/${token}`);
      return response.data;
    } catch {
      return null;
    }
  },

  // Marksheets
  getMarksheets: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
  }): Promise<PaginatedResponse<Marksheet>> => {
    const response = await apiClient.get(`${BASE_URL}/marksheets`, { params });
    return response.data;
  },

  getMarksheet: async (uuid: string): Promise<Marksheet> => {
    const response = await apiClient.get(`${BASE_URL}/marksheets/${uuid}`);
    return response.data;
  },

  createMarksheet: async (data: Partial<Marksheet>): Promise<Marksheet> => {
    const response = await apiClient.post(`${BASE_URL}/marksheets`, data);
    return response.data;
  },

  updateMarksheet: async (uuid: string, data: Partial<Marksheet>): Promise<Marksheet> => {
    const response = await apiClient.put(`${BASE_URL}/marksheets/${uuid}`, data);
    return response.data;
  },

  approveMarksheet: async (uuid: string): Promise<Marksheet> => {
    const response = await apiClient.post(`${BASE_URL}/marksheets/${uuid}/approve`);
    return response.data;
  },

  issueMarksheet: async (uuid: string): Promise<Marksheet> => {
    const response = await apiClient.post(`${BASE_URL}/marksheets/${uuid}/issue`);
    return response.data;
  },

  // Signatures
  getSignatures: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<DigitalSignature>> => {
    const response = await apiClient.get(`${BASE_URL}/signatures`, { params });
    return response.data;
  },

  createSignature: async (data: Partial<DigitalSignature>): Promise<DigitalSignature> => {
    const response = await apiClient.post(`${BASE_URL}/signatures`, data);
    return response.data;
  },

  updateSignature: async (uuid: string, data: Partial<DigitalSignature>): Promise<DigitalSignature> => {
    const response = await apiClient.put(`${BASE_URL}/signatures/${uuid}`, data);
    return response.data;
  },

  deleteSignature: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/signatures/${uuid}`);
  },

  // Seals
  getSeals: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<DigitalSeal>> => {
    const response = await apiClient.get(`${BASE_URL}/seals`, { params });
    return response.data;
  },

  createSeal: async (data: Partial<DigitalSeal>): Promise<DigitalSeal> => {
    const response = await apiClient.post(`${BASE_URL}/seals`, data);
    return response.data;
  },

  updateSeal: async (uuid: string, data: Partial<DigitalSeal>): Promise<DigitalSeal> => {
    const response = await apiClient.put(`${BASE_URL}/seals/${uuid}`, data);
    return response.data;
  },

  deleteSeal: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/seals/${uuid}`);
  },

  // Archive
  getArchive: async (params?: {
    page?: number;
    per_page?: number;
    document_type?: string;
    student_id?: number;
  }): Promise<PaginatedResponse<CertificateArchive>> => {
    const response = await apiClient.get(`${BASE_URL}/archive`, { params });
    return response.data;
  },

  archiveDocument: async (data: Partial<CertificateArchive>): Promise<CertificateArchive> => {
    const response = await apiClient.post(`${BASE_URL}/archive`, data);
    return response.data;
  },

  // Duplicate Requests
  getDuplicateRequests: async (params?: {
    page?: number;
    per_page?: number;
    status?: string;
  }): Promise<PaginatedResponse<DuplicateCertificateRequest>> => {
    const response = await apiClient.get(`${BASE_URL}/duplicate-requests`, { params });
    return response.data;
  },

  createDuplicateRequest: async (data: Partial<DuplicateCertificateRequest>): Promise<DuplicateCertificateRequest> => {
    const response = await apiClient.post(`${BASE_URL}/duplicate-requests`, data);
    return response.data;
  },

  approveDuplicateRequest: async (uuid: string): Promise<DuplicateCertificateRequest> => {
    const response = await apiClient.post(`${BASE_URL}/duplicate-requests/${uuid}/approve`);
    return response.data;
  },

  rejectDuplicateRequest: async (uuid: string): Promise<DuplicateCertificateRequest> => {
    const response = await apiClient.post(`${BASE_URL}/duplicate-requests/${uuid}/reject`);
    return response.data;
  },

  // Verifications
  getVerifications: async (params?: {
    page?: number;
    per_page?: number;
    certificate_number?: string;
  }): Promise<PaginatedResponse<CertificateVerification>> => {
    const response = await apiClient.get(`${BASE_URL}/verifications`, { params });
    return response.data;
  },
};
