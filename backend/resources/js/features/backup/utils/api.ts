import { apiClient } from '@/lib/api-client';
import type {
  BackupJob,
  BackupSnapshot,
  RecoveryJob,
  FailoverEvent,
  DRSite,
  BackupSummary,
  PaginatedResponse,
  ApiResponse,
} from '../types';

const API_BASE = '/api/v3';

// Backup Jobs
export const backupApi = {
  list: (params?: Record<string, unknown>) =>
    apiClient.get<PaginatedResponse<BackupJob>>(`${API_BASE}/backups`, { params }),

  get: (id: string) =>
    apiClient.get<ApiResponse<BackupJob>>(`${API_BASE}/backups/${id}`),

  create: (data: Partial<BackupJob>) =>
    apiClient.post<ApiResponse<BackupJob>>(`${API_BASE}/backups`, data),

  update: (id: string, data: Partial<BackupJob>) =>
    apiClient.put<ApiResponse<BackupJob>>(`${API_BASE}/backups/${id}`, data),

  delete: (id: string) =>
    apiClient.delete<ApiResponse<null>>(`${API_BASE}/backups/${id}`),

  start: (id: string) =>
    apiClient.post<ApiResponse<BackupJob>>(`${API_BASE}/backups/${id}/start`),

  complete: (id: string, data: { checksum?: string; size_bytes?: number; file_count?: number }) =>
    apiClient.post<ApiResponse<BackupJob>>(`${API_BASE}/backups/${id}/complete`, data),

  fail: (id: string, error_message: string) =>
    apiClient.post<ApiResponse<BackupJob>>(`${API_BASE}/backups/${id}/fail`, { error_message }),

  cancel: (id: string) =>
    apiClient.post<ApiResponse<BackupJob>>(`${API_BASE}/backups/${id}/cancel`),

  verify: (id: string) =>
    apiClient.post<ApiResponse<BackupJob>>(`${API_BASE}/backups/${id}/verify`),

  summary: (environment?: string) =>
    apiClient.get<ApiResponse<BackupSummary>>(`${API_BASE}/backups/summary`, {
      params: { environment },
    }),

  snapshots: (id: string, params?: Record<string, unknown>) =>
    apiClient.get<PaginatedResponse<BackupSnapshot>>(`${API_BASE}/backups/${id}/snapshots`, { params }),
};

// Recovery Jobs
export const recoveryApi = {
  list: (params?: Record<string, unknown>) =>
    apiClient.get<PaginatedResponse<RecoveryJob>>(`${API_BASE}/recoveries`, { params }),

  get: (id: string) =>
    apiClient.get<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries/${id}`),

  create: (data: Partial<RecoveryJob>) =>
    apiClient.post<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries`, data),

  createFromSnapshot: (data: {
    snapshot_id: string;
    name: string;
    type: string;
    destination_type?: string;
    point_in_time?: string;
    restore_options?: Record<string, unknown>;
    target_database?: string;
    target_path?: string;
  }) =>
    apiClient.post<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries/from-snapshot`, data),

  start: (id: string) =>
    apiClient.post<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries/${id}/start`),

  complete: (id: string, data: { size_restored?: number; files_restored?: number; records_restored?: number }) =>
    apiClient.post<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries/${id}/complete`, data),

  fail: (id: string, error_message: string) =>
    apiClient.post<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries/${id}/fail`, { error_message }),

  verify: (id: string) =>
    apiClient.post<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries/${id}/verify`),

  cancel: (id: string) =>
    apiClient.post<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries/${id}/cancel`),

  addLog: (id: string, message: string, level?: string) =>
    apiClient.post<ApiResponse<RecoveryJob>>(`${API_BASE}/recoveries/${id}/log`, { message, level }),

  delete: (id: string) =>
    apiClient.delete<ApiResponse<null>>(`${API_BASE}/recoveries/${id}`),

  summary: (environment?: string) =>
    apiClient.get<ApiResponse<BackupSummary>>(`${API_BASE}/recoveries/summary`, {
      params: { environment },
    }),
};

// Failover Events
export const failoverApi = {
  list: (params?: Record<string, unknown>) =>
    apiClient.get<PaginatedResponse<FailoverEvent>>(`${API_BASE}/failovers`, { params }),

  get: (id: string) =>
    apiClient.get<ApiResponse<FailoverEvent>>(`${API_BASE}/failovers/${id}`),

  initiate: (data: {
    name: string;
    type: string;
    source_site: string;
    destination_site: string;
    trigger_reason?: string;
    trigger_details?: string;
    initiated_by: string;
    approved_by?: string;
  }) =>
    apiClient.post<ApiResponse<FailoverEvent>>(`${API_BASE}/failovers`, data),

  start: (id: string) =>
    apiClient.post<ApiResponse<FailoverEvent>>(`${API_BASE}/failovers/${id}/start`),

  complete: (id: string) =>
    apiClient.post<ApiResponse<FailoverEvent>>(`${API_BASE}/failovers/${id}/complete`),

  fail: (id: string, error_message: string) =>
    apiClient.post<ApiResponse<FailoverEvent>>(`${API_BASE}/failovers/${id}/fail`, { error_message }),

  rollback: (id: string) =>
    apiClient.post<ApiResponse<FailoverEvent>>(`${API_BASE}/failovers/${id}/rollback`),

  cancel: (id: string) =>
    apiClient.post<ApiResponse<FailoverEvent>>(`${API_BASE}/failovers/${id}/cancel`),

  updateAffected: (id: string, affected_users: number, downtime_seconds: number) =>
    apiClient.patch<ApiResponse<FailoverEvent>>(`${API_BASE}/failovers/${id}/affected`, {
      affected_users,
      downtime_seconds,
    }),

  delete: (id: string) =>
    apiClient.delete<ApiResponse<null>>(`${API_BASE}/failovers/${id}`),

  summary: () =>
    apiClient.get<ApiResponse<unknown>>(`${API_BASE}/failovers/summary`),
};

// DR Sites
export const drSiteApi = {
  list: (params?: Record<string, unknown>) =>
    apiClient.get<ApiResponse<DRSite[]>>(`${API_BASE}/dr-sites`, { params }),

  get: (id: string) =>
    apiClient.get<ApiResponse<DRSite>>(`${API_BASE}/dr-sites/${id}`),

  create: (data: Partial<DRSite>) =>
    apiClient.post<ApiResponse<DRSite>>(`${API_BASE}/dr-sites`, data),

  update: (id: string, data: Partial<DRSite>) =>
    apiClient.put<ApiResponse<DRSite>>(`${API_BASE}/dr-sites/${id}`, data),

  updateHealth: (id: string, status: string) =>
    apiClient.patch<ApiResponse<DRSite>>(`${API_BASE}/dr-sites/${id}/health`, { status }),

  delete: (id: string) =>
    apiClient.delete<ApiResponse<null>>(`${API_BASE}/dr-sites/${id}`),
};

// DR Summary
export const drApi = {
  getSummary: (environment?: string) =>
    apiClient.get<ApiResponse<BackupSummary>>(`${API_BASE}/dr/summary`, {
      params: { environment },
    }),

  getStatus: (environment?: string) =>
    apiClient.get<ApiResponse<{
      status: string;
      backup: unknown;
      recovery: unknown;
      failover: unknown;
      storage: unknown;
      last_check: string;
    }>>(`${API_BASE}/dr/status`, { params: { environment } }),
};
