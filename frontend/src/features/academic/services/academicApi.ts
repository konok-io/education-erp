/**
 * Academic API
 */

import { apiClient } from '@/lib/api-client';
import type { 
  AcademicLevel, 
  Faculty, 
  Department, 
  Program, 
  AcademicSession, 
  Semester,
  AcademicClass,
  Section,
  Group,
  SubjectCategory,
  Subject,
  GradeRule,
  GpaRule,
  AcademicCalendar,
  AcademicHierarchy,
  AcademicFilters
} from '../types';
import type { PaginatedResponse } from '@/types';

// ===================== ACADEMIC LEVELS =====================

export const getAcademicLevels = async (filters?: AcademicFilters): Promise<PaginatedResponse<AcademicLevel>> => {
  const response = await apiClient.get('/api/v1/academic/levels', { params: filters });
  return response.data;
};

export const getAcademicLevel = async (uuid: string): Promise<AcademicLevel> => {
  const response = await apiClient.get(`/api/v1/academic/levels/${uuid}`);
  return response.data.data;
};

export const createAcademicLevel = async (data: Partial<AcademicLevel>): Promise<AcademicLevel> => {
  const response = await apiClient.post('/api/v1/academic/levels', data);
  return response.data.data;
};

export const updateAcademicLevel = async (uuid: string, data: Partial<AcademicLevel>): Promise<AcademicLevel> => {
  const response = await apiClient.put(`/api/v1/academic/levels/${uuid}`, data);
  return response.data.data;
};

export const deleteAcademicLevel = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/levels/${uuid}`);
};

// ===================== FACULTIES =====================

export const getFaculties = async (filters?: AcademicFilters): Promise<PaginatedResponse<Faculty>> => {
  const response = await apiClient.get('/api/v1/academic/faculties', { params: filters });
  return response.data;
};

export const getFaculty = async (uuid: string): Promise<Faculty> => {
  const response = await apiClient.get(`/api/v1/academic/faculties/${uuid}`);
  return response.data.data;
};

export const createFaculty = async (data: Partial<Faculty>): Promise<Faculty> => {
  const response = await apiClient.post('/api/v1/academic/faculties', data);
  return response.data.data;
};

export const updateFaculty = async (uuid: string, data: Partial<Faculty>): Promise<Faculty> => {
  const response = await apiClient.put(`/api/v1/academic/faculties/${uuid}`, data);
  return response.data.data;
};

export const deleteFaculty = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/faculties/${uuid}`);
};

// ===================== DEPARTMENTS =====================

export const getDepartments = async (filters?: AcademicFilters): Promise<PaginatedResponse<Department>> => {
  const response = await apiClient.get('/api/v1/academic/departments', { params: filters });
  return response.data;
};

export const getDepartment = async (uuid: string): Promise<Department> => {
  const response = await apiClient.get(`/api/v1/academic/departments/${uuid}`);
  return response.data.data;
};

export const createDepartment = async (data: Partial<Department>): Promise<Department> => {
  const response = await apiClient.post('/api/v1/academic/departments', data);
  return response.data.data;
};

export const updateDepartment = async (uuid: string, data: Partial<Department>): Promise<Department> => {
  const response = await apiClient.put(`/api/v1/academic/departments/${uuid}`, data);
  return response.data.data;
};

export const deleteDepartment = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/departments/${uuid}`);
};

// ===================== PROGRAMS =====================

export const getPrograms = async (filters?: AcademicFilters): Promise<PaginatedResponse<Program>> => {
  const response = await apiClient.get('/api/v1/academic/programs', { params: filters });
  return response.data;
};

export const getProgram = async (uuid: string): Promise<Program> => {
  const response = await apiClient.get(`/api/v1/academic/programs/${uuid}`);
  return response.data.data;
};

export const createProgram = async (data: Partial<Program>): Promise<Program> => {
  const response = await apiClient.post('/api/v1/academic/programs', data);
  return response.data.data;
};

export const updateProgram = async (uuid: string, data: Partial<Program>): Promise<Program> => {
  const response = await apiClient.put(`/api/v1/academic/programs/${uuid}`, data);
  return response.data.data;
};

export const deleteProgram = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/programs/${uuid}`);
};

// ===================== SESSIONS =====================

export const getSessions = async (filters?: AcademicFilters): Promise<PaginatedResponse<AcademicSession>> => {
  const response = await apiClient.get('/api/v1/academic/sessions', { params: filters });
  return response.data;
};

export const getSession = async (uuid: string): Promise<AcademicSession> => {
  const response = await apiClient.get(`/api/v1/academic/sessions/${uuid}`);
  return response.data.data;
};

export const createSession = async (data: Partial<AcademicSession>): Promise<AcademicSession> => {
  const response = await apiClient.post('/api/v1/academic/sessions', data);
  return response.data.data;
};

export const updateSession = async (uuid: string, data: Partial<AcademicSession>): Promise<AcademicSession> => {
  const response = await apiClient.put(`/api/v1/academic/sessions/${uuid}`, data);
  return response.data.data;
};

export const deleteSession = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/sessions/${uuid}`);
};

export const setCurrentSession = async (uuid: string): Promise<void> => {
  await apiClient.post(`/api/v1/academic/sessions/${uuid}/set-current`);
};

// ===================== SEMESTERS =====================

export const getSemesters = async (filters?: AcademicFilters): Promise<PaginatedResponse<Semester>> => {
  const response = await apiClient.get('/api/v1/academic/semesters', { params: filters });
  return response.data;
};

export const getSemester = async (uuid: string): Promise<Semester> => {
  const response = await apiClient.get(`/api/v1/academic/semesters/${uuid}`);
  return response.data.data;
};

export const createSemester = async (data: Partial<Semester>): Promise<Semester> => {
  const response = await apiClient.post('/api/v1/academic/semesters', data);
  return response.data.data;
};

export const updateSemester = async (uuid: string, data: Partial<Semester>): Promise<Semester> => {
  const response = await apiClient.put(`/api/v1/academic/semesters/${uuid}`, data);
  return response.data.data;
};

export const deleteSemester = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/semesters/${uuid}`);
};

// ===================== CLASSES =====================

export const getClasses = async (filters?: AcademicFilters): Promise<PaginatedResponse<AcademicClass>> => {
  const response = await apiClient.get('/api/v1/academic/classes', { params: filters });
  return response.data;
};

export const getClass = async (uuid: string): Promise<AcademicClass> => {
  const response = await apiClient.get(`/api/v1/academic/classes/${uuid}`);
  return response.data.data;
};

export const createClass = async (data: Partial<AcademicClass>): Promise<AcademicClass> => {
  const response = await apiClient.post('/api/v1/academic/classes', data);
  return response.data.data;
};

export const updateClass = async (uuid: string, data: Partial<AcademicClass>): Promise<AcademicClass> => {
  const response = await apiClient.put(`/api/v1/academic/classes/${uuid}`, data);
  return response.data.data;
};

export const deleteClass = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/classes/${uuid}`);
};

// ===================== SECTIONS =====================

export const getSections = async (filters?: AcademicFilters): Promise<PaginatedResponse<Section>> => {
  const response = await apiClient.get('/api/v1/academic/sections', { params: filters });
  return response.data;
};

export const createSection = async (data: Partial<Section>): Promise<Section> => {
  const response = await apiClient.post('/api/v1/academic/sections', data);
  return response.data.data;
};

export const updateSection = async (uuid: string, data: Partial<Section>): Promise<Section> => {
  const response = await apiClient.put(`/api/v1/academic/sections/${uuid}`, data);
  return response.data.data;
};

export const deleteSection = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/sections/${uuid}`);
};

// ===================== GROUPS =====================

export const getGroups = async (filters?: AcademicFilters): Promise<PaginatedResponse<Group>> => {
  const response = await apiClient.get('/api/v1/academic/groups', { params: filters });
  return response.data;
};

export const createGroup = async (data: Partial<Group>): Promise<Group> => {
  const response = await apiClient.post('/api/v1/academic/groups', data);
  return response.data.data;
};

export const updateGroup = async (uuid: string, data: Partial<Group>): Promise<Group> => {
  const response = await apiClient.put(`/api/v1/academic/groups/${uuid}`, data);
  return response.data.data;
};

export const deleteGroup = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/groups/${uuid}`);
};

// ===================== SUBJECTS =====================

export const getSubjects = async (filters?: AcademicFilters): Promise<PaginatedResponse<Subject>> => {
  const response = await apiClient.get('/api/v1/academic/subjects', { params: filters });
  return response.data;
};

export const getSubject = async (uuid: string): Promise<Subject> => {
  const response = await apiClient.get(`/api/v1/academic/subjects/${uuid}`);
  return response.data.data;
};

export const createSubject = async (data: Partial<Subject>): Promise<Subject> => {
  const response = await apiClient.post('/api/v1/academic/subjects', data);
  return response.data.data;
};

export const updateSubject = async (uuid: string, data: Partial<Subject>): Promise<Subject> => {
  const response = await apiClient.put(`/api/v1/academic/subjects/${uuid}`, data);
  return response.data.data;
};

export const deleteSubject = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/subjects/${uuid}`);
};

// ===================== GRADE RULES =====================

export const getGradeRules = async (filters?: AcademicFilters): Promise<PaginatedResponse<GradeRule>> => {
  const response = await apiClient.get('/api/v1/academic/grade-rules', { params: filters });
  return response.data;
};

export const createGradeRule = async (data: Partial<GradeRule>): Promise<GradeRule> => {
  const response = await apiClient.post('/api/v1/academic/grade-rules', data);
  return response.data.data;
};

export const updateGradeRule = async (uuid: string, data: Partial<GradeRule>): Promise<GradeRule> => {
  const response = await apiClient.put(`/api/v1/academic/grade-rules/${uuid}`, data);
  return response.data.data;
};

export const deleteGradeRule = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/academic/grade-rules/${uuid}`);
};

// ===================== LOOKUPS =====================

export const getAcademicHierarchy = async (): Promise<AcademicHierarchy> => {
  const response = await apiClient.get('/api/v1/academic/hierarchy');
  return response.data.data;
};

export const getSubjectsByProgram = async (programUuid: string): Promise<any> => {
  const response = await apiClient.get(`/api/v1/academic/programs/${programUuid}/subjects`);
  return response.data.data;
};

export const getClassesBySession = async (sessionUuid: string): Promise<any> => {
  const response = await apiClient.get(`/api/v1/academic/sessions/${sessionUuid}/classes`);
  return response.data.data;
};
