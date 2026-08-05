import { useState, useCallback } from 'react';
import * as userApi from '../services/userApi';
import type { User, UserFilters, CreateUserData, UpdateUserData } from '../services/userApi';

interface UseUsersOptions {
  perPage?: number;
}

interface UseUsersReturn {
  users: User[];
  isLoading: boolean;
  error: string | null;
  pagination: {
    currentPage: number;
    lastPage: number;
    perPage: number;
    total: number;
  };
  filters: UserFilters;
  fetchUsers: (filters?: UserFilters) => Promise<void>;
  createUser: (data: CreateUserData) => Promise<User>;
  updateUser: (uuid: string, data: UpdateUserData) => Promise<User>;
  deleteUser: (uuid: string) => Promise<void>;
  updateUserStatus: (uuid: string, status: string) => Promise<void>;
  bulkUpdateStatus: (uuids: string[], status: string) => Promise<void>;
  searchUsers: (query: string) => Promise<User[]>;
  setFilters: (filters: UserFilters) => void;
}

export function useUsers(options: UseUsersOptions = {}): UseUsersReturn {
  const [users, setUsers] = useState<User[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [pagination, setPagination] = useState({
    currentPage: 1,
    lastPage: 1,
    perPage: options.perPage || 20,
    total: 0,
  });
  const [filters, setFilters] = useState<UserFilters>({
    per_page: options.perPage || 20,
    sort_by: 'created_at',
    sort_order: 'desc',
  });

  const fetchUsers = useCallback(async (newFilters?: UserFilters) => {
    setIsLoading(true);
    setError(null);

    try {
      const appliedFilters = { ...filters, ...newFilters };
      const response = await userApi.getUsers(appliedFilters);

      setUsers(response.data);
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
      setError(err.response?.data?.message || 'Failed to fetch users');
    } finally {
      setIsLoading(false);
    }
  }, [filters]);

  const createUser = useCallback(async (data: CreateUserData): Promise<User> => {
    setIsLoading(true);
    setError(null);

    try {
      const user = await userApi.createUser(data);
      setUsers(prev => [user, ...prev]);
      return user;
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to create user');
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, []);

  const updateUser = useCallback(async (uuid: string, data: UpdateUserData): Promise<User> => {
    setIsLoading(true);
    setError(null);

    try {
      const user = await userApi.updateUser(uuid, data);
      setUsers(prev => prev.map(u => u.id === uuid ? user : u));
      return user;
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to update user');
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, []);

  const deleteUser = useCallback(async (uuid: string): Promise<void> => {
    setIsLoading(true);
    setError(null);

    try {
      await userApi.deleteUser(uuid);
      setUsers(prev => prev.filter(u => u.id !== uuid));
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to delete user');
      throw err;
    } finally {
      setIsLoading(false);
    }
  }, []);

  const updateUserStatus = useCallback(async (uuid: string, status: string): Promise<void> => {
    try {
      await userApi.updateUserStatus(uuid, status);
      setUsers(prev => prev.map(u => u.id === uuid ? { ...u, status: status as User['status'] } : u));
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to update status');
      throw err;
    }
  }, []);

  const bulkUpdateStatus = useCallback(async (uuids: string[], status: string): Promise<void> => {
    try {
      await userApi.bulkUpdateStatus(uuids, status);
      setUsers(prev => prev.map(u => 
        uuids.includes(u.id) ? { ...u, status: status as User['status'] } : u
      ));
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to bulk update status');
      throw err;
    }
  }, []);

  const searchUsers = useCallback(async (query: string): Promise<User[]> => {
    try {
      const response = await userApi.searchUsers(query);
      return response.data;
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to search users');
      return [];
    }
  }, []);

  return {
    users,
    isLoading,
    error,
    pagination,
    filters,
    fetchUsers,
    createUser,
    updateUser,
    deleteUser,
    updateUserStatus,
    bulkUpdateStatus,
    searchUsers,
    setFilters,
  };
}
