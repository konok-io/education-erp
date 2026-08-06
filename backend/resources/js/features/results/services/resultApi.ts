/**
 * Result & Examination API
 */

import { apiClient } from '@/lib/api-client';
import type { Exam, Result, ResultDetail, MarkEntryItem, GradeRule, MeritListItem, FailListItem, Transcript, Marksheet, GPAResult, CGPAResult, ResultAnalytics } from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== EXAMS =====================

export const getExams = async (filters?: any): Promise<PaginatedResponse<Exam>> => {
  const response = await apiClient.get('/api/v1/results/exams', { params: filters });
  return response.data;
};

export const createExam = async (data: Partial<Exam>): Promise<Exam> => {
  const response = await apiClient.post('/api/v1/results/exams', data);
  return response.data.data;
};

export const updateExam = async (uuid: string, data: Partial<Exam>): Promise<Exam> => {
  const response = await apiClient.put(`/api/v1/results/exams/${uuid}`, data);
  return response.data.data;
};

export const deleteExam = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/results/exams/${uuid}`);
};

// ===================== MARK ENTRY =====================

export const entryMarks = async (data: {
  exam_id: string;
  subject_id: string;
  marks: MarkEntryItem[];
}): Promise<{ total: number; success: number; failed: number; errors: string[] }> => {
  const response = await apiClient.post('/api/v1/results/marks', data);
  return response.data.data;
};

export const updateMarks = async (uuid: string, data: Partial<ResultDetail>): Promise<ResultDetail> => {
  const response = await apiClient.put(`/api/v1/results/marks/${uuid}`, data);
  return response.data.data;
};

// ===================== RESULTS =====================

export const getStudentResults = async (data: {
  student_id: string;
  session_id?: string;
  semester_id?: string;
}): Promise<Result[]> => {
  const response = await apiClient.get('/api/v1/results/student', { params: data });
  return response.data.data;
};

export const getClassResults = async (data: {
  exam_id: string;
  class_id?: string;
  section_id?: string;
}): Promise<PaginatedResponse<Result>> => {
  const response = await apiClient.get('/api/v1/results/class', { params: data });
  return response.data;
};

// ===================== PROCESSING =====================

export const processResults = async (examId: string): Promise<{ processed: number; exam_id: string }> => {
  const response = await apiClient.post('/api/v1/results/process', { exam_id: examId });
  return response.data.data;
};

// ===================== GPA/CGPA =====================

export const calculateGPA = async (data: {
  student_id: string;
  semester_id?: string;
}): Promise<GPAResult> => {
  const response = await apiClient.get('/api/v1/results/gpa', { params: data });
  return response.data.data;
};

export const calculateCGPA = async (studentId: string): Promise<CGPAResult> => {
  const response = await apiClient.get('/api/v1/results/cgpa', { params: { student_id: studentId } });
  return response.data.data;
};

// ===================== PUBLISH/APPROVE =====================

export const verifyResult = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/results/${uuid}/verify`);
};

export const approveResult = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/results/${uuid}/approve`);
};

export const publishResults = async (examId: string): Promise<void> => {
  await apiClient.post('/api/v1/results/publish', { exam_id: examId });
};

export const lockResult = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/results/${uuid}/lock`);
};

// ===================== TRANSCRIPT/MARKSHEET =====================

export const getTranscript = async (studentId: string): Promise<Transcript> => {
  const response = await apiClient.get(`/api/v1/results/transcript/${studentId}`);
  return response.data.data;
};

export const getMarksheet = async (data: {
  student_id: string;
  exam_id: string;
}): Promise<Marksheet> => {
  const response = await apiClient.get('/api/v1/results/marksheet', { params: data });
  return response.data.data;
};

// ===================== MERIT/FAIL LIST =====================

export const getMeritList = async (data: {
  exam_id: string;
  class_id?: string;
  section_id?: string;
  limit?: number;
}): Promise<MeritListItem[]> => {
  const response = await apiClient.get('/api/v1/results/merit-list', { params: data });
  return response.data.data;
};

export const getFailList = async (data: {
  exam_id: string;
  class_id?: string;
}): Promise<FailListItem[]> => {
  const response = await apiClient.get('/api/v1/results/fail-list', { params: data });
  return response.data.data;
};

// ===================== ANALYTICS =====================

export const getResultAnalytics = async (examId: string): Promise<ResultAnalytics> => {
  const response = await apiClient.get('/api/v1/results/analytics', { params: { exam_id: examId } });
  return response.data.data;
};

export const getSubjectAnalysis = async (data: {
  exam_id: string;
  subject_id?: string;
}): Promise<any> => {
  const response = await apiClient.get('/api/v1/results/subject-analysis', { params: data });
  return response.data.data;
};

// ===================== GRADE RULES =====================

export const getGradeRules = async (): Promise<GradeRule[]> => {
  const response = await apiClient.get('/api/v1/results/grade-rules');
  return response.data.data;
};

export const createGradeRule = async (data: Partial<GradeRule>): Promise<GradeRule> => {
  const response = await apiClient.post('/api/v1/results/grade-rules', data);
  return response.data.data;
};

// ===================== EXPORT =====================

export const exportResults = async (data: {
  exam_id: string;
  format: 'excel' | 'csv' | 'pdf';
  class_id?: string;
  section_id?: string;
}): Promise<string> => {
  const response = await apiClient.get('/api/v1/results/export', { params: data });
  return response.data.data.url;
};
