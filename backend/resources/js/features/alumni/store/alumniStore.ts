/**
 * Alumni Store - State Management
 */

import { create } from 'zustand';
import { alumniApi } from '../services/alumniApi';
import type {
  AlumniProfile,
  Employer,
  Job,
  JobApplication,
  Internship,
  Placement,
  AlumniEvent,
  Mentorship,
  Donation,
  FundraisingCampaign,
  AlumniDashboard,
} from '../types';

interface AlumniState {
  // Dashboard
  dashboard: AlumniDashboard | null;
  dashboardLoading: boolean;

  // Alumni
  alumni: AlumniProfile[];
  alumniPagination: { current_page: number; last_page: number; total: number } | null;
  alumniLoading: boolean;
  selectedAlumni: AlumniProfile | null;

  // Employers
  employers: Employer[];
  employersLoading: boolean;

  // Jobs
  jobs: Job[];
  jobsPagination: { current_page: number; last_page: number; total: number } | null;
  jobsLoading: boolean;
  selectedJob: Job | null;

  // Internships
  internships: Internship[];
  internshipsLoading: boolean;

  // Placements
  placements: Placement[];
  placementsLoading: boolean;

  // Events
  events: AlumniEvent[];
  eventsPagination: { current_page: number; last_page: number; total: number } | null;
  eventsLoading: boolean;

  // Mentorships
  mentorships: Mentorship[];
  mentorshipsLoading: boolean;

  // Donations
  donations: Donation[];
  donationsLoading: boolean;

  // Campaigns
  campaigns: FundraisingCampaign[];
  campaignsLoading: boolean;

  // Actions
  fetchDashboard: () => Promise<void>;
  fetchAlumni: (params?: Record<string, any>) => Promise<void>;
  fetchAlumniProfile: (uuid: string) => Promise<void>;
  createAlumniProfile: (data: Partial<AlumniProfile>) => Promise<AlumniProfile>;
  updateAlumniProfile: (uuid: string, data: Partial<AlumniProfile>) => Promise<AlumniProfile>;
  deleteAlumniProfile: (uuid: string) => Promise<void>;
  verifyAlumniProfile: (uuid: string) => Promise<void>;

  fetchEmployers: (params?: Record<string, any>) => Promise<void>;
  createEmployer: (data: Partial<Employer>) => Promise<Employer>;
  updateEmployer: (uuid: string, data: Partial<Employer>) => Promise<Employer>;
  deleteEmployer: (uuid: string) => Promise<void>;
  verifyEmployer: (uuid: string) => Promise<void>;

  fetchJobs: (params?: Record<string, any>) => Promise<void>;
  createJob: (data: Partial<Job>) => Promise<Job>;
  updateJob: (uuid: string, data: Partial<Job>) => Promise<Job>;
  deleteJob: (uuid: string) => Promise<void>;
  publishJob: (uuid: string) => Promise<void>;

  fetchInternships: (params?: Record<string, any>) => Promise<void>;
  createInternship: (data: Partial<Internship>) => Promise<Internship>;

  fetchPlacements: (params?: Record<string, any>) => Promise<void>;
  createPlacement: (data: Partial<Placement>) => Promise<Placement>;

  fetchEvents: (params?: Record<string, any>) => Promise<void>;
  createEvent: (data: Partial<AlumniEvent>) => Promise<AlumniEvent>;
  updateEvent: (uuid: string, data: Partial<AlumniEvent>) => Promise<AlumniEvent>;
  publishEvent: (uuid: string) => Promise<void>;
  registerForEvent: (eventId: string, data: any) => Promise<any>;

  fetchMentorships: (params?: Record<string, any>) => Promise<void>;
  createMentorship: (data: Partial<Mentorship>) => Promise<Mentorship>;

  fetchDonations: (params?: Record<string, any>) => Promise<void>;
  createDonation: (data: Partial<Donation>) => Promise<Donation>;

  fetchCampaigns: (params?: Record<string, any>) => Promise<void>;
  createCampaign: (data: Partial<FundraisingCampaign>) => Promise<FundraisingCampaign>;

  resetState: () => void;
}

const initialState = {
  dashboard: null,
  dashboardLoading: false,
  alumni: [],
  alumniPagination: null,
  alumniLoading: false,
  selectedAlumni: null,
  employers: [],
  employersLoading: false,
  jobs: [],
  jobsPagination: null,
  jobsLoading: false,
  selectedJob: null,
  internships: [],
  internshipsLoading: false,
  placements: [],
  placementsLoading: false,
  events: [],
  eventsPagination: null,
  eventsLoading: false,
  mentorships: [],
  mentorshipsLoading: false,
  donations: [],
  donationsLoading: false,
  campaigns: [],
  campaignsLoading: false,
};

export const useAlumniStore = create<AlumniState>((set, get) => ({
  ...initialState,

  // Dashboard
  fetchDashboard: async () => {
    set({ dashboardLoading: true });
    try {
      const dashboard = await alumniApi.getDashboard();
      set({ dashboard, dashboardLoading: false });
    } catch {
      set({ dashboardLoading: false });
    }
  },

  // Alumni
  fetchAlumni: async (params) => {
    set({ alumniLoading: true });
    try {
      const response = await alumniApi.getAlumni(params);
      set({
        alumni: response.data,
        alumniPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        alumniLoading: false,
      });
    } catch {
      set({ alumniLoading: false });
    }
  },

  fetchAlumniProfile: async (uuid) => {
    set({ alumniLoading: true });
    try {
      const alumni = await alumniApi.getAlumniProfile(uuid);
      set({ selectedAlumni: alumni, alumniLoading: false });
    } catch {
      set({ alumniLoading: false });
    }
  },

  createAlumniProfile: async (data) => {
    const alumni = await alumniApi.createAlumniProfile(data);
    const alumniList = [alumni, ...get().alumni];
    set({ alumni: alumniList });
    return alumni;
  },

  updateAlumniProfile: async (uuid, data) => {
    const alumni = await alumniApi.updateAlumniProfile(uuid, data);
    const alumniList = get().alumni.map((a) => (a.id === uuid ? alumni : a));
    set({ alumni: alumniList, selectedAlumni: alumni });
    return alumni;
  },

  deleteAlumniProfile: async (uuid) => {
    await alumniApi.deleteAlumniProfile(uuid);
    const alumni = get().alumni.filter((a) => a.id !== uuid);
    set({ alumni });
  },

  verifyAlumniProfile: async (uuid) => {
    await alumniApi.verifyAlumniProfile(uuid);
    get().fetchAlumni();
  },

  // Employers
  fetchEmployers: async (params) => {
    set({ employersLoading: true });
    try {
      const response = await alumniApi.getEmployers(params);
      set({ employers: response.data, employersLoading: false });
    } catch {
      set({ employersLoading: false });
    }
  },

  createEmployer: async (data) => {
    const employer = await alumniApi.createEmployer(data);
    const employers = [...get().employers, employer];
    set({ employers });
    return employer;
  },

  updateEmployer: async (uuid, data) => {
    const employer = await alumniApi.updateEmployer(uuid, data);
    const employers = get().employers.map((e) => (e.id === uuid ? employer : e));
    set({ employers });
    return employer;
  },

  deleteEmployer: async (uuid) => {
    await alumniApi.deleteEmployer(uuid);
    const employers = get().employers.filter((e) => e.id !== uuid);
    set({ employers });
  },

  verifyEmployer: async (uuid) => {
    await alumniApi.verifyEmployer(uuid);
    get().fetchEmployers();
  },

  // Jobs
  fetchJobs: async (params) => {
    set({ jobsLoading: true });
    try {
      const response = await alumniApi.getJobs(params);
      set({
        jobs: response.data,
        jobsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        jobsLoading: false,
      });
    } catch {
      set({ jobsLoading: false });
    }
  },

  createJob: async (data) => {
    const job = await alumniApi.createJob(data);
    const jobs = [job, ...get().jobs];
    set({ jobs });
    return job;
  },

  updateJob: async (uuid, data) => {
    const job = await alumniApi.updateJob(uuid, data);
    const jobs = get().jobs.map((j) => (j.id === uuid ? job : j));
    set({ jobs, selectedJob: job });
    return job;
  },

  deleteJob: async (uuid) => {
    await alumniApi.deleteJob(uuid);
    const jobs = get().jobs.filter((j) => j.id !== uuid);
    set({ jobs });
  },

  publishJob: async (uuid) => {
    await alumniApi.publishJob(uuid);
    get().fetchJobs();
  },

  // Internships
  fetchInternships: async (params) => {
    set({ internshipsLoading: true });
    try {
      const response = await alumniApi.getInternships(params);
      set({ internships: response.data, internshipsLoading: false });
    } catch {
      set({ internshipsLoading: false });
    }
  },

  createInternship: async (data) => {
    const internship = await alumniApi.createInternship(data);
    const internships = [internship, ...get().internships];
    set({ internships });
    return internship;
  },

  // Placements
  fetchPlacements: async (params) => {
    set({ placementsLoading: true });
    try {
      const response = await alumniApi.getPlacements(params);
      set({ placements: response.data, placementsLoading: false });
    } catch {
      set({ placementsLoading: false });
    }
  },

  createPlacement: async (data) => {
    const placement = await alumniApi.createPlacement(data);
    const placements = [placement, ...get().placements];
    set({ placements });
    return placement;
  },

  // Events
  fetchEvents: async (params) => {
    set({ eventsLoading: true });
    try {
      const response = await alumniApi.getEvents(params);
      set({
        events: response.data,
        eventsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        eventsLoading: false,
      });
    } catch {
      set({ eventsLoading: false });
    }
  },

  createEvent: async (data) => {
    const event = await alumniApi.createEvent(data);
    const events = [event, ...get().events];
    set({ events });
    return event;
  },

  updateEvent: async (uuid, data) => {
    const event = await alumniApi.updateEvent(uuid, data);
    const events = get().events.map((e) => (e.id === uuid ? event : e));
    set({ events });
    return event;
  },

  publishEvent: async (uuid) => {
    await alumniApi.publishEvent(uuid);
    get().fetchEvents();
  },

  registerForEvent: async (eventId, data) => {
    return await alumniApi.registerForEvent(eventId, data);
  },

  // Mentorships
  fetchMentorships: async (params) => {
    set({ mentorshipsLoading: true });
    try {
      const response = await alumniApi.getMentorships(params);
      set({ mentorships: response.data, mentorshipsLoading: false });
    } catch {
      set({ mentorshipsLoading: false });
    }
  },

  createMentorship: async (data) => {
    const mentorship = await alumniApi.createMentorship(data);
    const mentorships = [mentorship, ...get().mentorships];
    set({ mentorships });
    return mentorship;
  },

  // Donations
  fetchDonations: async (params) => {
    set({ donationsLoading: true });
    try {
      const response = await alumniApi.getDonations(params);
      set({ donations: response.data, donationsLoading: false });
    } catch {
      set({ donationsLoading: false });
    }
  },

  createDonation: async (data) => {
    const donation = await alumniApi.createDonation(data);
    const donations = [donation, ...get().donations];
    set({ donations });
    return donation;
  },

  // Campaigns
  fetchCampaigns: async (params) => {
    set({ campaignsLoading: true });
    try {
      const response = await alumniApi.getCampaigns(params);
      set({ campaigns: response.data, campaignsLoading: false });
    } catch {
      set({ campaignsLoading: false });
    }
  },

  createCampaign: async (data) => {
    const campaign = await alumniApi.createCampaign(data);
    const campaigns = [...get().campaigns, campaign];
    set({ campaigns });
    return campaign;
  },

  // Reset
  resetState: () => set(initialState),
}));
