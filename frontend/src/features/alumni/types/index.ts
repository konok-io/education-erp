/**
 * Alumni Types
 */

export interface AlumniProfile {
  id: string;
  membership_number: string;
  student_id?: number;
  registration_number?: string;
  full_name: string;
  email: string;
  phone?: string;
  photo?: string;
  passing_year: number;
  department?: string;
  program?: string;
  current_occupation?: string;
  current_organization?: string;
  designation?: string;
  country?: string;
  city?: string;
  address?: string;
  linkedin?: string;
  twitter?: string;
  facebook?: string;
  website?: string;
  bio?: string;
  skills?: string[];
  education?: Education[];
  experience?: Experience[];
  achievements?: string[];
  employment_status: EmploymentStatus;
  employment_status_label?: string;
  current_salary?: number;
  salary_currency?: string;
  membership_type: MembershipType;
  membership_type_label?: string;
  membership_start_date?: string;
  membership_end_date?: string;
  is_verified: boolean;
  is_active: boolean;
  status: AlumniStatus;
  status_label?: string;
  verified_at?: string;
  created_at: string;
  updated_at: string;
}

export interface Employer {
  id: string;
  company_name: string;
  company_code: string;
  industry: string;
  description?: string;
  website?: string;
  logo?: string;
  contact_person: string;
  contact_designation?: string;
  email: string;
  phone?: string;
  country?: string;
  city?: string;
  address?: string;
  company_size?: string;
  company_type?: string;
  founded_year?: string;
  social_links?: Record<string, string>;
  is_verified: boolean;
  is_featured: boolean;
  status: EmployerStatus;
  verified_at?: string;
  created_at: string;
  updated_at: string;
}

export interface Job {
  id: string;
  job_number: string;
  employer?: { id: string; company_name: string; logo?: string; industry: string; website?: string };
  job_title: string;
  description?: string;
  job_type: JobType;
  job_type_label?: string;
  department?: string;
  designation?: string;
  location?: string;
  country?: string;
  city?: string;
  work_type: WorkType;
  work_type_label?: string;
  vacancy: number;
  requirements?: string;
  responsibilities?: string;
  benefits?: string;
  experience_required?: string;
  education_required?: string;
  min_salary?: number;
  max_salary?: number;
  salary_currency?: string;
  salary_frequency?: string;
  application_deadline?: string;
  start_date?: string;
  is_featured: boolean;
  is_active: boolean;
  status: JobStatus;
  status_label?: string;
  published_at?: string;
  created_at: string;
  updated_at: string;
}

export interface JobApplication {
  id: string;
  job_id: string;
  alumni_profile_id?: number;
  student_id?: number;
  applicant_name: string;
  email: string;
  phone?: string;
  resume?: string;
  cover_letter?: string;
  portfolio_link?: string;
  linkedin?: string;
  experience_summary?: string;
  skills?: string[];
  expected_salary?: string;
  current_company?: string;
  current_designation?: string;
  status: ApplicationStatus;
  employer_notes?: string;
  rejection_reason?: string;
  reviewed_at?: string;
  created_at: string;
  updated_at: string;
}

export interface Internship {
  id: string;
  internship_number: string;
  employer?: { id: string; company_name: string; logo?: string };
  internship_title: string;
  description?: string;
  internship_type: InternshipType;
  department?: string;
  location?: string;
  country?: string;
  positions: number;
  requirements?: string;
  responsibilities?: string;
  duration?: string;
  start_date?: string;
  end_date?: string;
  stipend?: number;
  stipend_currency?: string;
  is_paid: boolean;
  is_remote: boolean;
  is_active: boolean;
  status: InternshipStatus;
  published_at?: string;
  created_at: string;
  updated_at: string;
}

export interface Placement {
  id: string;
  placement_number: string;
  employer?: { id: string; company_name: string };
  job_id?: string;
  alumni_profile_id?: number;
  student_id?: number;
  student_name: string;
  student_email: string;
  company_name: string;
  designation: string;
  department?: string;
  location?: string;
  salary?: number;
  salary_currency?: string;
  employment_type?: string;
  joining_date?: string;
  offer_letter?: string;
  status: PlacementStatus;
  remarks?: string;
  created_at: string;
  updated_at: string;
}

export interface AlumniEvent {
  id: string;
  event_number: string;
  event_title: string;
  description?: string;
  event_type: EventType;
  event_type_label?: string;
  banner_image?: string;
  event_date: string;
  start_time?: string;
  end_time?: string;
  venue?: string;
  city?: string;
  country?: string;
  address?: string;
  agenda?: string;
  speakers?: string;
  max_participants?: number;
  registered_count: number;
  registration_fee?: number;
  is_free: boolean;
  is_virtual: boolean;
  meeting_link?: string;
  is_featured: boolean;
  is_active: boolean;
  status: EventStatus;
  status_label?: string;
  published_at?: string;
  created_at: string;
  updated_at: string;
}

export interface EventRegistration {
  id: string;
  event_id: string;
  alumni_profile_id?: number;
  student_id?: number;
  registrant_name: string;
  email: string;
  phone?: string;
  ticket_type?: string;
  amount_paid?: number;
  payment_status: PaymentStatus;
  transaction_id?: string;
  attended: boolean;
  certificate_generated: boolean;
  certificate_number?: string;
  feedback?: string;
  status: RegistrationStatus;
  created_at: string;
  updated_at: string;
}

export interface Mentorship {
  id: string;
  mentorship_number: string;
  mentor_id?: number;
  mentee_id?: number;
  student_id?: number;
  mentee_name: string;
  mentee_email: string;
  mentee_phone?: string;
  expertise_area?: string;
  goals?: string;
  background?: string;
  start_date?: string;
  end_date?: string;
  meeting_frequency?: string;
  status: MentorshipStatus;
  notes?: string;
  feedback?: string;
  sessions_completed: number;
  created_at: string;
  updated_at: string;
}

export interface Donation {
  id: string;
  donation_number: string;
  campaign_id?: string;
  alumni_profile_id?: number;
  donor_id?: number;
  donor_name: string;
  donor_email: string;
  donor_phone?: string;
  donor_type: string;
  company_name?: string;
  amount: number;
  currency?: string;
  payment_method?: string;
  transaction_id?: string;
  payment_status: DonationPaymentStatus;
  donation_type: DonationType;
  fund_category?: string;
  notes?: string;
  is_anonymous: boolean;
  is_tax_deductible: boolean;
  receipt_path?: string;
  donated_at?: string;
  created_at: string;
  updated_at: string;
}

export interface FundraisingCampaign {
  id: string;
  campaign_code: string;
  campaign_title: string;
  description?: string;
  banner_image?: string;
  goal_amount: number;
  raised_amount: number;
  currency?: string;
  fund_category?: string;
  start_date?: string;
  end_date?: string;
  donor_count: number;
  is_featured: boolean;
  is_active: boolean;
  status: CampaignStatus;
  created_at: string;
  updated_at: string;
}

export interface AlumniDashboard {
  total_alumni: number;
  verified_alumni: number;
  active_members: number;
  total_employers: number;
  verified_employers: number;
  total_jobs: number;
  open_jobs: number;
  total_internships: number;
  open_internships: number;
  total_placements: number;
  total_events: number;
  upcoming_events: number;
  total_donations: number;
  total_campaigns: number;
  active_campaigns: number;
}

export interface Education {
  degree: string;
  institution: string;
  year: string;
  percentage?: string;
}

export interface Experience {
  company: string;
  designation: string;
  start_date: string;
  end_date?: string;
  is_current: boolean;
}

// Enums
export type MembershipType = 'lifetime' | 'annual' | 'premium' | 'honorary' | 'corporate';
export type AlumniStatus = 'active' | 'inactive' | 'suspended';
export type EmploymentStatus = 'employed' | 'self_employed' | 'unemployed' | 'student' | 'retired';
export type EmployerStatus = 'active' | 'inactive';
export type JobType = 'full_time' | 'part_time' | 'contract' | 'internship' | 'remote' | 'government' | 'private';
export type WorkType = 'on_site' | 'remote' | 'hybrid';
export type JobStatus = 'open' | 'closed' | 'filled' | 'draft';
export type ApplicationStatus = 'applied' | 'shortlisted' | 'interview' | 'offered' | 'accepted' | 'rejected';
export type InternshipType = 'paid' | 'unpaid' | 'research' | 'industrial' | 'teaching';
export type InternshipStatus = 'open' | 'closed' | 'filled';
export type PlacementStatus = 'offer_extended' | 'offer_accepted' | 'offer_declined' | 'joined' | 'probation' | 'confirmed' | 'left';
export type EventType = 'reunion' | 'seminar' | 'workshop' | 'conference' | 'networking' | 'webinar';
export type EventStatus = 'draft' | 'published' | 'ongoing' | 'completed' | 'cancelled';
export type RegistrationStatus = 'registered' | 'confirmed' | 'cancelled' | 'no_show';
export type PaymentStatus = 'pending' | 'completed' | 'refunded';
export type MentorshipStatus = 'active' | 'paused' | 'completed' | 'cancelled';
export type DonationPaymentStatus = 'pending' | 'completed' | 'failed' | 'refunded';
export type DonationType = 'one_time' | 'monthly' | 'annual';
export type CampaignStatus = 'active' | 'completed' | 'cancelled';

// Constants
export const MEMBERSHIP_TYPES: Record<MembershipType, string> = {
  lifetime: 'Lifetime',
  annual: 'Annual',
  premium: 'Premium',
  honorary: 'Honorary',
  corporate: 'Corporate',
};

export const EMPLOYMENT_STATUSES: Record<EmploymentStatus, string> = {
  employed: 'Employed',
  self_employed: 'Self Employed',
  unemployed: 'Unemployed',
  student: 'Student',
  retired: 'Retired',
};

export const JOB_TYPES: Record<JobType, string> = {
  full_time: 'Full Time',
  part_time: 'Part Time',
  contract: 'Contract',
  internship: 'Internship',
  remote: 'Remote',
  government: 'Government',
  private: 'Private',
};

export const WORK_TYPES: Record<WorkType, string> = {
  on_site: 'On Site',
  remote: 'Remote',
  hybrid: 'Hybrid',
};

export const EVENT_TYPES: Record<EventType, string> = {
  reunion: 'Reunion',
  seminar: 'Seminar',
  workshop: 'Workshop',
  conference: 'Conference',
  networking: 'Networking Event',
  webinar: 'Webinar',
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
