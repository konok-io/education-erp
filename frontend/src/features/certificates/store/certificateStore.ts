/**
 * Certificate Store - State Management
 */

import { create } from 'zustand';
import { certificateApi } from '../services/certificateApi';
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
} from '../types';

interface CertificateState {
  // Dashboard
  dashboard: CertificateDashboard | null;
  dashboardLoading: boolean;

  // Certificates
  certificates: Certificate[];
  certificatesPagination: { current_page: number; last_page: number; total: number } | null;
  certificatesLoading: boolean;
  selectedCertificate: Certificate | null;

  // Templates
  templates: CertificateTemplate[];
  templatesLoading: boolean;

  // Transcripts
  transcripts: Transcript[];
  transcriptsPagination: { current_page: number; last_page: number; total: number } | null;
  transcriptsLoading: boolean;

  // Marksheets
  marksheets: Marksheet[];
  marksheetsPagination: { current_page: number; last_page: number; total: number } | null;
  marksheetsLoading: boolean;

  // Signatures
  signatures: DigitalSignature[];
  signaturesLoading: boolean;

  // Seals
  seals: DigitalSeal[];
  sealsLoading: boolean;

  // Archive
  archive: CertificateArchive[];
  archiveLoading: boolean;

  // Duplicate Requests
  duplicateRequests: DuplicateCertificateRequest[];
  duplicateRequestsLoading: boolean;

  // Verifications
  verifications: CertificateVerification[];
  verificationsLoading: boolean;

  // Actions
  fetchDashboard: () => Promise<void>;
  fetchCertificates: (params?: Record<string, any>) => Promise<void>;
  fetchCertificate: (uuid: string) => Promise<void>;
  createCertificate: (data: Partial<Certificate>) => Promise<Certificate>;
  updateCertificate: (uuid: string, data: Partial<Certificate>) => Promise<Certificate>;
  deleteCertificate: (uuid: string) => Promise<void>;
  approveCertificate: (uuid: string) => Promise<void>;
  issueCertificate: (uuid: string) => Promise<void>;
  rejectCertificate: (uuid: string, reason?: string) => Promise<void>;
  verifyCertificate: (token: string) => Promise<Certificate | null>;

  fetchTemplates: (params?: Record<string, any>) => Promise<void>;
  createTemplate: (data: Partial<CertificateTemplate>) => Promise<CertificateTemplate>;
  updateTemplate: (uuid: string, data: Partial<CertificateTemplate>) => Promise<CertificateTemplate>;
  deleteTemplate: (uuid: string) => Promise<void>;

  fetchTranscripts: (params?: Record<string, any>) => Promise<void>;
  createTranscript: (data: Partial<Transcript>) => Promise<Transcript>;
  approveTranscript: (uuid: string) => Promise<void>;
  issueTranscript: (uuid: string) => Promise<void>;

  fetchMarksheets: (params?: Record<string, any>) => Promise<void>;
  createMarksheet: (data: Partial<Marksheet>) => Promise<Marksheet>;
  approveMarksheet: (uuid: string) => Promise<void>;
  issueMarksheet: (uuid: string) => Promise<void>;

  fetchSignatures: (params?: Record<string, any>) => Promise<void>;
  createSignature: (data: Partial<DigitalSignature>) => Promise<DigitalSignature>;
  updateSignature: (uuid: string, data: Partial<DigitalSignature>) => Promise<DigitalSignature>;
  deleteSignature: (uuid: string) => Promise<void>;

  fetchSeals: (params?: Record<string, any>) => Promise<void>;
  createSeal: (data: Partial<DigitalSeal>) => Promise<DigitalSeal>;
  updateSeal: (uuid: string, data: Partial<DigitalSeal>) => Promise<DigitalSeal>;
  deleteSeal: (uuid: string) => Promise<void>;

  fetchArchive: (params?: Record<string, any>) => Promise<void>;
  archiveDocument: (data: Partial<CertificateArchive>) => Promise<CertificateArchive>;

  fetchDuplicateRequests: (params?: Record<string, any>) => Promise<void>;
  createDuplicateRequest: (data: Partial<DuplicateCertificateRequest>) => Promise<DuplicateCertificateRequest>;
  approveDuplicateRequest: (uuid: string) => Promise<void>;
  rejectDuplicateRequest: (uuid: string) => Promise<void>;

  fetchVerifications: (params?: Record<string, any>) => Promise<void>;

  resetState: () => void;
}

const initialState = {
  dashboard: null,
  dashboardLoading: false,
  certificates: [],
  certificatesPagination: null,
  certificatesLoading: false,
  selectedCertificate: null,
  templates: [],
  templatesLoading: false,
  transcripts: [],
  transcriptsPagination: null,
  transcriptsLoading: false,
  marksheets: [],
  marksheetsPagination: null,
  marksheetsLoading: false,
  signatures: [],
  signaturesLoading: false,
  seals: [],
  sealsLoading: false,
  archive: [],
  archiveLoading: false,
  duplicateRequests: [],
  duplicateRequestsLoading: false,
  verifications: [],
  verificationsLoading: false,
};

export const useCertificateStore = create<CertificateState>((set, get) => ({
  ...initialState,

  // Dashboard
  fetchDashboard: async () => {
    set({ dashboardLoading: true });
    try {
      const dashboard = await certificateApi.getDashboard();
      set({ dashboard, dashboardLoading: false });
    } catch {
      set({ dashboardLoading: false });
    }
  },

  // Certificates
  fetchCertificates: async (params) => {
    set({ certificatesLoading: true });
    try {
      const response = await certificateApi.getCertificates(params);
      set({
        certificates: response.data,
        certificatesPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        certificatesLoading: false,
      });
    } catch {
      set({ certificatesLoading: false });
    }
  },

  fetchCertificate: async (uuid) => {
    set({ certificatesLoading: true });
    try {
      const certificate = await certificateApi.getCertificate(uuid);
      set({ selectedCertificate: certificate, certificatesLoading: false });
    } catch {
      set({ certificatesLoading: false });
    }
  },

  createCertificate: async (data) => {
    const certificate = await certificateApi.createCertificate(data);
    const certificates = [certificate, ...get().certificates];
    set({ certificates });
    return certificate;
  },

  updateCertificate: async (uuid, data) => {
    const certificate = await certificateApi.updateCertificate(uuid, data);
    const certificates = get().certificates.map((c) => (c.id === uuid ? certificate : c));
    set({ certificates, selectedCertificate: certificate });
    return certificate;
  },

  deleteCertificate: async (uuid) => {
    await certificateApi.deleteCertificate(uuid);
    const certificates = get().certificates.filter((c) => c.id !== uuid);
    set({ certificates });
  },

  approveCertificate: async (uuid) => {
    await certificateApi.approveCertificate(uuid);
    get().fetchCertificates();
  },

  issueCertificate: async (uuid) => {
    await certificateApi.issueCertificate(uuid);
    get().fetchCertificates();
  },

  rejectCertificate: async (uuid, reason) => {
    await certificateApi.rejectCertificate(uuid, reason);
    get().fetchCertificates();
  },

  verifyCertificate: async (token) => {
    return await certificateApi.verifyCertificate(token);
  },

  // Templates
  fetchTemplates: async (params) => {
    set({ templatesLoading: true });
    try {
      const response = await certificateApi.getTemplates(params);
      set({ templates: response.data, templatesLoading: false });
    } catch {
      set({ templatesLoading: false });
    }
  },

  createTemplate: async (data) => {
    const template = await certificateApi.createTemplate(data);
    const templates = [...get().templates, template];
    set({ templates });
    return template;
  },

  updateTemplate: async (uuid, data) => {
    const template = await certificateApi.updateTemplate(uuid, data);
    const templates = get().templates.map((t) => (t.id === uuid ? template : t));
    set({ templates });
    return template;
  },

  deleteTemplate: async (uuid) => {
    await certificateApi.deleteTemplate(uuid);
    const templates = get().templates.filter((t) => t.id !== uuid);
    set({ templates });
  },

  // Transcripts
  fetchTranscripts: async (params) => {
    set({ transcriptsLoading: true });
    try {
      const response = await certificateApi.getTranscripts(params);
      set({
        transcripts: response.data,
        transcriptsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        transcriptsLoading: false,
      });
    } catch {
      set({ transcriptsLoading: false });
    }
  },

  createTranscript: async (data) => {
    const transcript = await certificateApi.createTranscript(data);
    const transcripts = [transcript, ...get().transcripts];
    set({ transcripts });
    return transcript;
  },

  approveTranscript: async (uuid) => {
    await certificateApi.approveTranscript(uuid);
    get().fetchTranscripts();
  },

  issueTranscript: async (uuid) => {
    await certificateApi.issueTranscript(uuid);
    get().fetchTranscripts();
  },

  // Marksheets
  fetchMarksheets: async (params) => {
    set({ marksheetsLoading: true });
    try {
      const response = await certificateApi.getMarksheets(params);
      set({
        marksheets: response.data,
        marksheetsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        marksheetsLoading: false,
      });
    } catch {
      set({ marksheetsLoading: false });
    }
  },

  createMarksheet: async (data) => {
    const marksheet = await certificateApi.createMarksheet(data);
    const marksheets = [marksheet, ...get().marksheets];
    set({ marksheets });
    return marksheet;
  },

  approveMarksheet: async (uuid) => {
    await certificateApi.approveMarksheet(uuid);
    get().fetchMarksheets();
  },

  issueMarksheet: async (uuid) => {
    await certificateApi.issueMarksheet(uuid);
    get().fetchMarksheets();
  },

  // Signatures
  fetchSignatures: async (params) => {
    set({ signaturesLoading: true });
    try {
      const response = await certificateApi.getSignatures(params);
      set({ signatures: response.data, signaturesLoading: false });
    } catch {
      set({ signaturesLoading: false });
    }
  },

  createSignature: async (data) => {
    const signature = await certificateApi.createSignature(data);
    const signatures = [...get().signatures, signature];
    set({ signatures });
    return signature;
  },

  updateSignature: async (uuid, data) => {
    const signature = await certificateApi.updateSignature(uuid, data);
    const signatures = get().signatures.map((s) => (s.id === uuid ? signature : s));
    set({ signatures });
    return signature;
  },

  deleteSignature: async (uuid) => {
    await certificateApi.deleteSignature(uuid);
    const signatures = get().signatures.filter((s) => s.id !== uuid);
    set({ signatures });
  },

  // Seals
  fetchSeals: async (params) => {
    set({ sealsLoading: true });
    try {
      const response = await certificateApi.getSeals(params);
      set({ seals: response.data, sealsLoading: false });
    } catch {
      set({ sealsLoading: false });
    }
  },

  createSeal: async (data) => {
    const seal = await certificateApi.createSeal(data);
    const seals = [...get().seals, seal];
    set({ seals });
    return seal;
  },

  updateSeal: async (uuid, data) => {
    const seal = await certificateApi.updateSeal(uuid, data);
    const seals = get().seals.map((s) => (s.id === uuid ? seal : s));
    set({ seals });
    return seal;
  },

  deleteSeal: async (uuid) => {
    await certificateApi.deleteSeal(uuid);
    const seals = get().seals.filter((s) => s.id !== uuid);
    set({ seals });
  },

  // Archive
  fetchArchive: async (params) => {
    set({ archiveLoading: true });
    try {
      const response = await certificateApi.getArchive(params);
      set({ archive: response.data, archiveLoading: false });
    } catch {
      set({ archiveLoading: false });
    }
  },

  archiveDocument: async (data) => {
    const archive = await certificateApi.archiveDocument(data);
    const archiveDocs = [archive, ...get().archive];
    set({ archive: archiveDocs });
    return archive;
  },

  // Duplicate Requests
  fetchDuplicateRequests: async (params) => {
    set({ duplicateRequestsLoading: true });
    try {
      const response = await certificateApi.getDuplicateRequests(params);
      set({ duplicateRequests: response.data, duplicateRequestsLoading: false });
    } catch {
      set({ duplicateRequestsLoading: false });
    }
  },

  createDuplicateRequest: async (data) => {
    const request = await certificateApi.createDuplicateRequest(data);
    const requests = [request, ...get().duplicateRequests];
    set({ duplicateRequests: requests });
    return request;
  },

  approveDuplicateRequest: async (uuid) => {
    await certificateApi.approveDuplicateRequest(uuid);
    get().fetchDuplicateRequests();
  },

  rejectDuplicateRequest: async (uuid) => {
    await certificateApi.rejectDuplicateRequest(uuid);
    get().fetchDuplicateRequests();
  },

  // Verifications
  fetchVerifications: async (params) => {
    set({ verificationsLoading: true });
    try {
      const response = await certificateApi.getVerifications(params);
      set({ verifications: response.data, verificationsLoading: false });
    } catch {
      set({ verificationsLoading: false });
    }
  },

  // Reset
  resetState: () => set(initialState),
}));
