/**
 * Authentication API
 */

import { apiClient } from '@/lib/api-client';
import type { User } from '@/types';

export interface LoginRequest {
  email: string;
  password: string;
  remember_me?: boolean;
  device_name?: string;
}

export interface AuthResponse {
  user: User;
  token: string;
  token_type: string;
  expires_at: string;
}

export interface LoginHistory {
  id: string;
  device_name: string;
  browser: string;
  platform: string;
  ip_address: string;
  login_at: string;
  logout_at: string | null;
  status: string;
}

export interface ActiveSession {
  id: number;
  name: string;
  created_at: string;
  last_used_at: string;
  expires_at: string;
}

/**
 * Login user
 */
export const login = async (data: LoginRequest): Promise<AuthResponse> => {
  const response = await apiClient.post<AuthResponse>('/api/v1/auth/login', data);
  return response.data;
};

/**
 * Logout user
 */
export const logout = async (): Promise<void> => {
  await apiClient.post('/api/v1/auth/logout');
};

/**
 * Get current user
 */
export const getCurrentUser = async (): Promise<User> => {
  const response = await apiClient.get<User>('/api/v1/auth/me');
  return response.data;
};

/**
 * Refresh token
 */
export const refreshToken = async (): Promise<{ token: string; token_type: string; expires_at: string }> => {
  const response = await apiClient.post('/api/v1/auth/refresh');
  return response.data;
};

/**
 * Change password
 */
export const changePassword = async (data: {
  current_password: string;
  password: string;
  password_confirmation: string;
}): Promise<void> => {
  await apiClient.post('/api/v1/auth/change-password', data);
};

/**
 * Forgot password
 */
export const forgotPassword = async (email: string): Promise<void> => {
  await apiClient.post('/api/v1/auth/forgot-password', { email });
};

/**
 * Reset password
 */
export const resetPassword = async (data: {
  email: string;
  token: string;
  password: string;
  password_confirmation: string;
}): Promise<void> => {
  await apiClient.post('/api/v1/auth/reset-password', data);
};

/**
 * Get login history
 */
export const getLoginHistory = async (page = 1): Promise<{ data: LoginHistory[]; meta: any }> => {
  const response = await apiClient.get('/api/v1/auth/history', { params: { page } });
  return response.data;
};

/**
 * Get active sessions
 */
export const getActiveSessions = async (): Promise<ActiveSession[]> => {
  const response = await apiClient.get('/api/v1/auth/sessions');
  return response.data.data;
};

/**
 * Logout from specific session
 */
export const logoutSession = async (sessionId: string): Promise<void> => {
  await apiClient.delete(`/api/v1/auth/sessions/${sessionId}`);
};

/**
 * Logout from all devices
 */
export const logoutAllDevices = async (): Promise<void> => {
  await apiClient.delete('/api/v1/auth/sessions');
};
