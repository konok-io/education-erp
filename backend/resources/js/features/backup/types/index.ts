export interface BackupJob {
  id: string;
  name: string;
  type: 'full' | 'incremental' | 'differential' | 'snapshot';
  status: 'pending' | 'running' | 'completed' | 'failed' | 'cancelled' | 'paused';
  source_type: string;
  destination_type: string;
  size_bytes: number;
  file_count: number;
  checksum?: string;
  scheduled_at?: string;
  started_at?: string;
  completed_at?: string;
  error_message?: string;
  is_immutable: boolean;
  verified: boolean;
  environment: string;
  created_at: string;
  updated_at: string;
  formatted_size: string;
  duration_seconds: number;
}

export interface BackupSnapshot {
  id: string;
  backup_job_id?: string;
  name: string;
  type: string;
  status: string;
  size_bytes: number;
  location: string;
  storage_provider: string;
  expires_at?: string;
  is_immutable: boolean;
  created_at: string;
  formatted_size: string;
  is_expired: boolean;
  is_immutable_now: boolean;
}

export interface RecoveryJob {
  id: string;
  backup_snapshot_id?: string;
  name: string;
  type: 'full' | 'partial' | 'file' | 'database' | 'point_in_time' | 'table';
  status: 'pending' | 'running' | 'completed' | 'failed' | 'cancelled' | 'verified';
  destination_type: string;
  point_in_time?: string;
  target_database?: string;
  target_path?: string;
  size_restored: number;
  files_restored: number;
  records_restored: number;
  started_at?: string;
  completed_at?: string;
  duration_seconds: number;
  error_message?: string;
  logs?: Array<{ timestamp: string; level: string; message: string }>;
  created_at: string;
  formatted_size: string;
}

export interface FailoverEvent {
  id: string;
  name: string;
  type: 'automatic' | 'manual' | 'planned' | 'emergency';
  status: 'initiated' | 'in_progress' | 'completed' | 'failed' | 'rolled_back' | 'cancelled';
  source_site: string;
  destination_site: string;
  trigger_reason?: string;
  affected_users: number;
  downtime_seconds: number;
  initiated_at?: string;
  completed_at?: string;
  recovery_time_seconds: number;
  error_message?: string;
  created_at: string;
}

export interface DRSite {
  id: string;
  name: string;
  slug: string;
  type: 'primary' | 'secondary' | 'dr' | 'hot' | 'warm' | 'cold';
  status: 'active' | 'standby' | 'failed' | 'maintenance';
  region: string;
  endpoint?: string;
  is_primary: boolean;
  auto_failover_enabled: boolean;
  health_status: 'healthy' | 'unhealthy' | 'unknown';
  recovery_time_target_seconds: number;
  recovery_point_target_seconds: number;
  formatted_rto_target: string;
  formatted_rpo_target: string;
  last_health_check?: string;
}

export interface BackupSummary {
  total_backups: number;
  successful_backups: number;
  failed_backups: number;
  pending_backups: number;
  running_backups: number;
  verified_backups: number;
  total_recoveries: number;
  successful_recoveries: number;
  failed_recoveries: number;
  active_replications: number;
  healthy_replications: number;
  failed_replications: number;
  total_storage_used_bytes: number;
  total_storage_available_bytes: number;
  storage_usage_percentage: number;
  backup_by_type: Record<string, number>;
  backup_by_status: Record<string, number>;
  recent_failovers: Array<{
    id: string;
    name: string;
    type: string;
    status: string;
    initiated_at?: string;
    completed_at?: string;
  }>;
  next_scheduled_backups: Array<{
    id: string;
    name: string;
    type: string;
    scheduled_at?: string;
  }>;
}

export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface PaginatedResponse<T> {
  success: boolean;
  data: T[];
  meta: PaginationMeta;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}
