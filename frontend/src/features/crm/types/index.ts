/**
 * CRM, Communication & Helpdesk Management Types
 * Phase 035 - Enterprise CRM System
 */

// ===================== CONTACT TYPES =====================

export interface CrmContact {
  id: string;
  uuid: string;
  contact_no: string;
  full_name: string;
  photo?: string;
  contact_type: ContactType;
  mobile?: string;
  alternative_mobile?: string;
  email?: string;
  phone?: string;
  present_address?: string;
  permanent_address?: string;
  district?: string;
  division?: string;
  country?: string;
  organization?: string;
  designation?: string;
  student_id?: number;
  guardian_id?: number;
  employee_id?: number;
  social_links?: Record<string, string>;
  tags?: string[];
  notes?: string;
  status: 'active' | 'inactive' | 'blocked';
  created_at: string;
  updated_at: string;
}

export type ContactType =
  | 'prospective_student'
  | 'student'
  | 'guardian'
  | 'teacher'
  | 'staff'
  | 'vendor'
  | 'supplier'
  | 'alumni'
  | 'visitor'
  | 'organization';

// ===================== LEAD TYPES =====================

export interface CrmLead {
  id: string;
  uuid: string;
  lead_no: string;
  contact_id?: number;
  contact?: CrmContact;
  full_name: string;
  mobile?: string;
  email?: string;
  lead_source: LeadSource;
  course_interested?: string;
  session?: string;
  assigned_counselor_id?: number;
  assigned_counselor?: { id: number; name: string };
  priority: Priority;
  pipeline_stage: PipelineStage;
  lead_score: number;
  expected_admission_date?: string;
  notes?: string;
  last_discussion?: string;
  last_followup?: string;
  next_followup?: string;
  status: 'active' | 'converted' | 'rejected' | 'lost';
  converted_to_student_id?: number;
  converted_at?: string;
  created_at: string;
  updated_at: string;
}

export type LeadSource =
  | 'website'
  | 'facebook'
  | 'google'
  | 'whatsapp'
  | 'phone_call'
  | 'walkin'
  | 'referral'
  | 'campaign'
  | 'education_fair';

export type PipelineStage =
  | 'new'
  | 'contacted'
  | 'interested'
  | 'counseling'
  | 'application'
  | 'admission'
  | 'rejected'
  | 'lost';

export type Priority = 'low' | 'medium' | 'high' | 'urgent' | 'critical';

// ===================== TICKET TYPES =====================

export interface CrmTicket {
  id: string;
  uuid: string;
  ticket_no: string;
  subject: string;
  description?: string;
  category: TicketCategory;
  priority: Priority;
  status: TicketStatus;
  contact_id?: number;
  contact?: CrmContact;
  created_by: number;
  creator?: { id: number; name: string };
  assigned_to?: number;
  assignee?: { id: number; name: string };
  department_id?: number;
  department?: { id: number; name: string };
  cc?: string[];
  attachments?: string[];
  tags?: string[];
  first_response_at?: string;
  resolved_at?: string;
  closed_at?: string;
  closed_by?: number;
  resolution_notes?: string;
  response_count: number;
  resolution_time_hours?: number;
  created_at: string;
  updated_at: string;
}

export type TicketCategory =
  | 'admission'
  | 'accounts'
  | 'result'
  | 'attendance'
  | 'routine'
  | 'library'
  | 'hostel'
  | 'transport'
  | 'technical'
  | 'general';

export type TicketStatus =
  | 'open'
  | 'assigned'
  | 'in_progress'
  | 'waiting'
  | 'resolved'
  | 'closed'
  | 'cancelled';

export interface CrmTicketReply {
  id: string;
  uuid: string;
  ticket_id: number;
  user_id: number;
  user?: { id: number; name: string };
  message: string;
  attachments?: string[];
  is_internal: boolean;
  is_customer_reply: boolean;
  created_at: string;
}

// ===================== CAMPAIGN TYPES =====================

export interface CrmCampaign {
  id: string;
  uuid: string;
  campaign_no: string;
  name: string;
  description?: string;
  campaign_type: CampaignType;
  channel: CampaignChannel;
  status: CampaignStatus;
  created_by: number;
  creator?: { id: number; name: string };
  start_date?: string;
  end_date?: string;
  scheduled_at?: string;
  target_audience?: string;
  audience_filters?: Record<string, any>;
  template_data?: Record<string, any>;
  total_recipients: number;
  sent_count: number;
  delivered_count: number;
  opened_count: number;
  clicked_count: number;
  responded_count: number;
  converted_count: number;
  budget?: number;
  cost_per_send?: number;
  conversion_rate?: number;
  notes?: string;
  created_at: string;
  updated_at: string;
}

export type CampaignType =
  | 'admission'
  | 'marketing'
  | 'awareness'
  | 'event'
  | 'scholarship'
  | 'reengagement';

export type CampaignChannel = 'email' | 'sms' | 'whatsapp' | 'push' | 'multi';

export type CampaignStatus =
  | 'draft'
  | 'scheduled'
  | 'running'
  | 'paused'
  | 'completed'
  | 'cancelled';

// ===================== COMMUNICATION TYPES =====================

export interface CrmCommunication {
  id: string;
  uuid: string;
  communication_no: string;
  contact_id?: number;
  contact?: CrmContact;
  lead_id?: number;
  student_id?: number;
  campaign_id?: number;
  channel: 'email' | 'sms' | 'whatsapp' | 'push' | 'phone';
  direction: 'inbound' | 'outbound';
  type: 'transactional' | 'promotional' | 'notification' | 'reminder' | 'campaign' | 'autoresponse' | 'broadcast';
  subject?: string;
  content: string;
  recipient_name?: string;
  recipient_email?: string;
  recipient_mobile?: string;
  delivery_status: 'queued' | 'sending' | 'sent' | 'delivered' | 'read' | 'failed' | 'bounced' | 'undelivered';
  sent_at?: string;
  delivered_at?: string;
  read_at?: string;
  failure_reason?: string;
  sent_by?: number;
  sender?: { id: number; name: string };
  created_at: string;
}

// ===================== ANNOUNCEMENT TYPES =====================

export interface CrmAnnouncement {
  id: string;
  uuid: string;
  announcement_no: string;
  title: string;
  content: string;
  announcement_type: AnnouncementType;
  priority: Priority;
  status: 'draft' | 'published' | 'archived';
  created_by: number;
  creator?: { id: number; name: string };
  publish_date?: string;
  end_date?: string;
  is_pinned: boolean;
  show_on_website: boolean;
  show_on_portal: boolean;
  send_notification: boolean;
  target_audience?: string[];
  attachments?: string[];
  view_count: number;
  created_at: string;
  updated_at: string;
}

export type AnnouncementType =
  | 'general'
  | 'academic'
  | 'exam'
  | 'holiday'
  | 'event'
  | 'emergency'
  | 'administrative';

// ===================== FEEDBACK TYPES =====================

export interface CrmFeedback {
  id: string;
  uuid: string;
  feedback_no: string;
  contact_id?: number;
  contact?: CrmContact;
  student_id?: number;
  employee_id?: number;
  ticket_id?: number;
  feedback_type: FeedbackType;
  subject: string;
  content: string;
  rating?: number;
  metadata?: Record<string, any>;
  attachments?: string[];
  status: 'submitted' | 'reviewed' | 'in_progress' | 'resolved' | 'closed';
  assigned_to?: number;
  assignee?: { id: number; name: string };
  resolution?: string;
  resolved_by?: number;
  resolver?: { id: number; name: string };
  resolved_at?: string;
  created_at: string;
}

export type FeedbackType =
  | 'suggestion'
  | 'complaint'
  | 'compliment'
  | 'service_rating'
  | 'experience';

// ===================== SURVEY TYPES =====================

export interface CrmSurvey {
  id: string;
  uuid: string;
  survey_no: string;
  title: string;
  description?: string;
  survey_type: SurveyType;
  questions: SurveyQuestion[];
  status: 'draft' | 'active' | 'closed';
  created_by: number;
  creator?: { id: number; name: string };
  start_date?: string;
  end_date?: string;
  is_anonymous: boolean;
  allow_multiple: boolean;
  show_results: boolean;
  target_audience?: string[];
  total_responses: number;
  average_rating?: number;
  created_at: string;
}

export type SurveyType =
  | 'course'
  | 'teacher_evaluation'
  | 'campus_feedback'
  | 'service_quality'
  | 'custom';

export interface SurveyQuestion {
  id: string;
  type: 'multiple_choice' | 'rating' | 'text' | 'checkbox' | 'dropdown';
  question: string;
  options?: string[];
  required: boolean;
}

// ===================== CRM DASHBOARD STATS =====================

export interface CrmDashboardStats {
  contacts: {
    total: number;
    active: number;
    inactive: number;
    blocked: number;
  };
  leads: {
    total: number;
    active: number;
    converted: number;
    lost: number;
    today_new: number;
    followup_due: number;
  };
  tickets: {
    total: number;
    open: number;
    in_progress: number;
    waiting: number;
    resolved: number;
    closed: number;
  };
  campaigns: {
    total: number;
    draft: number;
    scheduled: number;
    running: number;
    completed: number;
  };
  today_inquiries: number;
  open_tickets: number;
  closed_tickets: number;
  pending_followups: number;
}

// ===================== ENUMS =====================

export const CONTACT_TYPES: Record<ContactType, string> = {
  prospective_student: 'Prospective Student',
  student: 'Student',
  guardian: 'Guardian',
  teacher: 'Teacher',
  staff: 'Staff',
  vendor: 'Vendor',
  supplier: 'Supplier',
  alumni: 'Alumni',
  visitor: 'Visitor',
  organization: 'Organization',
};

export const LEAD_SOURCES: Record<LeadSource, string> = {
  website: 'Website',
  facebook: 'Facebook',
  google: 'Google',
  whatsapp: 'WhatsApp',
  phone_call: 'Phone Call',
  walkin: 'Walk-in',
  referral: 'Referral',
  campaign: 'Campaign',
  education_fair: 'Education Fair',
};

export const PIPELINE_STAGES: Record<PipelineStage, string> = {
  new: 'New',
  contacted: 'Contacted',
  interested: 'Interested',
  counseling: 'Counseling',
  application: 'Application',
  admission: 'Admission',
  rejected: 'Rejected',
  lost: 'Lost',
};

export const TICKET_CATEGORIES: Record<TicketCategory, string> = {
  admission: 'Admission',
  accounts: 'Accounts',
  result: 'Result',
  attendance: 'Attendance',
  routine: 'Routine',
  library: 'Library',
  hostel: 'Hostel',
  transport: 'Transport',
  technical: 'Technical',
  general: 'General',
};

export const TICKET_STATUSES: Record<TicketStatus, string> = {
  open: 'Open',
  assigned: 'Assigned',
  in_progress: 'In Progress',
  waiting: 'Waiting',
  resolved: 'Resolved',
  closed: 'Closed',
  cancelled: 'Cancelled',
};

export const PRIORITIES: Record<Priority, string> = {
  low: 'Low',
  medium: 'Medium',
  high: 'High',
  urgent: 'Urgent',
  critical: 'Critical',
};

export const CAMPAIGN_TYPES: Record<CampaignType, string> = {
  admission: 'Admission Campaign',
  marketing: 'Marketing Campaign',
  awareness: 'Awareness Campaign',
  event: 'Event Campaign',
  scholarship: 'Scholarship Campaign',
  reengagement: 'Re-engagement Campaign',
};

export const CAMPAIGN_CHANNELS: Record<CampaignChannel, string> = {
  email: 'Email',
  sms: 'SMS',
  whatsapp: 'WhatsApp',
  push: 'Push Notification',
  multi: 'Multi-Channel',
};

export const ANNOUNCEMENT_TYPES: Record<AnnouncementType, string> = {
  general: 'General Notice',
  academic: 'Academic Notice',
  exam: 'Exam Notice',
  holiday: 'Holiday Notice',
  event: 'Event Notice',
  emergency: 'Emergency Notice',
  administrative: 'Administrative Notice',
};

export const FEEDBACK_TYPES: Record<FeedbackType, string> = {
  suggestion: 'Suggestion',
  complaint: 'Complaint',
  compliment: 'Compliment',
  service_rating: 'Service Rating',
  experience: 'Experience',
};

export const SURVEY_TYPES: Record<SurveyType, string> = {
  course: 'Course Survey',
  teacher_evaluation: 'Teacher Evaluation',
  campus_feedback: 'Campus Feedback',
  service_quality: 'Service Quality',
  custom: 'Custom Survey',
};
