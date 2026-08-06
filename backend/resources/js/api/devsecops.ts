import { apiClient } from '../client';

export interface Environment {
  id: string;
  name: string;
  slug: string;
  type: 'development' | 'qa' | 'uat' | 'staging' | 'production';
  cluster?: string;
  namespace?: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Pipeline {
  id: string;
  name: string;
  slug: string;
  type: 'ci' | 'cd' | 'security' | 'release';
  provider: 'github' | 'gitlab' | 'jenkins' | 'azure';
  repository?: string;
  branch: string;
  status: 'inactive' | 'active' | 'paused' | 'archived';
  is_active: boolean;
  environment_id?: string;
  created_at: string;
  updated_at: string;
}

export interface PipelineRun {
  id: string;
  pipeline_id: string;
  run_number: string;
  status: 'pending' | 'running' | 'success' | 'failed' | 'cancelled' | 'blocked';
  trigger: 'push' | 'pull_request' | 'manual' | 'scheduled' | 'api';
  branch?: string;
  commit_sha?: string;
  author?: string;
  started_at?: string;
  completed_at?: string;
  duration?: number;
}

export interface Deployment {
  id: string;
  environment_id: string;
  version?: string;
  strategy: 'rolling' | 'blue_green' | 'canary' | 'ab' | 'shadow' | 'recreate';
  status: 'pending' | 'deploying' | 'deployed' | 'failed' | 'rolled_back';
  namespace?: string;
  started_at?: string;
  completed_at?: string;
  duration?: number;
  deployed_by?: string;
}

export interface Artifact {
  id: string;
  name: string;
  version?: string;
  type: 'docker' | 'npm' | 'composer' | 'android_apk' | 'android_aab' | 'electron' | 'archive';
  registry: 'dockerhub' | 'ghcr' | 'nexus' | 'artifactory' | 's3';
  digest?: string;
  signed: boolean;
  scan_status: 'pending' | 'running' | 'completed' | 'failed';
  vulnerability_count: number;
  critical_vulnerabilities: number;
  created_at: string;
}

export interface Release {
  id: string;
  name: string;
  version: string;
  type: 'major' | 'minor' | 'patch' | 'rc' | 'lts' | 'hotfix';
  status: 'draft' | 'rc' | 'stable' | 'lts' | 'deprecated' | 'archived';
  channel: 'stable' | 'beta' | 'alpha' | 'edge';
  is_prerelease: boolean;
  is_draft: boolean;
  released_at?: string;
  created_at: string;
}

export interface SecurityScan {
  id: string;
  type: 'sast' | 'dast' | 'sca' | 'secret' | 'container' | 'iac' | 'sbom' | 'license';
  tool: string;
  status: 'pending' | 'running' | 'completed' | 'failed';
  severity: 'none' | 'info' | 'low' | 'medium' | 'high' | 'critical';
  vulnerability_count: number;
  critical_count: number;
  started_at?: string;
  completed_at?: string;
}

export interface ActivityLog {
  id: string;
  type: 'pipeline' | 'deployment' | 'release' | 'security_scan' | 'rollback' | 'artifact' | 'gitops';
  action: string;
  status: 'success' | 'failed' | 'warning';
  resource_type?: string;
  resource_id?: string;
  resource_name?: string;
  message?: string;
  created_at: string;
}

export interface DevSecOpsDashboard {
  environments: { total: number; active: number };
  pipelines: { total: number; active: number };
  deployments: { active: number; stats: any };
  artifacts: { total: number; vulnerable: number };
  releases: { total: number; published: number; lts: number };
  security: any;
  activity: any;
}

const BASE_URL = '/api/v3/devsecops';

export const devsecopsApi = {
  // Dashboard
  getDashboard: () => 
    apiClient.get<DevSecOpsDashboard>(`${BASE_URL}/dashboard`),
  
  getSummary: () => 
    apiClient.get(`${BASE_URL}/summary`),
  
  getHealth: () => 
    apiClient.get(`${BASE_URL}/health`),

  // Environments
  getEnvironments: (params?: Record<string, any>) => 
    apiClient.get<{ data: Environment[]; meta: any }>(`${BASE_URL}/environments`, { params }),
  
  getActiveEnvironments: () => 
    apiClient.get<Environment[]>(`${BASE_URL}/environments/active`),
  
  getEnvironment: (id: string) => 
    apiClient.get<Environment>(`${BASE_URL}/environments/${id}`),
  
  createEnvironment: (data: Partial<Environment>) => 
    apiClient.post<Environment>(`${BASE_URL}/environments`, data),
  
  updateEnvironment: (id: string, data: Partial<Environment>) => 
    apiClient.put<Environment>(`${BASE_URL}/environments/${id}`, data),
  
  deleteEnvironment: (id: string) => 
    apiClient.delete(`${BASE_URL}/environments/${id}`),

  // Pipelines
  getPipelines: (params?: Record<string, any>) => 
    apiClient.get<{ data: Pipeline[]; meta: any }>(`${BASE_URL}/pipelines`, { params }),
  
  getActivePipelines: () => 
    apiClient.get<Pipeline[]>(`${BASE_URL}/pipelines/active`),
  
  getPipeline: (id: string) => 
    apiClient.get<Pipeline>(`${BASE_URL}/pipelines/${id}`),
  
  createPipeline: (data: Partial<Pipeline>) => 
    apiClient.post<Pipeline>(`${BASE_URL}/pipelines`, data),
  
  updatePipeline: (id: string, data: Partial<Pipeline>) => 
    apiClient.put<Pipeline>(`${BASE_URL}/pipelines/${id}`, data),
  
  deletePipeline: (id: string) => 
    apiClient.delete(`${BASE_URL}/pipelines/${id}`),
  
  triggerPipeline: (id: string, params?: Record<string, any>) => 
    apiClient.post(`${BASE_URL}/pipelines/${id}/trigger`, params),
  
  getPipelineRuns: (id: string, params?: Record<string, any>) => 
    apiClient.get<{ data: PipelineRun[]; meta: any }>(`${BASE_URL}/pipelines/${id}/runs`, { params }),

  // Deployments
  getDeployments: (params?: Record<string, any>) => 
    apiClient.get<{ data: Deployment[]; meta: any }>(`${BASE_URL}/deployments`, { params }),
  
  getActiveDeployments: () => 
    apiClient.get<Deployment[]>(`${BASE_URL}/deployments/active`),
  
  getDeployment: (id: string) => 
    apiClient.get<Deployment>(`${BASE_URL}/deployments/${id}`),
  
  createDeployment: (data: Partial<Deployment>) => 
    apiClient.post<Deployment>(`${BASE_URL}/deployments`, data),
  
  rollbackDeployment: (id: string) => 
    apiClient.post(`${BASE_URL}/deployments/${id}/rollback`),

  // Artifacts
  getArtifacts: (params?: Record<string, any>) => 
    apiClient.get<{ data: Artifact[]; meta: any }>(`${BASE_URL}/artifacts`, { params }),
  
  getArtifact: (id: string) => 
    apiClient.get<Artifact>(`${BASE_URL}/artifacts/${id}`),
  
  getVulnerableArtifacts: () => 
    apiClient.get<Artifact[]>(`${BASE_URL}/artifacts/vulnerable`),

  // Releases
  getReleases: (params?: Record<string, any>) => 
    apiClient.get<{ data: Release[]; meta: any }>(`${BASE_URL}/releases`, { params }),
  
  getPublishedReleases: () => 
    apiClient.get<Release[]>(`${BASE_URL}/releases/published`),
  
  getLtsReleases: () => 
    apiClient.get<Release[]>(`${BASE_URL}/releases/lts`),
  
  getLatestRelease: () => 
    apiClient.get<Release>(`${BASE_URL}/releases/latest`),
  
  getRelease: (id: string) => 
    apiClient.get<Release>(`${BASE_URL}/releases/${id}`),
  
  createRelease: (data: Partial<Release>) => 
    apiClient.post<Release>(`${BASE_URL}/releases`, data),
  
  publishRelease: (id: string) => 
    apiClient.post(`${BASE_URL}/releases/${id}/publish`),

  // Security Scans
  getSecurityScans: (params?: Record<string, any>) => 
    apiClient.get<{ data: SecurityScan[]; meta: any }>(`${BASE_URL}/security/scans`, { params }),
  
  getSecurityScan: (id: string) => 
    apiClient.get<SecurityScan>(`${BASE_URL}/security/scans/${id}`),
  
  getRecentVulnerabilities: (params?: { days?: number; limit?: number }) => 
    apiClient.get<SecurityScan[]>(`${BASE_URL}/security/scans/vulnerabilities`, { params }),
  
  getSecurityStats: () => 
    apiClient.get(`${BASE_URL}/security/stats`),

  // Activity Logs
  getActivityLogs: (params?: Record<string, any>) => 
    apiClient.get<{ data: ActivityLog[]; meta: any }>(`${BASE_URL}/logs`, { params }),
  
  getRecentActivity: (params?: { days?: number }) => 
    apiClient.get<{ data: ActivityLog[]; meta: any }>(`${BASE_URL}/logs/recent`, { params }),
  
  getActivityStats: () => 
    apiClient.get(`${BASE_URL}/logs/stats`),
};
