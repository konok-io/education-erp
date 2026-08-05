/**
 * User Management API
 */

import { apiClient } from '@/lib/api-client';
import type { PaginatedResponse } from '@/types';

export interface User {
  id: string;
  name: string;
  email: string;
  mobile: string | null;
  avatar: string | null;
  role: {
    id: string;
    name: string;
    display_name: string;
  } | null;
  campus: {
    id: string;
    name: string;
    code: string;
  } | null;
  status: 'active' | 'inactive' | 'blocked' | 'suspended' | 'pending';
  email_verified_at: string | null;
  last_login_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface UserFilters {
  search?: string;
  role?: string;
  status?: string;
  campus_id?: string;
  sort_by?: string;
  sort_order?: 'asc' | 'desc';
  per_page?: number;
}

export interface CreateUserData {
  name: string;
  email: string;
  mobile?: string;
  password: string;
  password_confirmation: string;
  role_id: number;
  campus_id?: number;
  status?: string;
  avatar?: File;
}

export interface UpdateUserData {
  name?: string;
  email?: string;
  mobile?: string;
  role_id?: number;
  campus_id?: number;
  status?: string;
}

/**
 * Get users list
 */
export const getUsers = async (filters?: UserFilters): Promise<PaginatedResponse<User>> => {
  const response = await apiClient.get('/api/v1/users', { params: filters });
  return response.data;
};

/**
 * Get user by ID
 */
export const getUser = async (uuid: string): Promise<User> => {
  const response = await apiClient.get(`/api/v1/users/${uuid}`);
  return response.data.data;
};

/**
 * Create user
 */
export const createUser = async (data: CreateUserData): Promise<User> => {
  const formData = new FormData();
  
  Object.entries(data).forEach(([key, value]) => {
    if (value !== undefined && value !== null) {
      if (key === 'avatar' && value instanceof File) {
        formData.append(key, value);
      } else {
        formData.append(key, String(value));
      }
    }
  });

  const response = await apiClient.post('/api/v1/users', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data;
};

/**
 * Update user
 */
export const updateUser = async (uuid: string, data: UpdateUserData): Promise<User> => {
  const response = await apiClient.put(`/api/v1/users/${uuid}`, data);
  return response.data.data;
};

/**
 * Delete user
 */
export const deleteUser = async (uuid: string): Promise<void> => {
  await apiClient.delete(`/api/v1/users/${uuid}`);
};

/**
 * Update user avatar
 */
export const updateUserAvatar = async (uuid: string, avatar: File): Promise<string> => {
  const formData = new FormData();
  formData.append('avatar', avatar);

  const response = await apiClient.post(`/api/v1/users/${uuid}/avatar`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data.avatar;
};

/**
 * Change user password (admin)
 */
export const changeUserPassword = async (uuid: string, password: string, password_confirmation: string): Promise<void> => {
  await apiClient.post(`/api/v1/users/${uuid}/password`, {
    password,
    password_confirmation,
  });
};

/**
 * Update user status
 */
export const updateUserStatus = async (uuid: string, status: string): Promise<void> => {
  await apiClient.post(`/api/v1/users/${uuid}/status`, { status });
};

/**
 * Assign role to user
 */
export const assignUserRole = async (uuid: string, roleId: number): Promise<void> => {
  await apiClient.post(`/api/v1/users/${uuid}/role`, { role_id: roleId });
};

/**
 * Search users
 */
export const searchUsers = async (query: string, perPage = 20): Promise<PaginatedResponse<User>> => {
  const response = await apiClient.get('/api/v1/users/search', {
    params: { q: query, per_page: perPage },
  });
  return response.data;
};

/**
 * Bulk update status
 */
export const bulkUpdateStatus = async (uuids: string[], status: string): Promise<void> => {
  await apiClient.put('/api/v1/users/bulk-status', { user_ids: uuids, status });
};

/**
 * Export users
 */
export const exportUsers = async (format: 'excel' | 'csv' | 'pdf', filters?: UserFilters): Promise<string> => {
  const response = await apiClient.get('/api/v1/users/export', {
    params: { format, ...filters },
  });
  return response.data.data.url;
};

/**
 * Import users
 */
export const importUsers = async (file: File): Promise<{
  total: number;
  success: number;
  failed: number;
  errors: string[];
}> => {
  const formData = new FormData();
  formData.append('file', file);

  const response = await apiClient.post('/api/v1/users/import', formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
  });
  return response.data.data;
};

/**
 * Get user activities
 */
export const getUserActivities = async (uuid: string): Promise<any[]> => {
  const response = await apiClient.get(`/api/v1/users/${uuid}/activities`);
  return response.data.data;
};

/**
 * Get user login history
 */
export const getUserLoginHistory = async (uuid: string, page = 1): Promise<PaginatedResponse<any>> => {
  const response = await apiClient.get(`/api/v1/users/${uuid}/login-history`, {
    params: { page },
  });
  return response.data;
};
