/**
 * Research Store - State Management
 */

import { create } from 'zustand';
import { researchApi } from '../services/researchApi';
import type {
  ResearchProject,
  ResearchGrant,
  Publication,
  Patent,
  Thesis,
  Innovation,
  ResearchDashboard,
} from '../types';

interface ResearchState {
  // Dashboard
  dashboard: ResearchDashboard | null;
  dashboardLoading: boolean;

  // Projects
  projects: ResearchProject[];
  projectsPagination: { current_page: number; last_page: number; total: number } | null;
  projectsLoading: boolean;
  selectedProject: ResearchProject | null;

  // Grants
  grants: ResearchGrant[];
  grantsLoading: boolean;

  // Publications
  publications: Publication[];
  publicationsPagination: { current_page: number; last_page: number; total: number } | null;
  publicationsLoading: boolean;

  // Patents
  patents: Patent[];
  patentsLoading: boolean;

  // Theses
  theses: Thesis[];
  thesesLoading: boolean;

  // Innovations
  innovations: Innovation[];
  innovationsLoading: boolean;

  // Actions
  fetchDashboard: () => Promise<void>;
  fetchProjects: (params?: Record<string, any>) => Promise<void>;
  fetchProject: (uuid: string) => Promise<void>;
  createProject: (data: Partial<ResearchProject>) => Promise<ResearchProject>;
  updateProject: (uuid: string, data: Partial<ResearchProject>) => Promise<ResearchProject>;
  deleteProject: (uuid: string) => Promise<void>;
  approveProject: (uuid: string) => Promise<void>;
  completeProject: (uuid: string) => Promise<void>;

  fetchGrants: (params?: Record<string, any>) => Promise<void>;
  createGrant: (data: Partial<ResearchGrant>) => Promise<ResearchGrant>;
  approveGrant: (uuid: string) => Promise<void>;
  releaseGrantAmount: (uuid: string, amount: number) => Promise<void>;

  fetchPublications: (params?: Record<string, any>) => Promise<void>;
  createPublication: (data: Partial<Publication>) => Promise<Publication>;
  updatePublication: (uuid: string, data: Partial<Publication>) => Promise<Publication>;
  deletePublication: (uuid: string) => Promise<void>;

  fetchPatents: (params?: Record<string, any>) => Promise<void>;
  createPatent: (data: Partial<Patent>) => Promise<Patent>;

  fetchTheses: (params?: Record<string, any>) => Promise<void>;
  createThesis: (data: Partial<Thesis>) => Promise<Thesis>;

  fetchInnovations: (params?: Record<string, any>) => Promise<void>;
  createInnovation: (data: Partial<Innovation>) => Promise<Innovation>;

  resetState: () => void;
}

const initialState = {
  dashboard: null,
  dashboardLoading: false,
  projects: [],
  projectsPagination: null,
  projectsLoading: false,
  selectedProject: null,
  grants: [],
  grantsLoading: false,
  publications: [],
  publicationsPagination: null,
  publicationsLoading: false,
  patents: [],
  patentsLoading: false,
  theses: [],
  thesesLoading: false,
  innovations: [],
  innovationsLoading: false,
};

export const useResearchStore = create<ResearchState>((set, get) => ({
  ...initialState,

  // Dashboard
  fetchDashboard: async () => {
    set({ dashboardLoading: true });
    try {
      const dashboard = await researchApi.getDashboard();
      set({ dashboard, dashboardLoading: false });
    } catch {
      set({ dashboardLoading: false });
    }
  },

  // Projects
  fetchProjects: async (params) => {
    set({ projectsLoading: true });
    try {
      const response = await researchApi.getProjects(params);
      set({
        projects: response.data,
        projectsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        projectsLoading: false,
      });
    } catch {
      set({ projectsLoading: false });
    }
  },

  fetchProject: async (uuid) => {
    set({ projectsLoading: true });
    try {
      const project = await researchApi.getProject(uuid);
      set({ selectedProject: project, projectsLoading: false });
    } catch {
      set({ projectsLoading: false });
    }
  },

  createProject: async (data) => {
    const project = await researchApi.createProject(data);
    const projects = [project, ...get().projects];
    set({ projects });
    return project;
  },

  updateProject: async (uuid, data) => {
    const project = await researchApi.updateProject(uuid, data);
    const projects = get().projects.map((p) => (p.id === uuid ? project : p));
    set({ projects, selectedProject: project });
    return project;
  },

  deleteProject: async (uuid) => {
    await researchApi.deleteProject(uuid);
    const projects = get().projects.filter((p) => p.id !== uuid);
    set({ projects });
  },

  approveProject: async (uuid) => {
    await researchApi.approveProject(uuid);
    get().fetchProjects();
  },

  completeProject: async (uuid) => {
    await researchApi.completeProject(uuid);
    get().fetchProjects();
  },

  // Grants
  fetchGrants: async (params) => {
    set({ grantsLoading: true });
    try {
      const response = await researchApi.getGrants(params);
      set({ grants: response.data, grantsLoading: false });
    } catch {
      set({ grantsLoading: false });
    }
  },

  createGrant: async (data) => {
    const grant = await researchApi.createGrant(data);
    const grants = [...get().grants, grant];
    set({ grants });
    return grant;
  },

  approveGrant: async (uuid) => {
    await researchApi.approveGrant(uuid);
    get().fetchGrants();
  },

  releaseGrantAmount: async (uuid, amount) => {
    await researchApi.releaseGrantAmount(uuid, amount);
    get().fetchGrants();
  },

  // Publications
  fetchPublications: async (params) => {
    set({ publicationsLoading: true });
    try {
      const response = await researchApi.getPublications(params);
      set({
        publications: response.data,
        publicationsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        publicationsLoading: false,
      });
    } catch {
      set({ publicationsLoading: false });
    }
  },

  createPublication: async (data) => {
    const publication = await researchApi.createPublication(data);
    const publications = [publication, ...get().publications];
    set({ publications });
    return publication;
  },

  updatePublication: async (uuid, data) => {
    const publication = await researchApi.updatePublication(uuid, data);
    const publications = get().publications.map((p) => (p.id === uuid ? publication : p));
    set({ publications });
    return publication;
  },

  deletePublication: async (uuid) => {
    await researchApi.deletePublication(uuid);
    const publications = get().publications.filter((p) => p.id !== uuid);
    set({ publications });
  },

  // Patents
  fetchPatents: async (params) => {
    set({ patentsLoading: true });
    try {
      const response = await researchApi.getPatents(params);
      set({ patents: response.data, patentsLoading: false });
    } catch {
      set({ patentsLoading: false });
    }
  },

  createPatent: async (data) => {
    const patent = await researchApi.createPatent(data);
    const patents = [...get().patents, patent];
    set({ patents });
    return patent;
  },

  // Theses
  fetchTheses: async (params) => {
    set({ thesesLoading: true });
    try {
      const response = await researchApi.getTheses(params);
      set({ theses: response.data, thesesLoading: false });
    } catch {
      set({ thesesLoading: false });
    }
  },

  createThesis: async (data) => {
    const thesis = await researchApi.createThesis(data);
    const theses = [...get().theses, thesis];
    set({ theses });
    return thesis;
  },

  // Innovations
  fetchInnovations: async (params) => {
    set({ innovationsLoading: true });
    try {
      const response = await researchApi.getInnovations(params);
      set({ innovations: response.data, innovationsLoading: false });
    } catch {
      set({ innovationsLoading: false });
    }
  },

  createInnovation: async (data) => {
    const innovation = await researchApi.createInnovation(data);
    const innovations = [...get().innovations, innovation];
    set({ innovations });
    return innovation;
  },

  // Reset
  resetState: () => set(initialState),
}));
