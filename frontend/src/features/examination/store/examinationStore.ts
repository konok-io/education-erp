/**
 * Examination Store - State Management
 */

import { create } from 'zustand';
import { examinationApi } from '../services/examinationApi';
import type {
  Exam,
  ExamSession,
  ExamSubject,
  ExamHall,
  ExamCommittee,
  ExamInvigilator,
  ExamSeatPlan,
  ExamAdmitCard,
  ExamAttendance,
  ExamMark,
  ExamMalpractice,
  ExamDashboard,
} from '../types';

interface ExaminationState {
  // Dashboard
  dashboard: ExamDashboard | null;
  dashboardLoading: boolean;

  // Exams
  exams: Exam[];
  examsPagination: { current_page: number; last_page: number; total: number } | null;
  examsLoading: boolean;
  selectedExam: Exam | null;

  // Sessions
  sessions: ExamSession[];
  sessionsLoading: boolean;
  currentSession: ExamSession | null;

  // Subjects
  subjects: ExamSubject[];
  subjectsLoading: boolean;

  // Halls
  halls: ExamHall[];
  hallsPagination: { current_page: number; last_page: number; total: number } | null;
  hallsLoading: boolean;

  // Committees
  committees: ExamCommittee[];
  committeesLoading: boolean;

  // Invigilators
  invigilators: ExamInvigilator[];
  invigilatorsLoading: boolean;

  // Seat Plans
  seatPlans: ExamSeatPlan[];
  seatPlansLoading: boolean;

  // Admit Cards
  admitCards: ExamAdmitCard[];
  admitCardsPagination: { current_page: number; last_page: number; total: number } | null;
  admitCardsLoading: boolean;

  // Attendance
  attendances: ExamAttendance[];
  attendancesLoading: boolean;

  // Marks
  marks: ExamMark[];
  marksPagination: { current_page: number; last_page: number; total: number } | null;
  marksLoading: boolean;

  // Malpractices
  malpractices: ExamMalpractice[];
  malpracticesLoading: boolean;

  // Actions
  fetchDashboard: () => Promise<void>;
  fetchExams: (params?: Record<string, any>) => Promise<void>;
  fetchExam: (uuid: string) => Promise<void>;
  createExam: (data: Partial<Exam>) => Promise<Exam>;
  updateExam: (uuid: string, data: Partial<Exam>) => Promise<Exam>;
  deleteExam: (uuid: string) => Promise<void>;
  publishExam: (uuid: string) => Promise<void>;

  fetchSessions: (params?: Record<string, any>) => Promise<void>;
  createSession: (data: Partial<ExamSession>) => Promise<ExamSession>;
  setCurrentSession: (uuid: string) => Promise<void>;

  fetchSubjects: (params?: Record<string, any>) => Promise<void>;
  createSubject: (data: Partial<ExamSubject>) => Promise<ExamSubject>;

  fetchHalls: (params?: Record<string, any>) => Promise<void>;
  createHall: (data: Partial<ExamHall>) => Promise<ExamHall>;

  fetchCommittees: (params?: Record<string, any>) => Promise<void>;
  createCommittee: (data: Partial<ExamCommittee>) => Promise<ExamCommittee>;

  fetchInvigilators: (params?: Record<string, any>) => Promise<void>;
  assignInvigilator: (data: Partial<ExamInvigilator>) => Promise<ExamInvigilator>;

  fetchSeatPlans: (params?: Record<string, any>) => Promise<void>;
  generateSeatPlan: (data: any) => Promise<ExamSeatPlan[]>;

  fetchAdmitCards: (params?: Record<string, any>) => Promise<void>;
  generateAdmitCards: (data: any) => Promise<ExamAdmitCard[]>;

  fetchAttendances: (params?: Record<string, any>) => Promise<void>;
  recordAttendance: (data: Partial<ExamAttendance>) => Promise<ExamAttendance>;
  bulkRecordAttendance: (records: Partial<ExamAttendance>[]) => Promise<void>;

  fetchMarks: (params?: Record<string, any>) => Promise<void>;
  enterMarks: (data: Partial<ExamMark>) => Promise<ExamMark>;
  bulkEnterMarks: (marks: Partial<ExamMark>[]) => Promise<ExamMark[]>;
  approveMarks: (uuid: string) => Promise<void>;

  fetchMalpractices: (params?: Record<string, any>) => Promise<void>;
  reportMalpractice: (data: Partial<ExamMalpractice>) => Promise<ExamMalpractice>;

  resetState: () => void;
}

const initialState = {
  dashboard: null,
  dashboardLoading: false,
  exams: [],
  examsPagination: null,
  examsLoading: false,
  selectedExam: null,
  sessions: [],
  sessionsLoading: false,
  currentSession: null,
  subjects: [],
  subjectsLoading: false,
  halls: [],
  hallsPagination: null,
  hallsLoading: false,
  committees: [],
  committeesLoading: false,
  invigilators: [],
  invigilatorsLoading: false,
  seatPlans: [],
  seatPlansLoading: false,
  admitCards: [],
  admitCardsPagination: null,
  admitCardsLoading: false,
  attendances: [],
  attendancesLoading: false,
  marks: [],
  marksPagination: null,
  marksLoading: false,
  malpractices: [],
  malpracticesLoading: false,
};

export const useExaminationStore = create<ExaminationState>((set, get) => ({
  ...initialState,

  // Dashboard
  fetchDashboard: async () => {
    set({ dashboardLoading: true });
    try {
      const dashboard = await examinationApi.getDashboard();
      set({ dashboard, dashboardLoading: false });
    } catch (error) {
      set({ dashboardLoading: false });
    }
  },

  // Exams
  fetchExams: async (params) => {
    set({ examsLoading: true });
    try {
      const response = await examinationApi.getExams(params);
      set({
        exams: response.data,
        examsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        examsLoading: false,
      });
    } catch (error) {
      set({ examsLoading: false });
    }
  },

  fetchExam: async (uuid) => {
    set({ examsLoading: true });
    try {
      const exam = await examinationApi.getExam(uuid);
      set({ selectedExam: exam, examsLoading: false });
    } catch (error) {
      set({ examsLoading: false });
    }
  },

  createExam: async (data) => {
    const exam = await examinationApi.createExam(data);
    const exams = [exam, ...get().exams];
    set({ exams });
    return exam;
  },

  updateExam: async (uuid, data) => {
    const exam = await examinationApi.updateExam(uuid, data);
    const exams = get().exams.map((e) => (e.id === uuid ? exam : e));
    set({ exams, selectedExam: exam });
    return exam;
  },

  deleteExam: async (uuid) => {
    await examinationApi.deleteExam(uuid);
    const exams = get().exams.filter((e) => e.id !== uuid);
    set({ exams });
  },

  publishExam: async (uuid) => {
    const exam = await examinationApi.publishExam(uuid);
    const exams = get().exams.map((e) => (e.id === uuid ? exam : e));
    set({ exams, selectedExam: exam });
  },

  // Sessions
  fetchSessions: async (params) => {
    set({ sessionsLoading: true });
    try {
      const response = await examinationApi.getSessions(params);
      set({ sessions: response.data, sessionsLoading: false });
      const current = response.data.find((s: ExamSession) => s.is_current);
      if (current) set({ currentSession: current });
    } catch (error) {
      set({ sessionsLoading: false });
    }
  },

  createSession: async (data) => {
    const session = await examinationApi.createSession(data);
    const sessions = [...get().sessions, session];
    set({ sessions });
    return session;
  },

  setCurrentSession: async (uuid) => {
    const session = await examinationApi.setCurrentSession(uuid);
    const sessions = get().sessions.map((s) =>
      s.id === uuid ? { ...s, is_current: true } : { ...s, is_current: false }
    );
    set({ sessions, currentSession: session });
  },

  // Subjects
  fetchSubjects: async (params) => {
    set({ subjectsLoading: true });
    try {
      const response = await examinationApi.getSubjects(params);
      set({ subjects: response.data, subjectsLoading: false });
    } catch (error) {
      set({ subjectsLoading: false });
    }
  },

  createSubject: async (data) => {
    const subject = await examinationApi.createSubject(data);
    const subjects = [...get().subjects, subject];
    set({ subjects });
    return subject;
  },

  // Halls
  fetchHalls: async (params) => {
    set({ hallsLoading: true });
    try {
      const response = await examinationApi.getHalls(params);
      set({
        halls: response.data,
        hallsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        hallsLoading: false,
      });
    } catch (error) {
      set({ hallsLoading: false });
    }
  },

  createHall: async (data) => {
    const hall = await examinationApi.createHall(data);
    const halls = [...get().halls, hall];
    set({ halls });
    return hall;
  },

  // Committees
  fetchCommittees: async (params) => {
    set({ committeesLoading: true });
    try {
      const response = await examinationApi.getCommittees(params);
      set({ committees: response.data, committeesLoading: false });
    } catch (error) {
      set({ committeesLoading: false });
    }
  },

  createCommittee: async (data) => {
    const committee = await examinationApi.createCommittee(data);
    const committees = [...get().committees, committee];
    set({ committees });
    return committee;
  },

  // Invigilators
  fetchInvigilators: async (params) => {
    set({ invigilatorsLoading: true });
    try {
      const response = await examinationApi.getInvigilators(params);
      set({ invigilators: response.data, invigilatorsLoading: false });
    } catch (error) {
      set({ invigilatorsLoading: false });
    }
  },

  assignInvigilator: async (data) => {
    const invigilator = await examinationApi.assignInvigilator(data);
    const invigilators = [...get().invigilators, invigilator];
    set({ invigilators });
    return invigilator;
  },

  // Seat Plans
  fetchSeatPlans: async (params) => {
    set({ seatPlansLoading: true });
    try {
      const response = await examinationApi.getSeatPlans(params);
      set({ seatPlans: response.data, seatPlansLoading: false });
    } catch (error) {
      set({ seatPlansLoading: false });
    }
  },

  generateSeatPlan: async (data) => {
    const seatPlans = await examinationApi.generateSeatPlan(data);
    set({ seatPlans: [...get().seatPlans, ...seatPlans] });
    return seatPlans;
  },

  // Admit Cards
  fetchAdmitCards: async (params) => {
    set({ admitCardsLoading: true });
    try {
      const response = await examinationApi.getAdmitCards(params);
      set({
        admitCards: response.data,
        admitCardsPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        admitCardsLoading: false,
      });
    } catch (error) {
      set({ admitCardsLoading: false });
    }
  },

  generateAdmitCards: async (data) => {
    const admitCards = await examinationApi.generateAdmitCards(data);
    set({ admitCards: [...get().admitCards, ...admitCards] });
    return admitCards;
  },

  // Attendance
  fetchAttendances: async (params) => {
    set({ attendancesLoading: true });
    try {
      const response = await examinationApi.getAttendances(params);
      set({ attendances: response.data, attendancesLoading: false });
    } catch (error) {
      set({ attendancesLoading: false });
    }
  },

  recordAttendance: async (data) => {
    const attendance = await examinationApi.recordAttendance(data);
    const attendances = [...get().attendances, attendance];
    set({ attendances });
    return attendance;
  },

  bulkRecordAttendance: async (records) => {
    await examinationApi.bulkRecordAttendance(records);
    get().fetchAttendances();
  },

  // Marks
  fetchMarks: async (params) => {
    set({ marksLoading: true });
    try {
      const response = await examinationApi.getMarks(params);
      set({
        marks: response.data,
        marksPagination: {
          current_page: response.meta.current_page,
          last_page: response.meta.last_page,
          total: response.meta.total,
        },
        marksLoading: false,
      });
    } catch (error) {
      set({ marksLoading: false });
    }
  },

  enterMarks: async (data) => {
    const mark = await examinationApi.enterMarks(data);
    const marks = get().marks.map((m) => (m.id === mark.id ? mark : m));
    if (!marks.find((m) => m.id === mark.id)) {
      marks.push(mark);
    }
    set({ marks });
    return mark;
  },

  bulkEnterMarks: async (marksData) => {
    const marks = await examinationApi.bulkEnterMarks(marksData);
    get().fetchMarks();
    return marks;
  },

  approveMarks: async (uuid) => {
    await examinationApi.approveMarks(uuid);
    get().fetchMarks();
  },

  // Malpractices
  fetchMalpractices: async (params) => {
    set({ malpracticesLoading: true });
    try {
      const response = await examinationApi.getMalpractices(params);
      set({ malpractices: response.data, malpracticesLoading: false });
    } catch (error) {
      set({ malpracticesLoading: false });
    }
  },

  reportMalpractice: async (data) => {
    const malpractice = await examinationApi.reportMalpractice(data);
    const malpractices = [...get().malpractices, malpractice];
    set({ malpractices });
    return malpractice;
  },

  // Reset
  resetState: () => set(initialState),
}));
