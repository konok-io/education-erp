/**
 * Research Types
 */

export interface ResearchProject {
  id: string;
  project_code: string;
  project_title: string;
  abstract?: string;
  objectives?: string;
  expected_outcome?: string;
  category?: string;
  research_type: ResearchType;
  research_type_label?: string;
  department?: string;
  keywords?: string[];
  start_date?: string;
  end_date?: string;
  status: ProjectStatus;
  status_label?: string;
  priority: ProjectPriority;
  priority_label?: string;
  budget?: number;
  budget_currency?: string;
  ethics_approval?: string;
  principal_investigator?: {
    id: string;
    name: string;
    email: string;
  };
  teams?: ResearchTeam[];
  milestones?: ResearchMilestone[];
  progress_percentage: number;
  is_featured: boolean;
  is_public: boolean;
  approved_at?: string;
  created_at: string;
  updated_at: string;
}

export interface ResearchTeam {
  id: string;
  member_name: string;
  member_email: string;
  designation?: string;
  department?: string;
  institution?: string;
  role: TeamRole;
  role_label?: string;
  responsibilities?: string;
  start_date?: string;
  end_date?: string;
  is_active: boolean;
  notes?: string;
}

export interface ResearchMilestone {
  id: string;
  milestone_name: string;
  description?: string;
  order: number;
  start_date?: string;
  end_date?: string;
  actual_completion_date?: string;
  status: MilestoneStatus;
  status_label?: string;
  progress_percentage: number;
  deliverables?: string;
  notes?: string;
}

export interface ResearchGrant {
  id: string;
  grant_number: string;
  grant_title: string;
  description?: string;
  grant_amount: number;
  currency?: string;
  application_date?: string;
  approval_date?: string;
  start_date?: string;
  end_date?: string;
  status: GrantStatus;
  status_label?: string;
  budget_breakdown?: Record<string, number>;
  released_amount: number;
  remaining_amount: number;
  terms_conditions?: string;
  agreement_document?: string;
  funding_agency?: FundingAgency;
  project?: {
    id: string;
    project_code: string;
    project_title: string;
  };
  created_at: string;
  updated_at: string;
}

export interface FundingAgency {
  id: string;
  agency_code: string;
  agency_name: string;
  agency_type: AgencyType;
  description?: string;
  website?: string;
  contact_person?: string;
  email?: string;
  phone?: string;
  address?: string;
  country?: string;
  is_active: boolean;
}

export interface Publication {
  id: string;
  publication_code: string;
  title: string;
  abstract?: string;
  publication_type: PublicationType;
  publication_type_label?: string;
  journal_name?: string;
  journal_issn?: string;
  publisher?: string;
  volume?: string;
  issue?: string;
  pages?: string;
  doi?: string;
  url?: string;
  publication_year: number;
  publication_date?: string;
  authors?: string[];
  keywords?: string[];
  co_authors?: string[];
  orcid?: string;
  citation_count: number;
  impact_factor?: number;
  quartile?: string;
  status: PublicationStatus;
  status_label?: string;
  conference_name?: string;
  conference_venue?: string;
  conference_date?: string;
  isbn?: string;
  book_publisher?: string;
  pdf_document?: string;
  is_open_access: boolean;
  is_peer_reviewed: boolean;
  created_at: string;
  updated_at: string;
}

export interface Patent {
  id: string;
  patent_number: string;
  patent_title: string;
  abstract?: string;
  patent_type: PatentType;
  status: PatentStatus;
  status_label?: string;
  country?: string;
  application_date?: string;
  publication_date?: string;
  grant_date?: string;
  expiry_date?: string;
  inventors?: string[];
  applicant?: string;
  assignee?: string;
  application_number?: string;
  publication_number?: string;
  ip_office?: string;
  claims?: string;
  patent_document?: string;
  cost?: number;
  cost_currency?: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Thesis {
  id: string;
  thesis_number: string;
  thesis_title: string;
  abstract?: string;
  thesis_type: ThesisType;
  student_id?: number;
  student_name: string;
  student_email?: string;
  student_roll?: string;
  department?: string;
  program?: string;
  supervisor?: string;
  co_supervisor?: string;
  degree?: string;
  submission_year: number;
  submission_date?: string;
  defense_date?: string;
  defense_score?: number;
  grade?: string;
  committee_members?: string[];
  keywords?: string[];
  status: ThesisStatus;
  status_label?: string;
  pdf_document?: string;
  doi?: string;
  created_at: string;
  updated_at: string;
}

export interface Innovation {
  id: string;
  innovation_code: string;
  title: string;
  description?: string;
  innovation_type?: InnovationType;
  stage: InnovationStage;
  status: InnovationStatus;
  technology_details?: string;
  market_potential?: string;
  has_patent: boolean;
  patent_number?: string;
  prototype_url?: string;
  demo_video?: string;
  team_members?: string[];
  funding_required?: number;
  thumbnail?: string;
  created_at: string;
  updated_at: string;
}

export interface ResearchRepository {
  id: string;
  document_code: string;
  title: string;
  description?: string;
  document_type: RepositoryDocumentType;
  file_path?: string;
  file_name?: string;
  file_type?: string;
  file_size?: number;
  access_type: RepositoryAccessType;
  license?: string;
  doi?: string;
  is_featured: boolean;
  is_active: boolean;
  contributor?: string;
  published_date?: string;
  created_at: string;
  updated_at: string;
}

export interface ResearchDashboard {
  total_projects: number;
  active_projects: number;
  completed_projects: number;
  total_grants: number;
  active_grants: number;
  total_funding: number;
  total_publications: number;
  published_publications: number;
  total_citations: number;
  total_patents: number;
  granted_patents: number;
  total_theses: number;
  total_innovations: number;
  repository_items: number;
  research_students: number;
}

// Enums
export type ResearchType = 'faculty' | 'student' | 'collaborative' | 'government' | 'industry' | 'international' | 'innovation';
export type ProjectStatus = 'draft' | 'pending' | 'department_review' | 'committee_review' | 'ethics_review' | 'approved' | 'active' | 'completed' | 'terminated';
export type ProjectPriority = 'low' | 'normal' | 'high' | 'urgent';
export type TeamRole = 'principal_investigator' | 'co_investigator' | 'researcher' | 'research_assistant' | 'student' | 'external_member';
export type MilestoneStatus = 'pending' | 'in_progress' | 'on_hold' | 'completed' | 'overdue';
export type GrantStatus = 'pending' | 'approved' | 'rejected' | 'active' | 'completed' | 'terminated';
export type AgencyType = 'government' | 'private' | 'university' | 'ngo' | 'international' | 'industry';
export type PublicationType = 'journal_article' | 'conference_paper' | 'book' | 'book_chapter' | 'magazine' | 'technical_report' | 'working_paper';
export type PublicationStatus = 'draft' | 'submitted' | 'under_review' | 'revised' | 'accepted' | 'published' | 'rejected';
export type PatentType = 'invention' | 'utility' | 'design' | 'plant';
export type PatentStatus = 'pending' | 'examined' | 'published' | 'granted' | 'rejected' | 'lapsed';
export type ThesisType = 'thesis' | 'dissertation' | 'project_report';
export type ThesisStatus = 'draft' | 'submitted' | 'under_review' | 'defense_scheduled' | 'defended' | 'approved' | 'rejected' | 'archived';
export type InnovationType = 'product' | 'process' | 'service' | 'software' | 'technology';
export type InnovationStage = 'idea' | 'research' | 'development' | 'prototype' | 'beta' | 'launch';
export type InnovationStatus = 'idea' | 'in_development' | 'prototype' | 'testing' | 'launched' | 'commercialized';
export type RepositoryDocumentType = 'pdf' | 'dataset' | 'image' | 'source_code' | 'presentation' | 'poster' | 'supplementary';
export type RepositoryAccessType = 'public' | 'private' | 'institution' | 'team_only';

// Constants
export const RESEARCH_TYPES: Record<ResearchType, string> = {
  faculty: 'Faculty Research',
  student: 'Student Research',
  collaborative: 'Collaborative Research',
  government: 'Government Project',
  industry: 'Industry Project',
  international: 'International Research',
  innovation: 'Innovation Project',
};

export const PROJECT_STATUSES: Record<ProjectStatus, string> = {
  draft: 'Draft',
  pending: 'Pending',
  department_review: 'Department Review',
  committee_review: 'Committee Review',
  ethics_review: 'Ethics Review',
  approved: 'Approved',
  active: 'Active',
  completed: 'Completed',
  terminated: 'Terminated',
};

export const PUBLICATION_TYPES: Record<PublicationType, string> = {
  journal_article: 'Journal Article',
  conference_paper: 'Conference Paper',
  book: 'Book',
  book_chapter: 'Book Chapter',
  magazine: 'Magazine Article',
  technical_report: 'Technical Report',
  working_paper: 'Working Paper',
};

export const PATENT_STATUSES: Record<PatentStatus, string> = {
  pending: 'Pending',
  examined: 'Examined',
  published: 'Published',
  granted: 'Granted',
  rejected: 'Rejected',
  lapsed: 'Lapsed',
};

export const TEAM_ROLES: Record<TeamRole, string> = {
  principal_investigator: 'Principal Investigator',
  co_investigator: 'Co-Investigator',
  researcher: 'Researcher',
  research_assistant: 'Research Assistant',
  student: 'Student Researcher',
  external_member: 'External Member',
};

// Paginated Response
export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
}
