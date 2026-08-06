import { apiClient } from '@/lib/api-client';
import type {
  AuthResponse,
  LoginCredentials,
  MFAVerification,
  Session,
  MFAFactor,
  TOTPSetupResponse,
  PaginatedResponse,
  ApiResponse,
} from '../types';

const API_BASE = '/api/v3';

// Authentication
export const authApi = {
  login: (credentials: LoginCredentials) =>
    apiClient.post<AuthResponse>(`${API_BASE}/auth/login`, credentials),

  loginWithMFA: (data: MFAVerification) =>
    apiClient.post<AuthResponse>(`${API_BASE}/auth/login/mfa`, data),

  logout: () =>
    apiClient.post<ApiResponse<null>>(`${API_BASE}/auth/logout`),

  logoutAll: () =>
    apiClient.post<ApiResponse<null>>(`${API_BASE}/auth/logout-all`),

  refresh: (refreshToken: string) =>
    apiClient.post<ApiResponse<{
      access_token: string;
      refresh_token: string;
      token_type: string;
      expires_in: number;
    }>>(`${API_BASE}/auth/refresh`, { refresh_token: refreshToken }),

  me: () =>
    apiClient.get<ApiResponse<{
      id: string;
      name: string;
      email: string;
      role: string;
    }>>(`${API_BASE}/auth/me`),
};

// Sessions
export const sessionApi = {
  list: (params?: Record<string, unknown>) =>
    apiClient.get<PaginatedResponse<Session>>(`${API_BASE}/sessions`, { params }),

  get: (id: string) =>
    apiClient.get<ApiResponse<Session>>(`${API_BASE}/sessions/${id}`),

  revoke: (id: string) =>
    apiClient.delete<ApiResponse<null>>(`${API_BASE}/sessions/${id}`),

  revokeAll: () =>
    apiClient.delete<ApiResponse<{ count: number }>>(`${API_BASE}/sessions`),
};

// MFA
export const mfaApi = {
  list: () =>
    apiClient.get<ApiResponse<MFAFactor[]>>(`${API_BASE}/mfa`),

  setupTOTP: (name: string) =>
    apiClient.post<TOTPSetupResponse>(`${API_BASE}/mfa/totp/setup`, { name }),

  setupSMS: (name: string, phoneNumber: string) =>
    apiClient.post<ApiResponse<{ factor_id: string; phone_number: string }>>(`${API_BASE}/mfa/sms/setup`, {
      name,
      phone_number: phoneNumber,
    }),

  verifySetup: (factorId: string, code: string) =>
    apiClient.post<ApiResponse<null>>(`${API_BASE}/mfa/verify-setup`, {
      factor_id: factorId,
      code,
    }),

  verify: (factorId: string, code: string) =>
    apiClient.post<ApiResponse<null>>(`${API_BASE}/mfa/verify`, {
      factor_id: factorId,
      code,
    }),

  generateBackupCodes: () =>
    apiClient.post<ApiResponse<{ codes: string[] }>>(`${API_BASE}/mfa/backup-codes`),

  delete: (id: string) =>
    apiClient.delete<ApiResponse<null>>(`${API_BASE}/mfa/${id}`),
};

// Identity
export const identityApi = {
  getUser: () =>
    apiClient.get<ApiResponse<{
      id: string;
      name: string;
      email: string;
      role: string;
    }>>(`${API_BASE}/identity/users`),

  getSessions: () =>
    apiClient.get<ApiResponse<Session[]>>(`${API_BASE}/identity/sessions`),
};
