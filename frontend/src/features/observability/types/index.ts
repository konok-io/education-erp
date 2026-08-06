export type MetricType = 'counter' | 'gauge' | 'histogram' | 'summary';
export type ServiceType = 'api' | 'frontend' | 'database' | 'queue' | 'cache' | 'storage' | 'network' | 'electron' | 'android' | 'ios';
export type ServiceStatus = 'healthy' | 'degraded' | 'down' | 'unknown' | 'maintenance';
export type LogLevel = 'debug' | 'info' | 'notice' | 'warning' | 'error' | 'critical' | 'alert' | 'emergency';
export type AlertSeverity = 'critical' | 'high' | 'medium' | 'low' | 'info';
export type AlertStatus = 'active' | 'acknowledged' | 'resolved' | 'silenced';
export type IncidentSeverity = 'critical' | 'high' | 'medium' | 'low';
export type IncidentStatus = 'triggered' | 'acknowledged' | 'investigating' | 'resolved' | 'closed';
export type HealthCheckType = 'api' | 'database' | 'queue' | 'cache' | 'storage' | 'smtp' | 'sms' | 'payment' | 'custom';
export type HealthCheckStatus = 'healthy' | 'degraded' | 'unhealthy' | 'unknown';

export interface Service {
  id: string;
  name: string;
  type: ServiceType;
  environment: string;
  status: ServiceStatus;
  metadata?: Record<string, any>;
  tags?: string[];
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Metric {
  id: string;
  service_id: string | null;
  name: string;
  type: MetricType;
  value: number;
  unit: string | null;
  timestamp: string;
  labels?: Record<string, string>;
  tags?: string[];
  environment: string;
}

export interface LogEntry {
  id: string;
  service_id: string | null;
  level: LogLevel;
  source: string;
  message: string;
  context?: Record<string, any>;
  extra?: Record<string, any>;
  environment: string;
  host: string | null;
  trace_id: string | null;
  span_id: string | null;
  logged_at: string;
}

export interface Alert {
  id: string;
  service_id: string | null;
  name: string;
  description: string | null;
  severity: AlertSeverity;
  status: AlertStatus;
  metric_name: string | null;
  condition: string | null;
  threshold: number | null;
  current_value: number | null;
  environment: string;
  metadata?: Record<string, any>;
  labels?: Record<string, string>;
  triggered_at: string | null;
  resolved_at: string | null;
  triggered_by_user_id: string | null;
  acknowledged_by_user_id: string | null;
  created_at: string;
  updated_at: string;
}

export interface IncidentTimelineEvent {
  id: string;
  incident_id: string;
  event_type: string;
  title: string;
  description: string | null;
  user_id: string | null;
  metadata?: Record<string, any>;
  occurred_at: string;
}

export interface Incident {
  id: string;
  incident_number: string;
  title: string;
  description: string | null;
  severity: IncidentSeverity;
  status: IncidentStatus;
  service_id: string | null;
  alert_id: string | null;
  environment: string;
  affected_components?: string[];
  impact?: Record<string, any>;
  started_at: string;
  acknowledged_at: string | null;
  resolved_at: string | null;
  closed_at: string | null;
  assigned_to_user_id: string | null;
  created_by_user_id: string | null;
  postmortem: string | null;
  timeline?: IncidentTimelineEvent[];
  metadata?: Record<string, any>;
  created_at: string;
  updated_at: string;
}

export interface HealthCheck {
  id: string;
  service_id: string | null;
  name: string;
  type: HealthCheckType;
  endpoint: string | null;
  method: string;
  status: HealthCheckStatus;
  check_interval_seconds: number;
  timeout_seconds: number;
  retry_count: number;
  headers?: Record<string, string>;
  expected_response?: Record<string, any>;
  is_active: boolean;
  environment: string;
  metadata?: Record<string, any>;
  last_check_at: string | null;
  last_status: string | null;
  last_error: string | null;
  last_response_time_ms: number | null;
  created_at: string;
  updated_at: string;
}

export interface HealthCheckResult {
  id: string;
  health_check_id: string;
  status: HealthCheckStatus;
  response_time_ms: number;
  http_status_code: number | null;
  error_message: string | null;
  response_body?: Record<string, any>;
  checked_at: string;
}

export interface StatusPageComponent {
  id: string;
  status_page_id: string;
  service_id: string | null;
  name: string;
  description: string | null;
  position: string | null;
  status: string;
  show_history: boolean;
}

export interface StatusPage {
  id: string;
  name: string;
  slug: string;
  title: string;
  description: string | null;
  logo_url: string | null;
  timezone: string;
  status: string;
  header_settings?: Record<string, any>;
  footer_settings?: Record<string, any>;
  custom_css?: Record<string, any>;
  show_incident_history: boolean;
  is_active: boolean;
  components?: StatusPageComponent[];
  active_incidents?: Incident[];
  created_at: string;
  updated_at: string;
}

export interface DashboardSummary {
  total_services: number;
  healthy_services: number;
  degraded_services: number;
  down_services: number;
  active_alerts: number;
  active_incidents: number;
  critical_incidents: number;
  average_availability: number;
  alert_severity_breakdown: Record<AlertSeverity, number>;
  incident_severity_breakdown: Record<IncidentSeverity, number>;
  recent_incidents: Array<{
    id: string;
    incident_number: string;
    title: string;
    severity: IncidentSeverity;
    status: IncidentStatus;
    started_at: string;
  }>;
  top_alerts: Array<{
    id: string;
    name: string;
    severity: AlertSeverity;
    status: AlertStatus;
    triggered_at: string | null;
    service_id: string | null;
  }>;
  slo_summary: Array<{
    id: string;
    name: string;
    type: string;
    target: number;
    current: number | null;
    is_breached: boolean;
  }>;
}

export interface UptimeData {
  id: string;
  name: string;
  type: ServiceType;
  status: ServiceStatus;
  uptime_percentage: number;
}

export interface HealthTrend {
  date: string;
  alerts: number;
  incidents: number;
}
