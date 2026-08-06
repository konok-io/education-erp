/**
 * Transport Management Types
 */

export interface Vehicle {
  id: string;
  vehicle_number: string;
  registration_number: string;
  vehicle_type: VehicleType;
  vehicle_type_label?: string;
  brand?: string;
  model?: string;
  manufacture_year?: number;
  color?: string;
  engine_number?: string;
  chassis_number?: string;
  seat_capacity: number;
  purchase_date?: string;
  purchase_cost?: number;
  fuel_type: FuelType;
  fuel_type_label?: string;
  tank_capacity?: number;
  current_odometer?: string;
  status: VehicleStatus;
  status_label?: string;
  image?: string;
  description?: string;
  notes?: string;
  is_active: boolean;
  is_available?: boolean;
  current_insurance?: {
    policy_number: string;
    expiry_date: string;
  };
  maintenance_due?: boolean;
}

export interface Driver {
  id: string;
  driver_id: string;
  photo?: string;
  full_name: string;
  father_name?: string;
  mother_name?: string;
  date_of_birth?: string;
  gender?: string;
  blood_group?: string;
  phone?: string;
  mobile?: string;
  email?: string;
  present_address?: string;
  permanent_address?: string;
  nid?: string;
  license_number: string;
  license_type?: string;
  license_expiry?: string;
  license_expiring_soon?: boolean;
  license_expired?: boolean;
  joining_date?: string;
  emergency_contact?: string;
  emergency_phone?: string;
  status: DriverStatus;
  status_label?: string;
  notes?: string;
  is_active: boolean;
  is_available?: boolean;
}

export interface Route {
  id: string;
  route_code: string;
  route_name: string;
  starting_point: string;
  ending_point: string;
  distance?: number;
  estimated_time?: string;
  monthly_fee: number;
  description?: string;
  status: RouteStatus;
  is_active: boolean;
  stops?: RouteStop[];
  total_stops?: number;
  active_assignments?: number;
}

export interface RouteStop {
  id: string;
  stop_name: string;
  latitude?: number;
  longitude?: number;
  address?: string;
  arrival_time?: string;
  departure_time?: string;
  sequence: number;
  distance_from_school?: number;
  monthly_fee: number;
  is_active: boolean;
}

export interface TransportAssignment {
  id: string;
  assignment_no: string;
  assignable_type: string;
  assignable_id: number;
  route_id?: string;
  route?: { id: string; route_code: string; route_name: string };
  vehicle_id?: string;
  vehicle?: { id: string; vehicle_number: string };
  driver_id?: string;
  driver?: { id: string; full_name: string };
  pickup_stop_id?: string;
  drop_stop_id?: string;
  monthly_fee: number;
  effective_date: string;
  end_date?: string;
  status: AssignmentStatus;
  remarks?: string;
}

export interface Trip {
  id: string;
  trip_no: string;
  vehicle_id?: string;
  vehicle?: { id: string; vehicle_number: string; vehicle_type: string };
  driver_id?: string;
  driver?: { id: string; full_name: string; phone: string };
  route_id?: string;
  route?: { id: string; route_code: string; route_name: string };
  trip_type: TripType;
  trip_type_label?: string;
  trip_date: string;
  start_time?: string;
  end_time?: string;
  start_odometer?: string;
  end_odometer?: string;
  distance?: number;
  passenger_count: number;
  status: TripStatus;
  status_label?: string;
  remarks?: string;
  created_by?: { id: number; name: string };
}

export interface FuelRecord {
  id: string;
  fuel_no: string;
  vehicle_id?: string;
  vehicle?: { id: string; vehicle_number: string };
  fuel_date: string;
  fuel_type: FuelType;
  quantity: number;
  price_per_liter: number;
  total_cost: number;
  odometer_reading?: string;
  fuel_station?: string;
  invoice_no?: string;
  created_by?: string;
  remarks?: string;
}

export interface VehicleMaintenance {
  id: string;
  maintenance_no: string;
  vehicle_id?: string;
  vehicle?: { id: string; vehicle_number: string };
  maintenance_type: MaintenanceType;
  priority: MaintenancePriority;
  service_date: string;
  next_service_date?: string;
  vendor?: string;
  technician_name?: string;
  cost?: number;
  description?: string;
  work_done?: string;
  status: MaintenanceStatus;
  odometer?: string;
  created_by?: { id: number; name: string };
}

export interface VehicleInsurance {
  id: string;
  vehicle_id?: string;
  vehicle?: { id: string; vehicle_number: string };
  policy_number: string;
  insurance_type: InsuranceType;
  company_name?: string;
  start_date: string;
  expiry_date: string;
  premium_amount?: number;
  coverage_amount?: number;
  agent_name?: string;
  agent_phone?: string;
  document?: string;
  status: InsuranceStatus;
  remarks?: string;
}

export interface VehicleDocument {
  id: string;
  vehicle_id?: string;
  vehicle?: { id: string; vehicle_number: string };
  document_type: DocumentType;
  document_number?: string;
  issue_date?: string;
  expiry_date?: string;
  document_file?: string;
  status: DocumentStatus;
  remarks?: string;
}

export interface Accident {
  id: string;
  accident_no: string;
  vehicle_id?: string;
  vehicle?: { id: string; vehicle_number: string };
  driver_id?: string;
  driver?: { id: string; full_name: string };
  accident_date: string;
  accident_time?: string;
  location?: string;
  description?: string;
  police_station?: string;
  fir_number?: string;
  casualties: number;
  damage_cost?: number;
  insurance_claim?: number;
  claim_status: ClaimStatus;
  status: AccidentStatus;
  reported_by?: { id: number; name: string };
  remarks?: string;
}

export interface Incident {
  id: string;
  incident_no: string;
  vehicle_id?: string;
  vehicle?: { id: string; vehicle_number: string };
  driver_id?: string;
  driver?: { id: string; full_name: string };
  incident_date: string;
  incident_type: IncidentType;
  description?: string;
  status: IncidentStatus;
  reported_by?: { id: number; name: string };
  resolution?: string;
  resolved_at?: string;
  remarks?: string;
}

export interface TransportDashboard {
  total_vehicles: number;
  active_vehicles: number;
  inactive_vehicles: number;
  under_maintenance: number;
  total_drivers: number;
  active_drivers: number;
  total_routes: number;
  active_routes: number;
  today_trips: number;
  completed_trips: number;
  scheduled_trips: number;
  monthly_fuel_cost: number;
  maintenance_due: number;
  insurance_expiring: number;
  license_expiring: number;
  pending_incidents: number;
}

// Enums
export type VehicleType = 'bus' | 'mini_bus' | 'micro_bus' | 'van' | 'car' | 'pickup' | 'ambulance' | 'motorcycle';
export type VehicleStatus = 'active' | 'inactive' | 'maintenance' | 'reserved' | 'disposed' | 'accident';
export type FuelType = 'diesel' | 'petrol' | 'octane' | 'cng' | 'electric';
export type DriverStatus = 'active' | 'inactive' | 'on_leave' | 'suspended';
export type RouteStatus = 'active' | 'inactive';
export type AssignmentStatus = 'active' | 'inactive' | 'pending' | 'cancelled';
export type TripType = 'regular' | 'morning' | 'evening' | 'special' | 'exam' | 'holiday';
export type TripStatus = 'scheduled' | 'started' | 'in_progress' | 'completed' | 'cancelled';
export type MaintenanceType = 'routine' | 'engine' | 'oil_change' | 'tyre' | 'battery' | 'brake' | 'emergency';
export type MaintenancePriority = 'low' | 'normal' | 'high' | 'urgent';
export type MaintenanceStatus = 'scheduled' | 'in_progress' | 'completed' | 'cancelled';
export type InsuranceType = 'comprehensive' | 'third_party' | 'fire_theft';
export type InsuranceStatus = 'active' | 'expired' | 'renewed';
export type DocumentType = 'registration' | 'fitness' | 'tax_token' | 'insurance' | 'route_permit' | 'pollution' | 'other';
export type DocumentStatus = 'active' | 'expired' | 'renewed';
export type AccidentStatus = 'reported' | 'investigation' | 'settled' | 'closed';
export type ClaimStatus = 'none' | 'pending' | 'approved' | 'rejected' | 'settled';
export type IncidentType = 'breakdown' | 'late_arrival' | 'route_change' | 'complaint' | 'traffic' | 'weather' | 'other';
export type IncidentStatus = 'reported' | 'in_progress' | 'resolved' | 'closed';

// Constants
export const VEHICLE_TYPES: Record<VehicleType, string> = {
  bus: 'School Bus',
  mini_bus: 'Mini Bus',
  micro_bus: 'Micro Bus',
  van: 'Van',
  car: 'Car',
  pickup: 'Pickup',
  ambulance: 'Ambulance',
  motorcycle: 'Motorcycle',
};

export const VEHICLE_STATUSES: Record<VehicleStatus, string> = {
  active: 'Active',
  inactive: 'Inactive',
  maintenance: 'Under Maintenance',
  reserved: 'Reserved',
  disposed: 'Disposed',
  accident: 'Accident',
};

export const FUEL_TYPES: Record<FuelType, string> = {
  diesel: 'Diesel',
  petrol: 'Petrol',
  octane: 'Octane',
  cng: 'CNG',
  electric: 'Electric',
};

export const DRIVER_STATUSES: Record<DriverStatus, string> = {
  active: 'Active',
  inactive: 'Inactive',
  on_leave: 'On Leave',
  suspended: 'Suspended',
};

export const TRIP_TYPES: Record<TripType, string> = {
  regular: 'Regular',
  morning: 'Morning',
  evening: 'Evening',
  special: 'Special',
  exam: 'Exam',
  holiday: 'Holiday',
};

export const TRIP_STATUSES: Record<TripStatus, string> = {
  scheduled: 'Scheduled',
  started: 'Started',
  in_progress: 'In Progress',
  completed: 'Completed',
  cancelled: 'Cancelled',
};

export const MAINTENANCE_TYPES: Record<MaintenanceType, string> = {
  routine: 'Routine Service',
  engine: 'Engine Service',
  oil_change: 'Oil Change',
  tyre: 'Tyre Replacement',
  battery: 'Battery Replacement',
  brake: 'Brake Service',
  emergency: 'Emergency Repair',
};

export const MAINTENANCE_PRIORITIES: Record<MaintenancePriority, string> = {
  low: 'Low',
  normal: 'Normal',
  high: 'High',
  urgent: 'Urgent',
};

export const MAINTENANCE_STATUSES: Record<MaintenanceStatus, string> = {
  scheduled: 'Scheduled',
  in_progress: 'In Progress',
  completed: 'Completed',
  cancelled: 'Cancelled',
};

export const INCIDENT_TYPES: Record<IncidentType, string> = {
  breakdown: 'Breakdown',
  late_arrival: 'Late Arrival',
  route_change: 'Route Change',
  complaint: 'Passenger Complaint',
  traffic: 'Traffic Issue',
  weather: 'Weather Related',
  other: 'Other',
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
