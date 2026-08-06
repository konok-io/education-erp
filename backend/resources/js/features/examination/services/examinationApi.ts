/**
 * Examination API Service
 */

import { apiClient } from '@/lib/api-client';
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
  PaginatedResponse,
} from '../types';

const BASE_URL = '/api/v1/examinations';

export const examinationApi = {
  // Dashboard
  getDashboard: async (): Promise<ExamDashboard> => {
    const response = await apiClient.get(`${BASE_URL}/dashboard`);
    return response.data;
  },

  // Sessions
  getSessions: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    status?: string;
  }): Promise<PaginatedResponse<ExamSession>> => {
    const response = await apiClient.get(`${BASE_URL}/sessions`, { params });
    return response.data;
  },

  createSession: async (data: Partial<ExamSession>): Promise<ExamSession> => {
    const response = await apiClient.post(`${BASE_URL}/sessions`, data);
    return response.data;
  },

  setCurrentSession: async (uuid: string): Promise<ExamSession> => {
    const response = await apiClient.post(`${BASE_URL}/sessions/${uuid}/set-current`);
    return response.data;
  },

  // Exams
  getExams: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    exam_type?: string;
    status?: string;
    exam_session_id?: string;
  }): Promise<PaginatedResponse<Exam>> => {
    const response = await apiClient.get(BASE_URL, { params });
    return response.data;
  },

  getExam: async (uuid: string): Promise<Exam> => {
    const response = await apiClient.get(`${BASE_URL}/${uuid}`);
    return response.data;
  },

  createExam: async (data: Partial<Exam>): Promise<Exam> => {
    const response = await apiClient.post(BASE_URL, data);
    return response.data;
  },

  updateExam: async (uuid: string, data: Partial<Exam>): Promise<Exam> => {
    const response = await apiClient.put(`${BASE_URL}/${uuid}`, data);
    return response.data;
  },

  deleteExam: async (uuid: string): Promise<void> => {
    await apiClient.delete(`${BASE_URL}/${uuid}`);
  },

  publishExam: async (uuid: string): Promise<Exam> => {
    const response = await apiClient.post(`${BASE_URL}/${uuid}/publish`);
    return response.data;
  },

  // Subjects
  getSubjects: async (params?: {
    page?: number;
    per_page?: number;
    exam_id?: string;
  }): Promise<PaginatedResponse<ExamSubject>> => {
    const response = await apiClient.get(`${BASE_URL}/subjects`, { params });
    return response.data;
  },

  createSubject: async (data: Partial<ExamSubject>): Promise<ExamSubject> => {
    const response = await apiClient.post(`${BASE_URL}/subjects`, data);
    return response.data;
  },

  // Halls
  getHalls: async (params?: {
    page?: number;
    per_page?: number;
    search?: string;
    status?: string;
  }): Promise<PaginatedResponse<ExamHall>> => {
    const response = await apiClient.get(`${BASE_URL}/halls`, { params });
    return response.data;
  },

  createHall: async (data: Partial<ExamHall>): Promise<ExamHall> => {
    const response = await apiClient.post(`${BASE_URL}/halls`, data);
    return response.data;
  },

  // Committees
  getCommittees: async (params?: {
    page?: number;
    per_page?: number;
    exam_session_id?: string;
  }): Promise<PaginatedResponse<ExamCommittee>> => {
    const response = await apiClient.get(`${BASE_URL}/committees`, { params });
    return response.data;
  },

  createCommittee: async (data: Partial<ExamCommittee>): Promise<ExamCommittee> => {
    const response = await apiClient.post(`${BASE_URL}/committees`, data);
    return response.data;
  },

  // Invigilators
  getInvigilators: async (params?: {
    page?: number;
    per_page?: number;
    exam_id?: string;
    duty_date?: string;
  }): Promise<PaginatedResponse<ExamInvigilator>> => {
    const response = await apiClient.get(`${BASE_URL}/invigilators`, { params });
    return response.data;
  },

  assignInvigilator: async (data: Partial<ExamInvigilator>): Promise<ExamInvigilator> => {
    const response = await apiClient.post(`${BASE_URL}/invigilators`, data);
    return response.data;
  },

  // Seat Plans
  getSeatPlans: async (params?: {
    page?: number;
    per_page?: number;
    exam_id?: string;
    exam_subject_id?: string;
    exam_hall_id?: string;
  }): Promise<PaginatedResponse<ExamSeatPlan>> => {
    const response = await apiClient.get(`${BASE_URL}/seat-plans`, { params });
    return response.data;
  },

  generateSeatPlan: async (data: {
    exam_id: string;
    exam_subject_id: string;
    exam_hall_id: string;
    students: Array<{
      id: number;
      name: string;
      roll: string;
      registration_no?: string;
    }>;
  }): Promise<ExamSeatPlan[]> => {
    const response = await apiClient.post(`${BASE_URL}/seat-plans/generate`, data);
    return response.data;
  },

  // Admit Cards
  getAdmitCards: async (params?: {
    page?: number;
    per_page?: number;
    exam_id?: string;
    student_id?: number;
    status?: string;
  }): Promise<PaginatedResponse<ExamAdmitCard>> => {
    const response = await apiClient.get(`${BASE_URL}/admit-cards`, { params });
    return response.data;
  },

  generateAdmitCards: async (data: {
    exam_id: string;
    valid_until?: string;
    students: Array<{
      id: number;
      name: string;
      roll: string;
      registration_no?: string;
      class_name?: string;
      section?: string;
    }>;
  }): Promise<ExamAdmitCard[]> => {
    const response = await apiClient.post(`${BASE_URL}/admit-cards/generate`, data);
    return response.data;
  },

  verifyAdmitCard: async (token: string): Promise<ExamAdmitCard | null> => {
    try {
      const response = await apiClient.get(`${BASE_URL}/admit-card/verify/${token}`);
      return response.data;
    } catch {
      return null;
    }
  },

  // Attendance
  getAttendances: async (params?: {
    page?: number;
    per_page?: number;
    exam_subject_id?: string;
    status?: string;
  }): Promise<PaginatedResponse<ExamAttendance>> => {
    const response = await apiClient.get(`${BASE_URL}/attendance`, { params });
    return response.data;
  },

  recordAttendance: async (data: Partial<ExamAttendance>): Promise<ExamAttendance> => {
    const response = await apiClient.post(`${BASE_URL}/attendance`, data);
    return response.data;
  },

  bulkRecordAttendance: async (records: Partial<ExamAttendance>[]): Promise<void> => {
    await apiClient.post(`${BASE_URL}/attendance/bulk`, { records });
  },

  // Marks
  getMarks: async (params?: {
    page?: number;
    per_page?: number;
    exam_subject_id?: string;
    status?: string;
  }): Promise<PaginatedResponse<ExamMark>> => {
    const response = await apiClient.get(`${BASE_URL}/marks`, { params });
    return response.data;
  },

  enterMarks: async (data: Partial<ExamMark>): Promise<ExamMark> => {
    const response = await apiClient.post(`${BASE_URL}/marks`, data);
    return response.data;
  },

  bulkEnterMarks: async (marks: Partial<ExamMark>[]): Promise<ExamMark[]> => {
    const response = await apiClient.post(`${BASE_URL}/marks/bulk`, { marks });
    return response.data;
  },

  approveMarks: async (uuid: string): Promise<ExamMark> => {
    const response = await apiClient.post(`${BASE_URL}/marks/${uuid}/approve`);
    return response.data;
  },

  // Malpractices
  getMalpractices: async (params?: {
    page?: number;
    per_page?: number;
    exam_subject_id?: string;
    status?: string;
  }): Promise<PaginatedResponse<ExamMalpractice>> => {
    const response = await apiClient.get(`${BASE_URL}/malpractices`, { params });
    return response.data;
  },

  reportMalpractice: async (data: Partial<ExamMalpractice>): Promise<ExamMalpractice> => {
    const response = await apiClient.post(`${BASE_URL}/malpractices`, data);
    return response.data;
  },
};
