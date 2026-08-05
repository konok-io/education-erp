/**
 * Hostel Management Types
 */

export interface Hostel {
  id: string;
  hostel_name: string;
  hostel_code: string;
  hostel_type: HostelType;
  hostel_type_label?: string;
  gender: Gender;
  campus?: string;
  manager_name?: string;
  phone?: string;
  email?: string;
  address?: string;
  total_buildings: number;
  total_rooms: number;
  total_beds: number;
  occupied_beds: number;
  available_beds?: number;
  occupancy_rate?: number;
  description?: string;
  notes?: string;
  status: HostelStatus;
  is_active: boolean;
  buildings?: Building[];
}

export interface Building {
  id: string;
  building_name: string;
  building_code: string;
  hostel_id?: string;
  hostel?: { id: string; hostel_name: string };
  campus?: string;
  address?: string;
  total_floors: number;
  total_rooms: number;
  total_beds: number;
  occupied_beds: number;
  available_beds?: number;
  description?: string;
  status: string;
  is_active: boolean;
  floors?: Floor[];
  rooms?: Room[];
}

export interface Floor {
  id: string;
  floor_number: number;
  floor_name?: string;
  building_id?: string;
  building?: { id: string; building_name: string };
  total_rooms: number;
  total_beds: number;
  occupied_beds: number;
  available_beds?: number;
  description?: string;
  status: string;
  is_active: boolean;
  rooms?: Room[];
}

export interface Room {
  id: string;
  room_number: string;
  room_code: string;
  room_type: RoomType;
  room_type_label?: string;
  hostel_id?: string;
  hostel?: { id: string; hostel_name: string };
  building_id?: string;
  building?: { id: string; building_name: string };
  floor_id?: string;
  floor?: { id: string; floor_number: number };
  floor_number?: number;
  capacity: number;
  occupied: number;
  available_beds?: number;
  is_available?: boolean;
  monthly_fee: number;
  security_deposit: number;
  location?: string;
  description?: string;
  status: RoomStatus;
  status_label?: string;
  is_active: boolean;
  beds?: Bed[];
}

export interface Bed {
  id: string;
  bed_number: string;
  bed_code: string;
  position?: BedPosition;
  position_label?: string;
  room_id?: string;
  room?: { id: string; room_number: string; room_code: string };
  status: BedStatus;
  status_label?: string;
  is_available?: boolean;
  allocation_date?: string;
  checkout_date?: string;
  notes?: string;
}

export interface HostelAllocation {
  id: string;
  allocation_no: string;
  allocatable_type: string;
  allocatable_id: number;
  hostel_id?: string;
  hostel?: { id: string; hostel_name: string };
  building_id?: string;
  building?: { id: string; building_name: string };
  room_id?: string;
  room?: { id: string; room_number: string; room_code: string };
  bed_id?: string;
  bed?: { id: string; bed_number: string; bed_code: string };
  check_in_date?: string;
  expected_checkout?: string;
  actual_checkout?: string;
  monthly_fee: number;
  security_deposit: number;
  total_paid: number;
  status: AllocationStatus;
  status_label?: string;
  remarks?: string;
  approved_by?: { id: number; name: string };
  approved_at?: string;
}

export interface HostelVisitor {
  id: string;
  visitor_no: string;
  visitor_name: string;
  nid?: string;
  phone?: string;
  relation?: string;
  purpose: VisitorPurpose;
  purpose_label?: string;
  hostel_id?: string;
  hostel?: { id: string; hostel_name: string };
  student_id?: number;
  student_name?: string;
  student_class?: string;
  student_roll?: string;
  visit_date: string;
  check_in_time?: string;
  check_out_time?: string;
  remarks?: string;
  status: VisitorStatus;
  status_label?: string;
  approved_by?: { id: number; name: string };
  approved_at?: string;
}

export interface GatePass {
  id: string;
  pass_no: string;
  passable_type: string;
  passable_id: number;
  pass_type: PassType;
  hostel_id?: string;
  hostel?: { id: string; hostel_name: string };
  issue_date: string;
  valid_from: string;
  valid_until: string;
  exit_time?: string;
  return_time?: string;
  destination?: string;
  reason?: string;
  guardian_name?: string;
  guardian_phone?: string;
  remarks?: string;
  status: PassStatus;
  issued_by?: { id: number; name: string };
  approved_by?: { id: number; name: string };
  approved_at?: string;
}

export interface HostelComplaint {
  id: string;
  complaint_no: string;
  hostel_id?: string;
  hostel?: { id: string; hostel_name: string };
  room_id?: string;
  room?: { id: string; room_number: string };
  student_id?: number;
  complaint_type: ComplaintType;
  priority: ComplaintPriority;
  description: string;
  response?: string;
  assigned_to?: string;
  response_date?: string;
  status: ComplaintStatus;
  reported_by?: { id: number; name: string };
  resolution?: string;
  resolved_date?: string;
  feedback?: string;
}

export interface HostelMaintenanceRequest {
  id: string;
  request_no: string;
  hostel_id?: string;
  hostel?: { id: string; hostel_name: string };
  room_id?: string;
  room?: { id: string; room_number: string };
  student_id?: number;
  request_type: MaintenanceType;
  priority: ComplaintPriority;
  description: string;
  status: MaintenanceStatus;
  estimated_cost?: number;
  actual_cost?: number;
  vendor?: string;
  scheduled_date?: string;
  completed_date?: string;
  work_done?: string;
  created_by?: { id: number; name: string };
  remarks?: string;
}

export interface HostelAttendance {
  id: string;
  hostel_id?: string;
  hostel?: { id: string; hostel_name: string };
  attendance_type: AttendanceType;
  attendance_date: string;
  student_name?: string;
  student_id_number?: string;
  bed_id?: string;
  check_in_time?: string;
  check_out_time?: string;
  status: AttendanceStatus;
  remarks?: string;
  recorded_by?: { id: number; name: string };
}

export interface HostelDashboard {
  total_hostels: number;
  active_hostels: number;
  total_buildings: number;
  total_rooms: number;
  total_beds: number;
  occupied_beds: number;
  available_beds: number;
  today_visitors: number;
  pending_complaints: number;
  pending_maintenance: number;
  today_check_ins: number;
  today_check_outs: number;
  pending_approvals: number;
}

// Enums
export type HostelType = 'boys' | 'girls' | 'teacher' | 'guest' | 'staff' | 'research';
export type HostelStatus = 'active' | 'inactive';
export type Gender = 'boys' | 'girls' | 'co-ed' | 'mixed';
export type RoomType = 'single' | 'double' | 'triple' | 'four_sharing' | 'dormitory' | 'vip' | 'guest';
export type RoomStatus = 'available' | 'partial' | 'full' | 'maintenance';
export type BedPosition = 'top_left' | 'top_right' | 'bottom_left' | 'bottom_right';
export type BedStatus = 'available' | 'occupied' | 'reserved' | 'maintenance' | 'blocked';
export type AllocationStatus = 'pending' | 'approved' | 'active' | 'checked_out' | 'cancelled';
export type VisitorPurpose = 'guardian' | 'parent' | 'relative' | 'official' | 'other';
export type VisitorStatus = 'pending' | 'approved' | 'checked_in' | 'checked_out' | 'cancelled';
export type PassType = 'leave' | 'temporary' | 'medical' | 'official' | 'emergency';
export type PassStatus = 'pending' | 'approved' | 'used' | 'expired' | 'cancelled';
export type ComplaintType = 'electricity' | 'water' | 'furniture' | 'internet' | 'cleaning' | 'security' | 'noise' | 'other';
export type ComplaintPriority = 'low' | 'normal' | 'high' | 'urgent';
export type ComplaintStatus = 'pending' | 'in_progress' | 'resolved' | 'closed';
export type MaintenanceType = 'electrical' | 'plumbing' | 'painting' | 'furniture' | 'cleaning' | 'internet' | 'ac' | 'other';
export type MaintenanceStatus = 'pending' | 'approved' | 'in_progress' | 'completed' | 'cancelled';
export type AttendanceType = 'morning' | 'night' | 'midday';
export type AttendanceStatus = 'present' | 'absent' | 'late' | 'leave' | 'early_leave';

// Constants
export const HOSTEL_TYPES: Record<HostelType, string> = {
  boys: 'Boys Hostel',
  girls: 'Girls Hostel',
  teacher: 'Teacher Hostel',
  guest: 'Guest House',
  staff: 'Staff Hostel',
  research: 'Research Hostel',
};

export const ROOM_TYPES: Record<RoomType, string> = {
  single: 'Single',
  double: 'Double',
  triple: 'Triple',
  four_sharing: 'Four Sharing',
  dormitory: 'Dormitory',
  vip: 'VIP Room',
  guest: 'Guest Room',
};

export const ROOM_STATUSES: Record<RoomStatus, string> = {
  available: 'Available',
  partial: 'Partially Occupied',
  full: 'Full',
  maintenance: 'Maintenance',
};

export const BED_STATUSES: Record<BedStatus, string> = {
  available: 'Available',
  occupied: 'Occupied',
  reserved: 'Reserved',
  maintenance: 'Maintenance',
  blocked: 'Blocked',
};

export const ALLOCATION_STATUSES: Record<AllocationStatus, string> = {
  pending: 'Pending',
  approved: 'Approved',
  active: 'Active',
  checked_out: 'Checked Out',
  cancelled: 'Cancelled',
};

export const VISITOR_STATUSES: Record<VisitorStatus, string> = {
  pending: 'Pending',
  approved: 'Approved',
  checked_in: 'Checked In',
  checked_out: 'Checked Out',
  cancelled: 'Cancelled',
};

export const COMPLAINT_TYPES: Record<ComplaintType, string> = {
  electricity: 'Electricity',
  water: 'Water',
  furniture: 'Furniture',
  internet: 'Internet',
  cleaning: 'Cleaning',
  security: 'Security',
  noise: 'Noise',
  other: 'Other',
};

export const COMPLAINT_PRIORITIES: Record<ComplaintPriority, string> = {
  low: 'Low',
  normal: 'Normal',
  high: 'High',
  urgent: 'Urgent',
};

export const COMPLAINT_STATUSES: Record<ComplaintStatus, string> = {
  pending: 'Pending',
  in_progress: 'In Progress',
  resolved: 'Resolved',
  closed: 'Closed',
};

export const MAINTENANCE_TYPES: Record<MaintenanceType, string> = {
  electrical: 'Electrical',
  plumbing: 'Plumbing',
  painting: 'Painting',
  furniture: 'Furniture Repair',
  cleaning: 'Cleaning',
  internet: 'Internet',
  ac: 'Air Conditioner',
  other: 'Other',
};

export const ATTENDANCE_STATUSES: Record<AttendanceStatus, string> = {
  present: 'Present',
  absent: 'Absent',
  late: 'Late',
  leave: 'On Leave',
  early_leave: 'Early Leave',
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
