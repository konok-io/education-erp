import { useState, useCallback } from 'react';
import * as academicApi from '../services/academicApi';
import type { AcademicFilters, AcademicHierarchy } from '../types';

interface UseAcademicOptions {
  perPage?: number;
}

export function useAcademic(options: UseAcademicOptions = {}) {
  const [data, setData] = useState<any[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [pagination, setPagination] = useState({
    currentPage: 1,
    lastPage: 1,
    perPage: options.perPage || 20,
    total: 0,
  });
  const [filters, setFilters] = useState<AcademicFilters>({
    per_page: options.perPage || 20,
  });

  const fetchData = useCallback(async (
    apiFn: (filters?: AcademicFilters) => Promise<any>,
    newFilters?: AcademicFilters
  ) => {
    setIsLoading(true);
    setError(null);

    try {
      const appliedFilters = { ...filters, ...newFilters };
      const response = await apiFn(appliedFilters);

      setData(response.data);
      setPagination({
        currentPage: response.meta.current_page,
        lastPage: response.meta.last_page,
        perPage: response.meta.per_page,
        total: response.meta.total,
      });

      if (newFilters) {
        setFilters(appliedFilters);
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to fetch data');
    } finally {
      setIsLoading(false);
    }
  }, [filters]);

  const create = useCallback(async (
    apiFn: (data: any) => Promise<any>,
    data: any
  ) => {
    setIsLoading(true);
    setError(null);

    try {
      const result = await apiFn(data);
      return result;
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to create');
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, []);

  const update = useCallback(async (
    apiFn: (uuid: string, data: any) => Promise<any>,
    uuid: string,
    data: any
  ) => {
    setIsLoading(true);
    setError(null);

    try {
      const result = await apiFn(uuid, data);
      return result;
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to update');
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, []);

  const remove = useCallback(async (
    apiFn: (uuid: string) => Promise<void>,
    uuid: string
  ) => {
    setIsLoading(true);
    setError(null);

    try {
      await apiFn(uuid);
      setData(prev => prev.filter((item: any) => item.id !== uuid));
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to delete');
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, []);

  return {
    data,
    isLoading,
    error,
    pagination,
    filters,
    fetchData,
    create,
    update,
    remove,
    setFilters,
  };
}

// Academic Levels
export function useAcademicLevels(options?: UseAcademicOptions) {
  const hook = useAcademic(options);

  const fetchAcademicLevels = useCallback((filters?: AcademicFilters) => {
    return hook.fetchData(academicApi.getAcademicLevels, filters);
  }, [hook]);

  const createAcademicLevel = useCallback((data: any) => {
    return hook.create(academicApi.createAcademicLevel, data);
  }, [hook]);

  const updateAcademicLevel = useCallback((uuid: string, data: any) => {
    return hook.update(academicApi.updateAcademicLevel, uuid, data);
  }, [hook]);

  const deleteAcademicLevel = useCallback((uuid: string) => {
    return hook.remove(academicApi.deleteAcademicLevel, uuid);
  }, [hook]);

  return {
    ...hook,
    fetchAcademicLevels,
    createAcademicLevel,
    updateAcademicLevel,
    deleteAcademicLevel,
  };
}

// Sessions
export function useSessions(options?: UseAcademicOptions) {
  const hook = useAcademic(options);

  const fetchSessions = useCallback((filters?: AcademicFilters) => {
    return hook.fetchData(academicApi.getSessions, filters);
  }, [hook]);

  const createSession = useCallback((data: any) => {
    return hook.create(academicApi.createSession, data);
  }, [hook]);

  const updateSession = useCallback((uuid: string, data: any) => {
    return hook.update(academicApi.updateSession, uuid, data);
  }, [hook]);

  const deleteSession = useCallback((uuid: string) => {
    return hook.remove(academicApi.deleteSession, uuid);
  }, [hook]);

  const setCurrentSession = useCallback(async (uuid: string) => {
    await academicApi.setCurrentSession(uuid);
    hook.fetchData(academicApi.getSessions);
  }, [hook]);

  return {
    ...hook,
    fetchSessions,
    createSession,
    updateSession,
    deleteSession,
    setCurrentSession,
  };
}

// Programs
export function usePrograms(options?: UseAcademicOptions) {
  const hook = useAcademic(options);

  const fetchPrograms = useCallback((filters?: AcademicFilters) => {
    return hook.fetchData(academicApi.getPrograms, filters);
  }, [hook]);

  const createProgram = useCallback((data: any) => {
    return hook.create(academicApi.createProgram, data);
  }, [hook]);

  const updateProgram = useCallback((uuid: string, data: any) => {
    return hook.update(academicApi.updateProgram, uuid, data);
  }, [hook]);

  const deleteProgram = useCallback((uuid: string) => {
    return hook.remove(academicApi.deleteProgram, uuid);
  }, [hook]);

  return {
    ...hook,
    fetchPrograms,
    createProgram,
    updateProgram,
    deleteProgram,
  };
}

// Subjects
export function useSubjects(options?: UseAcademicOptions) {
  const hook = useAcademic(options);

  const fetchSubjects = useCallback((filters?: AcademicFilters) => {
    return hook.fetchData(academicApi.getSubjects, filters);
  }, [hook]);

  const createSubject = useCallback((data: any) => {
    return hook.create(academicApi.createSubject, data);
  }, [hook]);

  const updateSubject = useCallback((uuid: string, data: any) => {
    return hook.update(academicApi.updateSubject, uuid, data);
  }, [hook]);

  const deleteSubject = useCallback((uuid: string) => {
    return hook.remove(academicApi.deleteSubject, uuid);
  }, [hook]);

  return {
    ...hook,
    fetchSubjects,
    createSubject,
    updateSubject,
    deleteSubject,
  };
}

// Academic Hierarchy
export function useAcademicHierarchy() {
  const [hierarchy, setHierarchy] = useState<AcademicHierarchy | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchHierarchy = useCallback(async () => {
    setIsLoading(true);
    setError(null);

    try {
      const data = await academicApi.getAcademicHierarchy();
      setHierarchy(data);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to fetch hierarchy');
    } finally {
      setIsLoading(false);
    }
  }, []);

  return {
    hierarchy,
    isLoading,
    error,
    fetchHierarchy,
  };
}
