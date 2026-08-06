import { apiClient } from '../../../api/client';
import type {
  Service,
  Metric,
  Alert,
  Incident,
  HealthCheck,
  HealthCheckResult,
  StatusPage,
  DashboardSummary,
  UptimeData,
  HealthTrend,
} from '../types';

export const observabilityApi = {
  // Dashboard
  async getDashboard(environment = 'production') {
    const response = await apiClient.get('/api/v3/observability/dashboard', {
      params: { environment },
    });
    return response.data;
  },

  async getSummary(environment = 'production') {
    const response = await apiClient.get('/api/v3/observability/summary', {
      params: { environment },
    });
    return response.data;
  },

  async getHealth(environment = 'production') {
    const response = await apiClient.get('/api/v3/observability/health', {
      params: { environment },
    });
    return response.data;
  },

  async getUptime(environment = 'production', days = 30) {
    const response = await apiClient.get('/api/v3/observability/uptime', {
      params: { environment, days },
    });
    return response.data;
  },

  // Services
  async getServices(params?: {
    type?: string;
    environment?: string;
    status?: string;
    is_active?: boolean;
    search?: string;
    per_page?: number;
  }) {
    const response = await apiClient.get('/api/v3/observability/services', { params });
    return response.data;
  },

  async getActiveServices(environment?: string) {
    const response = await apiClient.get('/api/v3/observability/services/active', {
      params: { environment },
    });
    return response.data;
  },

  async getService(id: string) {
    const response = await apiClient.get(`/api/v3/observability/services/${id}`);
    return response.data;
  },

  async createService(data: Partial<Service>) {
    const response = await apiClient.post('/api/v3/observability/services', data);
    return response.data;
  },

  async updateService(id: string, data: Partial<Service>) {
    const response = await apiClient.put(`/api/v3/observability/services/${id}`, data);
    return response.data;
  },

  async toggleService(id: string) {
    const response = await apiClient.post(`/api/v3/observability/services/${id}/toggle`);
    return response.data;
  },

  async deleteService(id: string) {
    const response = await apiClient.delete(`/api/v3/observability/services/${id}`);
    return response.data;
  },

  async getServiceHealth() {
    const response = await apiClient.get('/api/v3/observability/services/health');
    return response.data;
  },

  // Metrics
  async getMetrics(params?: {
    service_id?: string;
    name?: string;
    type?: string;
    environment?: string;
    start_date?: string;
    end_date?: string;
    per_page?: number;
  }) {
    const response = await apiClient.get('/api/v3/observability/metrics', { params });
    return response.data;
  },

  async getLatestMetrics(serviceId: string, name?: string, limit = 100) {
    const response = await apiClient.get(`/api/v3/observability/metrics/latest/${serviceId}`, {
      params: { name, limit },
    });
    return response.data;
  },

  async getMetricTimeSeries(
    serviceId: string,
    name: string,
    startDate: string,
    endDate: string,
    interval = 60
  ) {
    const response = await apiClient.get(`/api/v3/observability/metrics/timeseries/${serviceId}`, {
      params: { name, start_date: startDate, end_date: endDate, interval },
    });
    return response.data;
  },

  async recordMetric(data: Partial<Metric>) {
    const response = await apiClient.post('/api/v3/observability/metrics', data);
    return response.data;
  },

  // Alerts
  async getAlerts(params?: {
    service_id?: string;
    severity?: string;
    status?: string;
    environment?: string;
    start_date?: string;
    end_date?: string;
    per_page?: number;
  }) {
    const response = await apiClient.get('/api/v3/observability/alerts', { params });
    return response.data;
  },

  async getActiveAlerts(environment?: string) {
    const response = await apiClient.get('/api/v3/observability/alerts/active', {
      params: { environment },
    });
    return response.data;
  },

  async getAlert(id: string) {
    const response = await apiClient.get(`/api/v3/observability/alerts/${id}`);
    return response.data;
  },

  async createAlert(data: Partial<Alert>) {
    const response = await apiClient.post('/api/v3/observability/alerts', data);
    return response.data;
  },

  async updateAlert(id: string, data: Partial<Alert>) {
    const response = await apiClient.put(`/api/v3/observability/alerts/${id}`, data);
    return response.data;
  },

  async triggerAlert(id: string, currentValue: number, userId?: string) {
    const response = await apiClient.post(`/api/v3/observability/alerts/${id}/trigger`, {
      current_value: currentValue,
      user_id: userId,
    });
    return response.data;
  },

  async acknowledgeAlert(id: string, userId: string) {
    const response = await apiClient.post(`/api/v3/observability/alerts/${id}/acknowledge`, {
      user_id: userId,
    });
    return response.data;
  },

  async resolveAlert(id: string, userId?: string) {
    const response = await apiClient.post(`/api/v3/observability/alerts/${id}/resolve`, {
      user_id: userId,
    });
    return response.data;
  },

  async silenceAlert(id: string) {
    const response = await apiClient.post(`/api/v3/observability/alerts/${id}/silence`);
    return response.data;
  },

  async deleteAlert(id: string) {
    const response = await apiClient.delete(`/api/v3/observability/alerts/${id}`);
    return response.data;
  },

  async getAlertSummary() {
    const response = await apiClient.get('/api/v3/observability/alerts/summary');
    return response.data;
  },

  // Incidents
  async getIncidents(params?: {
    service_id?: string;
    severity?: string;
    status?: string;
    environment?: string;
    start_date?: string;
    end_date?: string;
    per_page?: number;
  }) {
    const response = await apiClient.get('/api/v3/observability/incidents', { params });
    return response.data;
  },

  async getActiveIncidents(environment?: string) {
    const response = await apiClient.get('/api/v3/observability/incidents/active', {
      params: { environment },
    });
    return response.data;
  },

  async getIncident(id: string) {
    const response = await apiClient.get(`/api/v3/observability/incidents/${id}`);
    return response.data;
  },

  async getIncidentByNumber(incidentNumber: string) {
    const response = await apiClient.get(`/api/v3/observability/incidents/number/${incidentNumber}`);
    return response.data;
  },

  async createIncident(data: Partial<Incident>) {
    const response = await apiClient.post('/api/v3/observability/incidents', data);
    return response.data;
  },

  async createIncidentFromAlert(alertId: string, data?: { title?: string; user_id?: string; description?: string }) {
    const response = await apiClient.post(`/api/v3/observability/incidents/from-alert/${alertId}`, data || {});
    return response.data;
  },

  async updateIncident(id: string, data: Partial<Incident>) {
    const response = await apiClient.put(`/api/v3/observability/incidents/${id}`, data);
    return response.data;
  },

  async acknowledgeIncident(id: string, userId: string) {
    const response = await apiClient.post(`/api/v3/observability/incidents/${id}/acknowledge`, {
      user_id: userId,
    });
    return response.data;
  },

  async assignIncident(id: string, userId: string, assignedByUserId: string) {
    const response = await apiClient.post(`/api/v3/observability/incidents/${id}/assign`, {
      user_id: userId,
      assigned_by_user_id: assignedByUserId,
    });
    return response.data;
  },

  async resolveIncident(id: string, userId: string) {
    const response = await apiClient.post(`/api/v3/observability/incidents/${id}/resolve`, {
      user_id: userId,
    });
    return response.data;
  },

  async closeIncident(id: string, userId: string) {
    const response = await apiClient.post(`/api/v3/observability/incidents/${id}/close`, {
      user_id: userId,
    });
    return response.data;
  },

  async addIncidentTimelineEvent(
    id: string,
    eventType: string,
    title: string,
    userId?: string,
    description?: string
  ) {
    const response = await apiClient.post(`/api/v3/observability/incidents/${id}/timeline`, {
      event_type: eventType,
      title,
      user_id: userId,
      description,
    });
    return response.data;
  },

  async addIncidentPostmortem(id: string, postmortem: string, userId: string) {
    const response = await apiClient.post(`/api/v3/observability/incidents/${id}/postmortem`, {
      postmortem,
      user_id: userId,
    });
    return response.data;
  },

  async deleteIncident(id: string) {
    const response = await apiClient.delete(`/api/v3/observability/incidents/${id}`);
    return response.data;
  },

  async getIncidentSummary() {
    const response = await apiClient.get('/api/v3/observability/incidents/summary');
    return response.data;
  },

  // Health Checks
  async getHealthChecks(params?: {
    service_id?: string;
    type?: string;
    status?: string;
    environment?: string;
    is_active?: boolean;
    per_page?: number;
  }) {
    const response = await apiClient.get('/api/v3/observability/health-checks', { params });
    return response.data;
  },

  async getActiveHealthChecks() {
    const response = await apiClient.get('/api/v3/observability/health-checks/active');
    return response.data;
  },

  async getHealthCheck(id: string) {
    const response = await apiClient.get(`/api/v3/observability/health-checks/${id}`);
    return response.data;
  },

  async createHealthCheck(data: Partial<HealthCheck>) {
    const response = await apiClient.post('/api/v3/observability/health-checks', data);
    return response.data;
  },

  async updateHealthCheck(id: string, data: Partial<HealthCheck>) {
    const response = await apiClient.put(`/api/v3/observability/health-checks/${id}`, data);
    return response.data;
  },

  async toggleHealthCheck(id: string) {
    const response = await apiClient.post(`/api/v3/observability/health-checks/${id}/toggle`);
    return response.data;
  },

  async executeHealthCheck(id: string) {
    const response = await apiClient.post(`/api/v3/observability/health-checks/${id}/execute`);
    return response.data;
  },

  async executeAllHealthChecks() {
    const response = await apiClient.post('/api/v3/observability/health-checks/execute-all');
    return response.data;
  },

  async getHealthCheckResults(id: string) {
    const response = await apiClient.get(`/api/v3/observability/health-checks/${id}/results`);
    return response.data;
  },

  async deleteHealthCheck(id: string) {
    const response = await apiClient.delete(`/api/v3/observability/health-checks/${id}`);
    return response.data;
  },

  async getHealthCheckSummary() {
    const response = await apiClient.get('/api/v3/observability/health-checks/summary');
    return response.data;
  },

  // Status Pages
  async getStatusPages(params?: { is_active?: boolean; per_page?: number }) {
    const response = await apiClient.get('/api/v3/observability/status-pages', { params });
    return response.data;
  },

  async getActiveStatusPages() {
    const response = await apiClient.get('/api/v3/observability/status-pages/active');
    return response.data;
  },

  async getStatusPage(id: string) {
    const response = await apiClient.get(`/api/v3/observability/status-pages/${id}`);
    return response.data;
  },

  async getPublicStatusPage(slug: string) {
    const response = await apiClient.get(`/api/v3/observability/status-pages/public/${slug}`);
    return response.data;
  },

  async createStatusPage(data: Partial<StatusPage>) {
    const response = await apiClient.post('/api/v3/observability/status-pages', data);
    return response.data;
  },

  async updateStatusPage(id: string, data: Partial<StatusPage>) {
    const response = await apiClient.put(`/api/v3/observability/status-pages/${id}`, data);
    return response.data;
  },

  async deleteStatusPage(id: string) {
    const response = await apiClient.delete(`/api/v3/observability/status-pages/${id}`);
    return response.data;
  },

  async refreshStatusPage(id: string) {
    const response = await apiClient.post(`/api/v3/observability/status-pages/${id}/refresh`);
    return response.data;
  },

  async addStatusPageComponent(
    id: string,
    data: { name: string; service_id?: string; description?: string; position?: number; status?: string }
  ) {
    const response = await apiClient.post(`/api/v3/observability/status-pages/${id}/components`, data);
    return response.data;
  },

  async updateStatusPageComponent(componentId: string, data: Partial<StatusPageComponent>) {
    const response = await apiClient.put(`/api/v3/observability/status-pages/components/${componentId}`, data);
    return response.data;
  },

  async updateStatusPageComponentStatus(componentId: string, status: string) {
    const response = await apiClient.put(
      `/api/v3/observability/status-pages/components/${componentId}/status`,
      { status }
    );
    return response.data;
  },

  async deleteStatusPageComponent(componentId: string) {
    const response = await apiClient.delete(`/api/v3/observability/status-pages/components/${componentId}`);
    return response.data;
  },

  async addStatusPageIncident(id: string, incidentId: string, isVisible = true) {
    const response = await apiClient.post(`/api/v3/observability/status-pages/${id}/incidents`, {
      incident_id: incidentId,
      is_visible: isVisible,
    });
    return response.data;
  },

  async removeStatusPageIncident(id: string, incidentId: string) {
    const response = await apiClient.delete(`/api/v3/observability/status-pages/${id}/incidents/${incidentId}`);
    return response.data;
  },
};
